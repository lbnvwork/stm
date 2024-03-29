<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class HomePageHandler
 * Вывод лендинга
 *
 * @package App\Handler
 */
class HomePageHandler implements RequestHandlerInterface
{
    /** @var string */
    private $containerName;

    /** @var Router\RouterInterface */
    private $router;

    /** @var null|TemplateRendererInterface */
    private $template;

    /**
     * HomePageHandler constructor.
     *
     * @param string $containerName
     * @param Router\RouterInterface $router
     * @param TemplateRendererInterface|null $template
     */
    public function __construct(
        string $containerName,
        Router\RouterInterface $router,
        ?TemplateRendererInterface $template = null
    ) {
        $this->containerName = $containerName;
        $this->router = $router;
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->template->render('app::home-page'));
    }
}
