<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 10:56
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class LogoutHandlerFactory
 *
 * @package Auth\Handler
 */
class LogoutHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return LogoutHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        return new LogoutHandler($container->get(UrlHelper::class));
    }
}
