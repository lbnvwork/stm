<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 16:42
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use Auth\Service\AuthenticationService;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Session\LazySession;
use Zend\Expressive\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LogoutHandler
 *
 * @package Auth\Handler
 */
class LogoutHandler implements RequestHandlerInterface
{
    private $urlHelper;

    /**
     * LogoutHandler constructor.
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
        $session->unset(AuthenticationService::SESSION_AUTH);

        return new RedirectResponse($this->urlHelper->generate('home'));
    }
}
