<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 22.01.18
 * Time: 10:29
 */

namespace Auth\Middleware;

use Auth\Entity\User;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template;

/**
 * Class AuthenticationMiddleware
 *
 * @package Auth\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthenticationInterface
     */
    protected $auth;

    protected $template;

    public function __construct(AuthenticationInterface $auth, Template\TemplateRendererInterface $template = null)
    {
        $this->auth = $auth;
        $this->template = $template;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        /** @var User $user */
        $user = $this->auth->authenticate($request);

        $this->template->addDefaultParam(Template\TemplateRendererInterface::TEMPLATE_ALL, 'user', $user);

        return $delegate->handle($request->withAttribute(UserInterface::class, $user));
    }
}
