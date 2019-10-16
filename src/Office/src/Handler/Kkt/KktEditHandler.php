<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:37
 */

namespace Office\Handler\Kkt;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Entity\LkKkt;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Office\Service\Validator\KktValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class KktEditHandler
 * Редактирует кассу
 *
 * @package Office\Handler\Kkt
 */
class KktEditHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/kkt/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    /**
     * KktEditHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param OfficeDataPrepareService $dataPrepareService
     * @param KktValidatorService $validator
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        OfficeDataPrepareService $dataPrepareService,
        KktValidatorService $validator
    ) {
        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->dataPrepareService = $dataPrepareService;
        $this->validator = $validator;
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
        $flashMessage = $request->getAttribute(FlashMessage::class);
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        /** @var LkKkt $kkt */
        $kkt = $this->entityManager->getRepository(LkKkt::class)->findOneBy(['id' => $id]);
        $params = $request->getParsedBody();
        //end сбор данных

        //begin проверка данных
        if (!isset($kkt) || !$this->dataPrepareService->checkForUser($user, $kkt->getCompany())) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };
        $validator = $this->validator->check($request);
        $params = $validator->setNullParams($params);
        if (!$validator->isValid()) {
            foreach ($validator->getMessages()->getAllErrorMessagesArr() as $errorMessage) {
                $flashMessage->addErrorMessage($errorMessage);
            }
            return new Response\RedirectResponse($this->urlHelper->generate('office.kkt.item', ['id' => $kkt->getId()]));
        }
        if ($kkt->getSeria() != $params['seria'] || $kkt->getMachineNumber() != $params['machineNumber']) {
            $res = $this->entityManager->getRepository(LkKkt::class)->checkKktDuplication((int)$params['seria'], substr($params['machineNumber'], 0, 7));
            if ($params['seria'] == null || $params['machineNumber'] == null || $res != null) {
                $flashMessage->addErrorMessage('ККТ с таким заводским номером и серией уже зарегистрирована');
                return new Response\RedirectResponse($this->urlHelper->generate('office.kkt.item', ['id' => $kkt->getId()]));
            }
        }

        if (!$this->dataPrepareService->checkForUser($user, $this->entityManager->find(LkCompany::class, $params['company']))) {
            $flashMessage->addErrorMessage('Компания не найдена!');
            return new Response\RedirectResponse($this->urlHelper->generate('office.kkt.item', ['id' => $kkt->getId()]));
        };
        //end проверка данных

        $persistArr = $this->dataPrepareService->getPersistArr($params);
        /** @var LkKkt $kkt */
        $this->entityManager->getRepository(LkKkt::class)->setEntityData($params, $kkt, $persistArr);
        $this->entityManager->persist($kkt);
        $this->entityManager->flush();
        $flashMessage->addSuccessMessage('Данные успешно изменены');
        return new Response\RedirectResponse($this->urlHelper->generate('office.kkt.item', ['id' => $kkt->getId()]));
    }
}
