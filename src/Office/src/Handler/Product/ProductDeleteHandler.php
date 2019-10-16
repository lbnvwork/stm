<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 24.04.19
 * Time: 11:17
 */

namespace Office\Handler\Product;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\ProductDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class ProductDeleteHandler
 * Удаляет продукт
 *
 * @package Office\Handler\Product
 */
class ProductDeleteHandler implements MiddlewareInterface
{
    //public const TEMPLATE_NAME = 'office::/kkt/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * ProductDeleteHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param ProductDataPrepareService $dataPrepareService
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        ProductDataPrepareService $dataPrepareService
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
        if ($this->entityManager->getRepository(LkProduct::class)->deleteProduct($request, $this->dataPrepareService)) {
            return new Response\JsonResponse(['success' => 'Продукт удален']);
        } else {
            return new Response\HtmlResponse($this->template->render('error::error'), 500);
        }
    }
}
