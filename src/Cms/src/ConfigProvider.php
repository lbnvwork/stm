<?php
/**
 * Created by PhpStorm.
 * User: Maksim
 * Date: 20.05.2019
 * Time: 21:29
 */

namespace Cms;

/**
 * Class ConfigProvider
 *
 * @package Cms
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
            ],
            'factories' => [
                Handler\User\UserAddHandler::class => Handler\UserHandlerFactory::class,
                //Handler\User\UserDeleteHandler::class => Handler\UserHandlerFactory::class,
                Handler\User\UserEditHandler::class => Handler\UserHandlerFactory::class,
                Handler\User\UserItemHandler::class => Handler\UserHandlerFactory::class,
                Handler\User\UserNewHandler::class => Handler\UserHandlerFactory::class,
                Handler\User\UserListHandler::class => Handler\UserHandlerFactory::class,
                Handler\User\UserLoginHandler::class => Handler\UserHandlerFactory::class,

                Service\Validator\UserValidatorService::class => \App\Service\Validator\ValidatorServiceFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            'paths' => [
                'cms' => [__DIR__.'/../templates/'],
//                'error' => [__DIR__.'/../templates/error'],
                'office' => [__DIR__.'/../../Office/templates/layout'],
                'layout' => [__DIR__.'/../templates/layout'],
            ],
        ];
    }
}
