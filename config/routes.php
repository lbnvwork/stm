<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

/**
 * Setup routes with a single request method:
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 * Or with multiple request methods:
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 * Or handling all request methods:
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 * or:
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');
    $app->route(
        '/lk/user/login', Auth\Handler\LoginHandler::class, [
        'GET',
        'POST',
    ], 'login'
    );
    $app->route(
        '/lk/user/register', Auth\Handler\RegisterHandler::class, [
        'GET',
        'POST',
    ], 'register'
    );
    $app->route(
        '/lk/user/change-password', Auth\Handler\ChangePasswordHandler::class, [
        'GET',
        'POST',
    ], 'user.changePassword'
    );
    $app->route(
        '/lk/user/profile', Auth\Handler\UserProfileHandler::class, [
        'GET',
        'POST',
    ], 'user.profile'
    );
    $app->get('/lk/user/rollback', \Auth\Handler\RollbackHandler::class, 'user.rollback');
    $app->get('/lk/user/logout', Auth\Handler\LogoutHandler::class, 'logout');
    $app->get('/lk/user/confirm/{hash:\w+}', Auth\Handler\ConfirmHandler::class, 'user.confirm');
    $app->get('/lk/user/restore/{hash:\w+}', Auth\Handler\RestoreHandler::class, 'user.restore');
    $app->route(
        '/lk/user/forget', Auth\Handler\ForgetHandler::class, [
        'GET',
        'POST'
    ], 'user.forget'
    );

    //begin компания
    $app->get(
        '/lk/company', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Company\CompanyListHandler::class
    ], 'office.company.list'
    );
    $app->get(
        '/lk/company/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Company\CompanyNewHandler::class
    ], 'office.company.new'
    );
    $app->post(
        '/lk/company/new', [
        //\Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Company\CompanyAddHandler::class
    ], 'office.company.add'
    );
    $app->get(
        '/lk/company/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Company\CompanyItemHandler::class
    ], 'office.company.item'
    );
    $app->post(
        '/lk/company/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Company\CompanyEditHandler::class
    ], 'office.company.edit'
    );
    $app->delete(
        '/lk/company/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Company\CompanyDeleteHandler::class
    ], 'office.company.delete'
    );
    //end компания

    //begin касса
    $app->get(
        '/lk/kkt', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Kkt\KktListHandler::class
    ], 'office.kkt.list'
    );
    $app->get(
        '/lk/kkt/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Kkt\KktNewHandler::class
    ], 'office.kkt.new'
    );
    $app->post(
        '/lk/kkt/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Kkt\KktAddHandler::class
    ], 'office.kkt.add'
    );
    $app->get(
        '/lk/kkt/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Kkt\KktItemHandler::class
    ], 'office.kkt.item'
    );
    $app->post(
        '/lk/kkt/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Kkt\KktEditHandler::class
    ], 'office.kkt.edit'
    );
    $app->delete(
        '/lk/kkt/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Kkt\KktDeleteHandler::class
    ], 'office.kkt.delete'
    );
    //end касса

    //begin база товаров
    $app->get(
        '/lk/db', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Db\DbListHandler::class
    ], 'office.db.list'
    );
    $app->post(
        '/lk/db/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Db\DbAddHandler::class
    ], 'office.db.add'
    );
    $app->post(
        '/lk/db/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Db\DbEditHandler::class
    ], 'office.db.edit'
    );
    $app->get(
        '/lk/db/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Db\DbItemHandler::class
    ], 'office.db.item'
    );
    $app->delete(
        '/lk/db/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Db\DbDeleteHandler::class
    ], 'office.db.delete'
    );
    //end база товаров

    //begin товары
    $app->get(
        '/lk/db/{id:\d+}/product/', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Product\ProductListHandler::class
    ], 'office.product.list'
    );
    $app->post(
        '/lk/db/{id:\d+}/product/', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Product\ProductAddHandler::class
    ], 'office.product.add'
    );
    $app->get(
        '/lk/db/{id:\d+}/product/{pid:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Product\ProductItemHandler::class
    ], 'office.product.item'
    );
    $app->post(
        '/lk/db/{id:\d+}/product/{pid:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Product\ProductEditHandler::class
    ], 'office.product.edit'
    );
    $app->delete(
        '/lk/db/{id:\d+}/product/{pid:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Product\ProductDeleteHandler::class
    ], 'office.product.delete'
    );
    $app->post(
        '/lk/db/{id:\d+}/product/import/', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Product\ProductImportHandler::class
    ], 'office.product.import'
    );
    $app->get(
        '/lk/db/{id:\d+}/product/clear/', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Product\ProductClearHandler::class
    ], 'office.product.clear'
    );
    //begin товары

    //begin прошивка
//    $app->get(
//        '/lk/firmware', [
//        \Office\Middleware\CheckProfileMiddleware::class,
//        \Office\Handler\Firmware\FirmwareListHandler::class
//    ], 'office.firmware.list'
//    );
//    $app->get(
//        '/lk/firmware/new', [
//        \Office\Middleware\CheckProfileMiddleware::class,
//        \Office\Handler\Firmware\FirmwareNewHandler::class
//    ], 'office.firmware.new'
//    );
//    $app->post(
//        '/lk/firmware/new', [
//        \Office\Middleware\CheckProfileMiddleware::class,
//        \Office\Handler\Firmware\FirmwareAddHandler::class
//    ], 'office.firmware.add'
//    );
//    $app->get(
//        '/lk/firmware/{id:\d+}', [
//        \Office\Middleware\CheckProfileMiddleware::class,
//        \Office\Handler\Firmware\FirmwareItemHandler::class
//    ], 'office.firmware.item'
//    );
//    $app->post(
//        '/lk/firmware/{id:\d+}', [
//        \Office\Middleware\CheckProfileMiddleware::class,
//        \Office\Handler\Firmware\FirmwareEditHandler::class
//    ], 'office.firmware.edit'
//    );
//    $app->delete(
//        '/lk/firmware/{id:\d+}', [
//        \Office\Middleware\CheckProfileMiddleware::class,
//        \Office\Handler\Firmware\FirmwareDeleteHandler::class
//    ], 'office.firmware.delete'
//    );
    //end прошивка

    //begin пользователи СТО
    $app->get(
        '/lk/customer', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Customer\CustomerListHandler::class
    ], 'office.customer.list'
    );
    $app->get(
        '/lk/customer/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Customer\CustomerNewHandler::class
    ], 'office.customer.new'
    );
    $app->post(
        '/lk/customer/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Customer\CustomerAddHandler::class
    ], 'office.customer.add'
    );
    $app->delete(
        '/lk/customer/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Office\Handler\Customer\CustomerDeleteHandler::class
    ], 'office.customer.delete'
    );
    //end пользователи СТО

    //begin админка
    $app->get(
        '/admin/user', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Cms\Handler\User\UserListHandler::class
    ], 'admin.user.list'
    );
    $app->get(
        '/admin/user/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Cms\Handler\User\UserNewHandler::class
    ], 'admin.user.new'
    );
    $app->post(
        '/admin/user/new', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Cms\Handler\User\UserAddHandler::class
    ], 'admin.user.add'
    );
    $app->get(
        '/admin/user/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Cms\Handler\User\UserItemHandler::class
    ], 'admin.user.item'
    );
    $app->post(
        '/admin/user/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Cms\Handler\User\UserEditHandler::class
    ], 'admin.user.edit'
    );
    $app->delete(
        '/admin/user/{id:\d+}', [
        \Office\Middleware\CheckProfileMiddleware::class,
        //\Cms\Handler\User\UserDeleteHandler::class
    ], 'admin.user.delete'
    );
    $app->get(
        '/lk/user/{id:\d+}/login', [
        \Office\Middleware\CheckProfileMiddleware::class,
        \Cms\Handler\User\UserLoginHandler::class
    ], 'admin.user.login'
    );
    //end админка
};
