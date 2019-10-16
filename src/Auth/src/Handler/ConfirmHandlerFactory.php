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
 * Class ConfirmHandlerFactory
 *
 * @package Auth\Handler
 */
class ConfirmHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ConfirmHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ConfirmHandler($container->get('doctrine.entity_manager.orm_default'), $container->get(UrlHelper::class));
    }
}
