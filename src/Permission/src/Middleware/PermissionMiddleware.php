<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 22.01.18
 * Time: 10:35
 */

namespace Permission\Middleware;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Router\RouteResult;
use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PermissionMiddleware
 *
 * @package Permission\Middleware
 */
class PermissionMiddleware implements ServerMiddlewareInterface
{
    protected $rbac;

    protected $asserts = [];

    private $urlHelper;

    /**
     * PermissionMiddleware constructor.
     *
     * @param Rbac $rbac
     * @param UrlHelper $urlHelper
     * @param array $asserts
     */
    public function __construct(Rbac $rbac, UrlHelper $urlHelper, array $asserts = [])
    {
        $this->rbac = $rbac;
        $this->urlHelper = $urlHelper;
        $this->asserts = $asserts;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $assert = null;
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        /** @var RouteResult $route */
        $route = $request->getAttribute(RouteResult::class);
        if ($route->isSuccess()) {
            $routeName = $route->getMatchedRoute()->getName();

            if (isset($this->asserts[$routeName])) {
                $assert = new $this->asserts[$routeName]($user);
            }

            $roles = $user ? $user->getRbacRoles() : [new Role('guest')];

            $isGranted = false;
            foreach ($roles as $role) {
                if ($this->rbac->isGranted($role, $routeName, $assert ?? null)) {
                    $isGranted = true;
                    break;
                }
            }

            if (!$isGranted) {
                if ($user === null) {
                    return new RedirectResponse($this->urlHelper->generate('login'));
                }
                if ($routeName === 'login' || $routeName === 'register') {
                    return new RedirectResponse($this->urlHelper->generate('user.profile'));
                }

                return new HtmlResponse($this->template->render('error::403'), 403);
            }
        }

        return $delegate->handle($request);
    }
}
