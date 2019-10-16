<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 10:56
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use Auth\Service\SendMail;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class ForgetHandlerFactory
 *
 * @package Auth\Handler
 */
class ForgetHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ForgetHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ForgetHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get('doctrine.entity_manager.orm_default'),
            $container->get(SendMail::class),
            $container->get(UrlHelper::class)
        );
    }
}
