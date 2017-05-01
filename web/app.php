<?php
//use Acme\HelloServiceProvider;
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->run();