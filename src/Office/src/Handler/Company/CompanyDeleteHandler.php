<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 9:49
 */

namespace Office\Handler\Company;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class CompanyDeleteHandler
 * Удаляет компанию
 *
 * @package Office\Handler\Company
 */
class CompanyDeleteHandler implements MiddlewareInterface
{
    //public const TEMPLATE_NAME = 'office::/company/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * CompanyDeleteHandler constructor.
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
        if ($this->entityManager->getRepository(LkCompany::class)->deleteEntity($request, $this->dataPrepareService)) {
            return new Response\JsonResponse(['success' => 'Компания удалена']);
        } else {
            return (new Response())->withStatus(500, 'Неизвестная ошибка при удалении');
        }
    }
}
