<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 22.04.19
 * Time: 15:16
 */

namespace Office\Handler;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Office\Service\SaveFileService;
use Office\Service\SprImport\SprImportService;
use Zend\ServiceManager\Factory\FactoryInterface;

class FirmwareFactory implements FactoryInterface
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
        $dataPrepareService = $container->get(OfficeDataPrepareService::class);
        $saveFileService = $container->get(SaveFileService::class);
        $sprImportService = $container->get(SprImportService::class);
        $em = $container->get('doctrine.entity_manager.orm_default');
        return new $requestedName($em, $container->get(UrlHelper::class), $dataPrepareService, $saveFileService, $sprImportService);
    }
}
