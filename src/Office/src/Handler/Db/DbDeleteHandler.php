<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:00
 */

namespace Office\Handler\Db;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DbDeleteHandler
 * Удаляет базу товаров
 *
 * @package Office\Handler\Db
 */
class DbDeleteHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/db/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * DbDeleteHandler constructor.
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
        OfficeDataPrepareService $dataPrepareService
    ) {

        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->dataPrepareService = $dataPrepareService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->entityManager->getRepository(LkDb::class)->deleteEntity($request, $this->dataPrepareService)) {
            return new Response\JsonResponse(['success' => 'База товаров удалена']);
        } else {
            return new Response\HtmlResponse($this->template->render('error::error'), 500);
        }
    }
}
