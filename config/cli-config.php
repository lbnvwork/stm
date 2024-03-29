<?php

$dirName = dirname(__DIR__);
define('ROOT_PATH', $dirName.'/');

$container = require 'config/container.php';

return new \Symfony\Component\Console\Helper\HelperSet(
    [
        'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper(
            $container->get('doctrine.entity_manager.orm_default')
        ),
    ]
);