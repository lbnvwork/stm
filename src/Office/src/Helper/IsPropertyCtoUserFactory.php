<?php
/**
 * Created by PhpStorm.
 * User: m-lobanov
 * Date: 03.06.19
 * Time: 14:35
 */

namespace Office\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IsPropertyCtoUserFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return mixed|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get('doctrine.entity_manager.orm_default');
        return new $requestedName($em);
    }
}
