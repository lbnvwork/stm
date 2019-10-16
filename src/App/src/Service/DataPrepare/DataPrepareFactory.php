<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 11.04.19
 * Time: 11:55
 */

namespace App\Service\DataPrepare;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DataPrepareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get('doctrine.entity_manager.orm_default');
        $urlHelper = $container->get(UrlHelper::class);
        return new $requestedName($em, $urlHelper);
    }
}
