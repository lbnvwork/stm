<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 28.05.19
 * Time: 11:13
 */

namespace App\Service\Validator;

use Interop\Container\ContainerInterface;

/**
 * Class ValidatorServiceFactory
 *
 * @package App\Service\Validator
 */
class ValidatorServiceFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get('doctrine.entity_manager.orm_default');
        return new $requestedName($em);
    }
}
