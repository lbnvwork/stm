<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 08.05.19
 * Time: 15:41
 */

namespace Cms\Handler\User;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Auth\Service\AuthenticationService;
use Doctrine\ORM\EntityManager;
use App\Service\DataPrepare\DataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Session\LazySession;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class UserLoginHandler
 * Выполняет авторизацию под пользователем
 *
 * @package Cms\Handler\User
 */
class UserLoginHandler implements MiddlewareInterface
{
    //public const TEMPLATE_NAME = 'office::/company/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * UserLoginHandler constructor.
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $user = null;
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);

        if ($user === null) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        }

        /** @var LazySession $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $session->set(AuthenticationService::SESSION_AUTH, $user->getId());
        $session->set('rollback', $request->getAttribute(UserInterface::class)->getId());
        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $flashMessage->addWarningMessage('Вы авторизованны под пользователем '.$user->getFIO().', будьте внимательны!');

        return new Response\RedirectResponse($this->urlHelper->generate('user.profile'));
    }
}
