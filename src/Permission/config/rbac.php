<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 16:46
 */

declare(strict_types=1);
return [
    'roles' => [
        'admin' => [],
        'office_manager' => ['admin'],
        'office_cto_user' => [],
        //['office_manager'],
        'office_user' => ['office_cto_user'],
        'user' => [
            'office_user',
            'office_manager'
        ],
        'guest' => ['user'],
    ],
    'permissions' => [
        'admin' => [
            //'admin.index',
            //'admin.roles',
            //'admin.role',
            //'admin.settings',
        ],
        'office_manager' => [
            'admin.left-menu',
            'admin.user.list',
            'admin.user.new',
            'admin.user.add',
            'admin.user.item',
            'admin.user.edit',
            'admin.user.delete',
            'admin.user.login'
        ],
        'office_cto_user' => [
            'office_cto_user.left-menu',
            'office.customer.list',
            'office.customer.new',
            'office.customer.add',
            'office.customer.delete',
        ],
        'office_user' => [
            'office_user.left-menu',
            'office.company.list',
            'office.company.new',
            'office.company.add',
            'office.company.item',
            'office.company.edit',
            'office.company.delete',

            'office.kkt.list',
            'office.kkt.new',
            'office.kkt.add',
            'office.kkt.item',
            'office.kkt.edit',
            'office.kkt.delete',

            'office.db.list',
            //'office.db.new',
            'office.db.add',
            'office.db.item',
            'office.db.edit',
            'office.db.delete',

            'office.product.list',
            'office.product.add',
            'office.product.edit',
            'office.product.delete',
            'office.product.clear',
            'office.product.item',
            'office.product.import',

//            'office.firmware.list',
//            'office.firmware.new',
//            'office.firmware.add',
//            'office.firmware.item',
//            'office.firmware.edit',
//            'office.firmware.delete',

        ],
        'user' => [
            'logout',
            'user.changePassword',
            'user.rollback',
            'user.profile',
        ],
        'guest' => [
            'home',
            'login',
            'register',
            'user.confirm',
            'user.forget',
            'user.restore',
        ],
    ],
    'asserts' => [
        'login' => \Permission\Asserts\NotLogin::class,
        'register' => \Permission\Asserts\NotLogin::class,
    ],
];
