<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 04.04.19
 * Time: 11:14
 */
//define('CURRENT_DATE', '2019-03-14');
$dirName = dirname(__DIR__);
chdir($dirName);
require 'vendor/autoload.php';

define('ROOT_PATH', $dirName.'/');

use Symfony\Component\Console\Application;

/** @var \Interop\Container\ContainerInterface $container */
$container = require 'config/container.php';
$application = new Application('Application console');

$commands = $container->get('config')['console']['commands'];
foreach ($commands as $command) {
    $application->add($container->get($command));
}

$application->run();