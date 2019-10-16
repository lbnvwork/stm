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

/**
 * Class RollbackHandlerFactory
 *
 * @package Auth\Handler
 */
class RollbackHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return RollbackHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        return new RollbackHandler($container->get(UrlHelper::class));
    }
}
