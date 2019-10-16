<?php
declare(strict_types=1);

namespace Office;

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
                Handler\Company\CompanyAddHandler::class => Handler\CompanyHandlerFactory::class,
                Handler\Company\CompanyDeleteHandler::class => Handler\OfficeHandlerFactory::class,
                Handler\Company\CompanyEditHandler::class => Handler\CompanyHandlerFactory::class,
                Handler\Company\CompanyItemHandler::class => Handler\CompanyHandlerFactory::class,
                Handler\Company\CompanyNewHandler::class => Handler\CompanyHandlerFactory::class,
                Handler\Company\CompanyListHandler::class => Handler\OfficeHandlerFactory::class,

                Handler\Db\DbAddHandler::class => Handler\DbHandlerFactory::class,
                Handler\Db\DbDeleteHandler::class => Handler\OfficeHandlerFactory::class,
                Handler\Db\DbItemHandler::class => Handler\OfficeHandlerFactory::class,
                Handler\Db\DbEditHandler::class => Handler\DbHandlerFactory::class,
                Handler\Db\DbListHandler::class => Handler\OfficeHandlerFactory::class,

                Handler\Product\ProductAddHandler::class => Handler\ProductHandlerFactory::class,
                Handler\Product\ProductDeleteHandler::class => Handler\ProductHandlerFactory::class,
                Handler\Product\ProductClearHandler::class => Handler\ProductHandlerFactory::class,
                Handler\Product\ProductItemHandler::class => Handler\ProductHandlerFactory::class,
                Handler\Product\ProductEditHandler::class => Handler\ProductHandlerFactory::class,
                Handler\Product\ProductListHandler::class => Handler\ProductHandlerFactory::class,
                Handler\Product\ProductImportHandler::class => Handler\ProductImportFactory::class,

//                Handler\Firmware\FirmwareAddHandler::class => Handler\FirmwareFactory::class,
//                Handler\Firmware\FirmwareDeleteHandler::class => Handler\OfficeHandlerFactory::class,
//                Handler\Firmware\FirmwareEditHandler::class => Handler\FirmwareFactory::class,
//                Handler\Firmware\FirmwareItemHandler::class => Handler\OfficeHandlerFactory::class,
//                Handler\Firmware\FirmwareNewHandler::class => Handler\OfficeHandlerFactory::class,
//                Handler\Firmware\FirmwareListHandler::class => Handler\OfficeHandlerFactory::class,

                Handler\Kkt\KktAddHandler::class => Handler\KktHandlerFactory::class,
                Handler\Kkt\KktDeleteHandler::class => Handler\OfficeHandlerFactory::class,
                Handler\Kkt\KktEditHandler::class => Handler\KktHandlerFactory::class,
                Handler\Kkt\KktItemHandler::class => Handler\KktHandlerFactory::class,
                Handler\Kkt\KktNewHandler::class => Handler\KktHandlerFactory::class,
                Handler\Kkt\KktListHandler::class => Handler\OfficeHandlerFactory::class,

                Handler\Customer\CustomerListHandler::class => Handler\CustomerFactory::class,
                Handler\Customer\CustomerNewHandler::class => Handler\CustomerFactory::class,
                Handler\Customer\CustomerAddHandler::class => Handler\CustomerFactory::class,
                Handler\Customer\CustomerDeleteHandler::class => Handler\OfficeHandlerFactory::class,

                Service\DataPrepare\OfficeDataPrepareService::class => \App\Service\DataPrepare\DataPrepareFactory::class,
                Service\DataPrepare\KktDataPrepareService::class => \App\Service\DataPrepare\DataPrepareFactory::class,
                Service\DataPrepare\CompanyDataPrepareService::class => \App\Service\DataPrepare\DataPrepareFactory::class,
                Service\DataPrepare\CustomerDataPrepareService::class => \App\Service\DataPrepare\DataPrepareFactory::class,
                Service\DataPrepare\ProductDataPrepareService::class => \App\Service\DataPrepare\DataPrepareFactory::class,

                Service\SaveFileService::class => Service\OfficeServiceFactory::class,
                Service\SprImport\SprImportService::class => Service\OfficeServiceFactory::class,

                Service\Validator\CompanyValidatorService::class => \App\Service\Validator\ValidatorServiceFactory::class,
                Service\Validator\KktValidatorService::class => \App\Service\Validator\ValidatorServiceFactory::class,
                Service\Validator\ProductValidatorService::class => \App\Service\Validator\ValidatorServiceFactory::class,
                Service\Validator\DbValidatorService::class => \App\Service\Validator\ValidatorServiceFactory::class,

                Helper\TemplateHelper::class => Helper\TemplateHelperFactory::class,
                Helper\IsPropertyCtoUser::class => Helper\IsPropertyCtoUserFactory::class,

                Middleware\CheckProfileMiddleware::class => Middleware\CheckProfileMiddlewareFactory::class,
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
                'office' => [__DIR__.'/../templates/office'],
                'cms' => [__DIR__.'/../../Cms/templates/layout'],
//                'error' => [__DIR__.'/../templates/error'],
                'layout' => [__DIR__.'/../templates/layout'],
            ],
        ];
    }
}
