<?php

use MorikenBot\Dependency;
use MorikenBot\Route;
use MorikenBot\Setting;

require __DIR__ . '/vendor/autoload.php';

$setting = Setting::getSetting();
$app = new \Slim\App($setting);

(new Dependency())->register($app);
(new Route())->register($app);

$app->run();