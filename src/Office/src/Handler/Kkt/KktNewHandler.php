<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:36
 */

namespace Office\Handler\Kkt;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Service\DataPrepare\KktDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class KktNewHandler
 * Выводит форму добавления кассы
 *
 * @package Office\Handler\Kkt
 */
class KktNewHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/kkt/item';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * KktNewHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param KktDataPrepareService $dataPrepareService
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        KktDataPrepareService $dataPrepareService
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
        $data = $this->dataPrepareService->getData($request);
        $data['companies'] = $this->dataPrepareService->unsetCtoUsersCompanies($data['companies'], $user);
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
