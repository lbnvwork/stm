<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 10:52
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Service\AuthenticationService;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Session\LazySession;
use Zend\Expressive\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RollbackHandler
 *
 * @package Auth\Handler
 */
class RollbackHandler implements RequestHandlerInterface
{
    private $urlHelper;

    /**
     * RollbackHandler constructor.
     *
     * @param UrlHelper $urlHelper
     */
    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var LazySession $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if ($session && $session->get('rollback')) {
            $session->set(AuthenticationService::SESSION_AUTH, $session->get('rollback'));
            $session->unset('rollback');

            /** @var FlashMessage $flashMessage */
            $flashMessage = $request->getAttribute(FlashMessage::class);
            $flashMessage->addSuccessMessage('Вы вернулись в админку');

            return new RedirectResponse($this->urlHelper->generate('user.profile'));
        }

        return new RedirectResponse($this->urlHelper->generate('home'));
    }
}
