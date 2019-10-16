<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 04.04.19
 * Time: 10:30
 */

namespace Office\Middleware;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;

/**
 * Class CheckProfileMiddlewareFactory
 *
 * @package Office\Middleware
 */
class CheckProfileMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return CheckProfileMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new CheckProfileMiddleware($container->get('doctrine.entity_manager.orm_default'), $container->get(UrlHelper::class));
    }
}
