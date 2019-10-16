<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 9:46
 */

namespace Office\Handler\Company;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Service\DataPrepare\CompanyDataPrepareService;
use Office\Service\Validator\CompanyValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class CompanyAddHandler
 * Добавляет компанию
 *
 * @package Office\Handler\Company
 */
class CompanyAddHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/company/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    /**
     * CompanyAddHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param CompanyDataPrepareService $dataPrepareService
     * @param CompanyValidatorService $validator
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        CompanyDataPrepareService $dataPrepareService,
        CompanyValidatorService $validator
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
        $user = $request->getAttribute(UserInterface::class);
        $company = new LkCompany();
        $company->addUser($user);
        $params = $request->getParsedBody();
        //$params['taxationTypeId'] = $params['taxationTypeId'] ?? null;
        //end сбор данных

        //begin проверка данных
        $validator = $this->validator->check($request);
        $params = $validator->setNullParams($params);
        if (!$validator->isValid()) {
            $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
            return new Response\JsonResponse(['error' => $allErrorMessages]);
        }
        //end проверка данных

        //begin обработка данных
        $persistArr = $this->dataPrepareService->getPersistArr($params);
        $company = $this->entityManager->getRepository(LkCompany::class)->setEntityData($params, $company, $persistArr);
        $user->addCompany($company);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $flashMessage->addSuccessMessage('Данные сохранены');
        //end обработка данных

        return new Response\JsonResponse(['url' => $this->urlHelper->generate('office.company.list')]);
    }
}
