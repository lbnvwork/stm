<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 30.05.19
 * Time: 16:33
 */

namespace Office\Handler;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Office\Service\Validator\DbValidatorService;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DbHandlerFactory implements FactoryInterface
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
        /** @var TemplateRendererInterface $template */
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $dataPrepareService = $container->get(OfficeDataPrepareService::class);
        $em = $container->get('doctrine.entity_manager.orm_default');
        $validator = $container->get(DbValidatorService::class);
        return new $requestedName($em, $template, $container->get(UrlHelper::class), $dataPrepareService, $validator);
    }
}
