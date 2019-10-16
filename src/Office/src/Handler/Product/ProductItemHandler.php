<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 30.04.19
 * Time: 15:26
 */

namespace Office\Handler\Product;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\ProductDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class ProductItemHandler
 * Выводит информацию о продукте
 *
 * @package Office\Handler\Product
 */
class ProductItemHandler implements MiddlewareInterface
{
    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * ProductItemHandler constructor.
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
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        $pid = $request->getAttribute('pid');
        /** @var LkDb $db */
        $db = $this->entityManager->getRepository(LkDb::class)->findOneBy(['id' => $id]);
        if (!$this->dataPrepareService->checkForUser($user, $db)) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };
        /** @var LkProduct $product */
        $product = $this->entityManager->getRepository(LkProduct::class)->findOneBy(['id' => $pid]);
        $data = [
            'strih' => htmlspecialchars($product->getStrih()),
            'name' => htmlspecialchars($product->getName()),
            'count' => htmlspecialchars($product->getCount()),
            'unitMeasure' => htmlspecialchars($product->getUnitMeasure()),
            'section' => htmlspecialchars($product->getSection()),
            'price' => htmlspecialchars($product->getPrice())
        ];
        return new Response\JsonResponse(
            [
                'url' => $this->urlHelper->generate(
                    'office.product.edit',
                    [
                        'id' => $db->getId(),
                        'pid' => $product->getId()
                    ]
                ),
                'product' => $data
            ]
        );
    }
}
