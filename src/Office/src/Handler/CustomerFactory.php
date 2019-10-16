<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 15.05.19
 * Time: 9:46
 */

namespace Office\Handler;

use App\Helper\UrlHelper;
use Auth\Service\SendMail;
use Interop\Container\ContainerInterface;
use Office\Service\DataPrepare\CustomerDataPrepareService;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CustomerFactory implements FactoryInterface
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
        $dataPrepare = $container->get(CustomerDataPrepareService::class);
        $em = $container->get('doctrine.entity_manager.orm_default');
        /** @var TemplateRendererInterface $template */
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        return new $requestedName(
            $em,
            $template,
            $container->get(UrlHelper::class),
            $dataPrepare,
            $container->get(UserRepositoryInterface::class),
            $container->get(SendMail::class)
        );
    }
}
