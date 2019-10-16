<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 17.05.19
 * Time: 16:24
 */

namespace Office\Handler;

use App\Helper\UrlHelper;
use Interop\Container\ContainerInterface;
use Office\Service\DataPrepare\CompanyDataPrepareService;
use Office\Service\Validator\CompanyValidatorService;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CompanyHandlerFactory implements FactoryInterface
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
        $dataPrepareService = $container->get(CompanyDataPrepareService::class);
        $em = $container->get('doctrine.entity_manager.orm_default');
        $validator = $container->get(CompanyValidatorService::class);
        return new $requestedName($em, $template, $container->get(UrlHelper::class), $dataPrepareService, $validator);
    }
}
