<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:15
 */

namespace Office\Handler\Firmware;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkFirm;
use Office\Service\DataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class FirmwareDeleteHandler
 * Удаляет прошивку
 *
 * @package Office\Handler\Firmware
 */
class FirmwareDeleteHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/firmware/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * FirmwareDeleteHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param DataPrepareService $dataPrepareService
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        DataPrepareService $dataPrepareService
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
        if ($this->entityManager->getRepository(LkFirm::class)->deleteEntity($request, $this->dataPrepareService)) {
            return new Response\JsonResponse(['success' => 'Компания удалена']);
        } else {
            return (new Response())->withStatus(500, 'Неизвестная ошибка при удалении');
        }
    }
}
