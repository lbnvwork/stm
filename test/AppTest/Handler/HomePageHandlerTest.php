<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageHandlerTest extends TestCase
{
    /** @var ContainerInterface|ObjectProphecy */
    protected $container;

    /** @var RouterInterface|ObjectProphecy */
    protected $router;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->router = $this->prophesize(RouterInterface::class);
    }

//    public function testReturnsJsonResponseWhenNoTemplateRendererProvided()
//    {
//        $homePage = new HomePageHandler(
//            get_class($this->container->reveal()),
//            $this->router->reveal(),
//            null
//        );
//        $response = $homePage->handle(
//            $this->prophesize(ServerRequestInterface::class)->reveal()
//        );
//
//        $this->assertInstanceOf(JsonResponse::class, $response);
//    }

    public function testReturnsHtmlResponseWhenTemplateRendererProvided()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer
            //->render('app::home-page', Argument::type('array'))
            ->render('app::home-page')
            ->willReturn('');

        $homePage = new HomePageHandler(
            get_class($this->container->reveal()),
            $this->router->reveal(),
            $renderer->reveal()
        );

        $response = $homePage->handle(
            $this->prophesize(ServerRequestInterface::class)->reveal()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
    }
}
