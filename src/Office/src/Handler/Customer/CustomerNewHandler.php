<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 08.05.19
 * Time: 11:56
 */

namespace Office\Handler\Customer;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Service\DataPrepare\CustomerDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class CustomerNewHandler
 * Выводит форму добавления клиента в список
 *
 * @package Office\Handler\Customer
 */
class CustomerNewHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/customer/new';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * CustomerNewHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param CustomerDataPrepareService $dataPrepareService
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        CustomerDataPrepareService $dataPrepareService
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
        $prepareLkArr = [
            [
                'name' => 'companies',
                'class' => LkCompany::class
            ],
        ];
        //$lkData = $this->dataPrepareService->prepareLkData($prepareLkArr, $user);

        $data = $this->dataPrepareService->getData($request);
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
