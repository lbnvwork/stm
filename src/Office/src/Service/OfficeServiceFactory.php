<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 11.04.19
 * Time: 11:55
 */

namespace Office\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class OfficeServiceFactory
 *
 * @package Office\Service
 */
class OfficeServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get('doctrine.entity_manager.orm_default');
        return new $requestedName($em);
    }
}
