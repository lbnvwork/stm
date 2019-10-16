<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 08.05.19
 * Time: 11:57
 */

namespace Office\Handler\Customer;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
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
 * Class CustomerDeleteHandler
 * Удаляет клиента из списка клиентов
 *
 * @package Office\Handler\Customer
 */
class CustomerDeleteHandler implements MiddlewareInterface
{
    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * CustomerDeleteHandler constructor.
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
        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);
        if ($this->entityManager->getRepository(LkCompany::class)->deleteCustomer($request, $this->dataPrepareService)) {
            $flashMessage->addSuccessMessage('Клиент удален из списка!');
            return new Response\JsonResponse(['success' => 'Клиент удален из списка']);
        } else {
            return (new Response())->withStatus(500, 'Неизвестная ошибка при удалении');
        }
    }
}
