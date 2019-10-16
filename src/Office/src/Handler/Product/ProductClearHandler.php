<?php
/**
 * Created by PhpStorm.
 * User: m-lobanov
 * Date: 11.06.19
 * Time: 15:40
 */

namespace Office\Handler\Product;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\ProductDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class ProductClearHandler implements MiddlewareInterface
{
    //public const TEMPLATE_NAME = 'office::/kkt/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * ProductClearHandler constructor.
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
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $id = $request->getAttribute('id');
        $res = $this->entityManager->getRepository(LkProduct::class)->clearProducts($request, $this->dataPrepareService);
        if ($res>0) {
            $flashMessage->addSuccessMessage('База товаров очищена');
            return new Response\RedirectResponse($this->urlHelper->generate('office.product.list', ['id' => $id]));
        } elseif ($res==0) {
            $flashMessage->addWarningMessage('Товары в базе отсутствуют');
            return new Response\RedirectResponse($this->urlHelper->generate('office.product.list', ['id' => $id]));
        } else {
            return new Response\HtmlResponse($this->template->render('error::error'), 500);
        }
    }
}
