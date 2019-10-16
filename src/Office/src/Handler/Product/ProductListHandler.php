<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 10:58
 */

namespace Office\Handler\Product;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Office\Entity\LkDb;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\ProductDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ProductListHandler
 * Выводит список продуктов
 *
 * @package Office\Handler\Product
 */
class ProductListHandler implements MiddlewareInterface
{
    //public const TEMPLATE_NAME = 'office::/db/item';

    private $template;

    public const COUNT_ITEMS = 20;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;


    /**
     * ProductListHandler constructor.
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
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();
        $db = null;
        /** @var LkDb $db */
        $db = $this->entityManager->getRepository(LkDb::class)->findOneBy(['id' => $id]);
        if (!$this->dataPrepareService->checkForUser($user, $db)) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        };

        $data = $this->entityManager->getRepository(LkProduct::class)->getProductsWithOrderBuilder($queryParams, $db);
        /** @var QueryBuilder $qb */
        $qb = $data['qb'];
        $queryParams = $data['queryParams'];
        $data = $this->dataPrepareService->getProducts(
            $queryParams,
            $this->urlHelper->generate('office.product.list', ['id' => $id]),
            $qb,
            self::COUNT_ITEMS
        );
        return new HtmlResponse(
            $this->template->render(
                'office::/product/list',
                [
                    'layout' => 'office::office',
                    'db' => $db,
                    'sortType' => $data['sortType'],
                    'order' => $data['order'],
                    'chevron' => $data['chevron'],
                    'products' => $data['products'],
                    'paginator' => $data['paginator']
                ]
            )
        );
    }
}
