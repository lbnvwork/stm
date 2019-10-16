<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:37
 */

namespace Office\Handler\Kkt;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkKkt;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class KktDeleteHandler
 * Удаляет кассу
 *
 * @package Office\Handler\Kkt
 */
class KktDeleteHandler implements MiddlewareInterface
{
    //public const TEMPLATE_NAME = 'office::/kkt/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * KktDeleteHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param $dataPrepareService
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
        if ($this->entityManager->getRepository(LkKkt::class)->deleteEntity($request, $this->dataPrepareService, 'company')) {
            return new Response\JsonResponse(['success' => 'Касса удалена']);
        } else {
            return (new Response())->withStatus(500, 'Неизвестная ошибка при удалении');
        }
    }
}
