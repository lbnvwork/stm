<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:37
 */

namespace Office\Handler\Kkt;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Entity\LkKkt;
use Office\Service\DataPrepare\KktDataPrepareService;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class KktItemHandler
 * Выводит форму редактирования кассы
 *
 * @package Office\Handler\Kkt
 */
class KktItemHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/kkt/item';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * KktItemHandler constructor.
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
        $id = $request->getAttribute('id');
        /** @var LkKkt $kkt */
        $kkt = $this->entityManager->getRepository(LkKkt::class)->findOneBy(['id' => $id]);
        if (!isset($kkt) || !$this->dataPrepareService->checkForUser($user, $kkt->getCompany())) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        };
        $data = $this->dataPrepareService->getData($request);
        $formulaData = $this->dataPrepareService->getFormulaData($request);
        $data['companies'] = $this->dataPrepareService->unsetCtoUsersCompanies($data['companies'], $user);

        return new HtmlResponse(
            $this->template->render(
                self::TEMPLATE_NAME,
                [
                    'layout' => 'office::office',
                    'kkt' => $kkt,
                    'data' => $data,
                    'formulaData' => $formulaData
                ]
            )
        );
    }
}
