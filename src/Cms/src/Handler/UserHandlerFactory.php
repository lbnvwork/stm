<?php
/**
 * Created by PhpStorm.
 * User: Maksim
 * Date: 20.05.2019
 * Time: 21:55
 */

namespace Cms\Handler;

use App\Helper\UrlHelper;
use Cms\Service\Validator\UserValidatorService;
use Interop\Container\ContainerInterface;
use App\Service\DataPrepare\DataPrepareService;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserHandlerFactory
 *
 * @package Cms\Handler
 */
class UserHandlerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var TemplateRendererInterface $template */
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $dataPrepare = $container->get(DataPrepareService::class);
        $em = $container->get('doctrine.entity_manager.orm_default');
        $validator = $container->get(UserValidatorService::class);
        return new $requestedName(
            $em,
            $template,
            $container->get(UrlHelper::class),
            $dataPrepare,
            $container->get(UserRepositoryInterface::class),
            $validator
        );
    }
}
