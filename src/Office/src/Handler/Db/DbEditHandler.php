<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 10:58
 */

namespace Office\Handler\Db;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Office\Service\Validator\DbValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DbEditHandler
 * Редактирует базу товаров
 *
 * @package Office\Handler\Db
 */
class DbEditHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/db/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    /**
     * DbEditHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param OfficeDataPrepareService $dataPrepareService
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        OfficeDataPrepareService $dataPrepareService,
        DbValidatorService $validator
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
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        $db = null;
        /** @var LkDb $db */
        $db = $this->entityManager->getRepository(LkDb::class)->findOneBy(['id' => $id]);
        if (!$this->dataPrepareService->checkForUser($user, $db)) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };
        $params = $request->getParsedBody();
        $products = $this->entityManager->getRepository(LkProduct::class)->findBy(['db' => $db]);

        if ($params['maxCount'] && count($products) > $params['maxCount']) {
            return new Response\JsonResponse(['error' => ['Данные не изменены! Товаров в базе больше максимального количества']]);
        }
        $validator = $this->validator->check($request);
        $params = $validator->setNullParams($params);
        if (!$validator->isValid()) {
            $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
            return new Response\JsonResponse(['error' => $allErrorMessages]);
        }
        $this->entityManager->getRepository(LkDb::class)->setEntityData($params, $db);
        $this->entityManager->flush();
        $flashMessage->addSuccessMessage('Данные сохранены');
        return new Response\JsonResponse(['url' => $this->urlHelper->generate('office.db.list')]);
    }
}
