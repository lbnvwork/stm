<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 17.05.19
 * Time: 16:27
 */

namespace Office\Handler;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;
use Office\Service\DataPrepare\ProductDataPrepareService;
use Office\Service\Validator\ProductValidatorService;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProductHandlerFactory implements FactoryInterface
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
        $dataPrepareService = $container->get(ProductDataPrepareService::class);
        /** @var TemplateRendererInterface $template */
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $em = $container->get('doctrine.entity_manager.orm_default');
        $validator = $container->get(ProductValidatorService::class);
        return new $requestedName($em, $template, $container->get(UrlHelper::class), $dataPrepareService, $validator);
    }
}
