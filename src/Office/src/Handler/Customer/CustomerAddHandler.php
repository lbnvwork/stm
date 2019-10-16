<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 08.05.19
 * Time: 11:56
 */

namespace Office\Handler\Customer;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Auth\Service\SendMail;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Office\Entity\LkCompany;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class CustomerAddHandler
 * Добавляет клиента в список клиентов (если клиента нет в БД, создает нового)
 *
 * @package Office\Handler\Customer
 */
class CustomerAddHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/company/list';

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $auth;

    private $sendMail;

    private $email = '';

    private const FIELDS = [
//        'lastName'   => ['title' => 'Фамилия'],
//        'firstName'  => ['title' => 'Имя'],
//        'middleName' => ['title' => 'Отчество'],
        'email' => ['title' => 'Email'],
//        'phone'      => ['title' => 'Телефон'],
    ];

    /**
     * CustomerAddHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param OfficeDataPrepareService $dataPrepareService
     * @param UserRepositoryInterface $auth
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        OfficeDataPrepareService $dataPrepareService,
        UserRepositoryInterface $auth,
        SendMail $sendMail
    ) {
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->auth = $auth;
        $this->dataPrepareService = $dataPrepareService;
        $this->sendMail = $sendMail;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //begin сбор данных
        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $params = $request->getParsedBody();
        $email = $params['email'];

        /** @var LkCompany $company */
        $company = $this->entityManager->getRepository(LkCompany::class)->findOneBy(['id' => (int)$params['company']]);
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        //end сбор данных

        //begin проверка данных
        if (!($company instanceof LkCompany)
            || !$this->dataPrepareService->checkForUser($user, $company)
        ) {
            $flashMessage->addErrorMessage('Ошибка при добавлении компании!');
            return new Response\RedirectResponse($this->urlHelper->generate('office.customer.new'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $flashMessage->addErrorMessage('Email введен неверно!');
            return new Response\RedirectResponse($this->urlHelper->generate('office.customer.new'));
        }
        //end проверка данных

        //begin обработка данных
        $customer = $this->entityManager->getRepository(LkCompany::class)->getUserByEmail($email);
        if ($customer == null) {
            $allowedParams = array_intersect_key($params, self::FIELDS);
            $allowedParams['ctoUser'] = $user;
            $allowedParams['company'] = $company;
            $newCustomer = $this->auth->register($allowedParams);
            $flashMessage->addSuccessMessage('Новый пользователь с email '.$email.' успешно добавлен!');
        } else {
            /** @var User $customer */
            $customer = $customer[0];
            if (!array_key_exists('office_user', is_array($customer->getRoles()) ? $customer->getRoles() : [])) {
                $flashMessage->addErrorMessage('Нет прав для добавления данного пользователя!');
                return new Response\RedirectResponse($this->urlHelper->generate('office.customer.new'));
            }
            $data = $this->auth->getUsersWithOrderBuilder([]);
            /** @var QueryBuilder $qb */
            $qb = $data['qb'];
            $customers = $qb->innerJoin('u.customer', 'cus')
                ->andWhere('cus.id = :user')->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            if (in_array($customer, $customers)) {
                $flashMessage->addErrorMessage('Пользователь уже был добавлен!');
                return new Response\RedirectResponse($this->urlHelper->generate('office.customer.new'));
            }

            $customer->setCtoUser($user);
            if (!in_array($company, $customer->getCompany()->toArray())) {
                $customer->addCompany($company);
            }
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
            $flashMessage->addSuccessMessage('Новый пользователь с email '.$email.' успешно добавлен!');
            $this->sendMail->sendNewRegister($customer);
        }
        //end обработка данных
        return new Response\RedirectResponse($this->urlHelper->generate('office.customer.list'));
    }
}
