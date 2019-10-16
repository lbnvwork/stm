<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 04.04.19
 * Time: 11:14
 */

namespace App\Middleware;

use App\Service\FlashMessage;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Zend\Expressive\Template;

/**
 * Class SlimFlashMiddleware
 *
 * @package App\Middleware
 */
class FlashMessageMiddleware implements MiddlewareInterface
{
    private $template;

    /**
     * FlashMessageMiddleware constructor.
     *
     * @param Template\TemplateRendererInterface|null $template
     */
    public function __construct(Template\TemplateRendererInterface $template = null)
    {
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $flashMessages = new FlashMessage($session);
        $this->template->addDefaultParam(Template\TemplateRendererInterface::TEMPLATE_ALL, 'flashMessage', $flashMessages);

        return $delegate->handle(
            $request->withAttribute(FlashMessage::class, $flashMessages)
        );
    }
}
