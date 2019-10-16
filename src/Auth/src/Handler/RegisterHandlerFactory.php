<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 14:34
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class RegisterHandlerFactory
 *
 * @package Auth\Handler
 */
class RegisterHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return RegisterHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        return new RegisterHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UserRepositoryInterface::class),
            $container->get('doctrine.entity_manager.orm_default'),
            $container->get(UrlHelper::class)
        );
    }
}
