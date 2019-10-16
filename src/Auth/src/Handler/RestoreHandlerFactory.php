<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 10:56
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use Auth\Service\SendMail;
use Auth\UserRepository\Database;
use Interop\Container\ContainerInterface;

/**
 * ackage Auth\Handler
 */
class RestoreHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return RestoreHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        return new RestoreHandler(
            $container->get('doctrine.entity_manager.orm_default'),
            $container->get(Database::class),
            $container->get(SendMail::class),
            $container->get(UrlHelper::class)
        );
    }
}
