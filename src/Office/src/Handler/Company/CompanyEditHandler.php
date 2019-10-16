<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 9:48
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
use Zend\Diactoros\Response;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CompanyEditHandler
 * Редактирует компанию
 *
 * @package Office\Handler\Company
 */
class CompanyEditHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/company/item';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    /**
     * CompanyEditHandler constructor.
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
        $id = $request->getAttribute('id');
        $company = null;
        /** @var LkCompany $company */
        $company = $this->entityManager->getRepository(LkCompany::class)->findOneBy(['id' => $id]);
        $params = $request->getParsedBody();
        //$params['taxationTypeId'] = $params['taxationTypeId'] ?? null;
        //end сбор данных

        //begin проверка данных
        if (!$this->dataPrepareService->checkForUser($user, $company)) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };
        $validator = $this->validator->check($request);
        $params = $validator->setNullParams($params);
        if (!$validator->isValid()) {
            foreach ($validator->getMessages()->getAllErrorMessagesArr() as $errorMessage) {
                $flashMessage->addErrorMessage($errorMessage);
            }
            return new Response\RedirectResponse($this->urlHelper->generate('office.company.item', ['id' => $company->getId()]));
        }
        //end проверка данных

        //begin обработка данных
        $persistArr = $this->dataPrepareService->getPersistArr($params);
        $company = $this->entityManager->getRepository(LkCompany::class)->setEntityData($params, $company, $persistArr);
        $this->entityManager->flush();
        $flashMessage->addSuccessMessage('Данные сохранены');
        //end обработка данных

        return new Response\RedirectResponse($this->urlHelper->generate('office.company.item', ['id' => $company->getId()]));
    }
}
