<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 29.04.19
 * Time: 16:11
 */

namespace Office\Handler\Db;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class DbItemHandler
 *
 * @package Office\Handler\Db
 */
class DbItemHandler implements MiddlewareInterface
{
    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * DbItemHandler constructor.
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
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        /** @var LkDb $db */
        $db = $this->entityManager->getRepository(LkDb::class)->findOneBy(['id' => $id]);
        if (!$this->dataPrepareService->checkForUser($user, $db)) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };
        $data = [
            'name' => htmlspecialchars($db->getName()),
            'maxCount' => htmlspecialchars($db->getMaxCount())
        ];
        return new Response\JsonResponse(
            [
                'url' => $this->urlHelper->generate('office.db.edit', ['id' => $db->getId()]),
                'db' => $data
            ]
        );
    }
}
