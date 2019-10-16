<?php
declare(strict_types=1);

namespace Auth;

use App\Service\Validator\ValidatorServiceFactory;

/**
 * The configuration provider for the Auth module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
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
                Handler\LoginHandler::class => Handler\LoginHandlerFactory::class,
                Handler\LogoutHandler::class => Handler\LogoutHandlerFactory::class,
                Handler\RegisterHandler::class => Handler\RegisterHandlerFactory::class,
                Handler\ConfirmHandler::class => Handler\ConfirmHandlerFactory::class,
                Handler\ForgetHandler::class => Handler\ForgetHandlerFactory::class,
                Handler\RestoreHandler::class => Handler\RestoreHandlerFactory::class,
                Handler\ChangePasswordHandler::class => Handler\ChangePasswordHandlerFactory::class,
                Handler\UserProfileHandler::class => Handler\UserProfileHandlerFactory::class,
                Handler\RollbackHandler::class => Handler\RollbackHandlerFactory::class,

                Service\AuthenticationService::class => Service\AuthenticationServiceFactory::class,
                Service\SendMail::class => Service\SendMailFactory::class,
                Service\Validator\ProfileValidatorService::class => ValidatorServiceFactory::class,

                UserRepository\Database::class => UserRepository\DatabaseFactory::class,
                Middleware\AuthenticationMiddleware::class => Middleware\AuthenticationMiddlewareFactory::class,
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
                'auth' => [__DIR__.'/../templates/auth'],
                'office' => [__DIR__.'/../../Office/templates/layout'],
//                'error' => [__DIR__.'/../templates/error'],
                'layout' => [__DIR__.'/../templates/layout'],
                'app' => [__DIR__.'/../../App/templates/layout'],
            ],
        ];
    }
}
