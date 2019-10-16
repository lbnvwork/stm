<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 04.04.19
 * Time: 11:14
 */
return [
    'dependencies' => [
        'invokables' => [
        ],

        'factories' => [
            App\Command\InitAppCommand::class => App\Command\InitAppCommandFactory::class,
        ],
    ],

    'console' => [
        'commands' => [
            App\Command\InitAppCommand::class,
        ],
    ],
];