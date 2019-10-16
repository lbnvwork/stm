<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:14
 */

namespace Office\Handler\Firmware;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use Office\Entity\RefFirmVid;
use Office\Service\DataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FirmwareNewHandler
 * Выводит форму добавления прошивки
 *
 * @package Office\Handler\Firmware
 */
class FirmwareNewHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/firmware/item';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * FirmwareNewHandler constructor.
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
        $prepareRefArr = [
            [
                'name' => 'vid',
                'class' => RefFirmVid::class
            ]
        ];
        $data = $this->dataPrepareService->prepareRefDada($prepareRefArr);
        return new HtmlResponse(
            $this->template->render(
                self::TEMPLATE_NAME,
                [
                    'layout' => 'office::office',
                    'data' => $data
                ]
            )
        );
    }
}
