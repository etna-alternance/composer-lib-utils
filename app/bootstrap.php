<?php

require_once __DIR__ . "/../vendor/autoload.php";

$app = new Silex\Application();

$app->register(new TestLibUtils\Config());

return $app;

