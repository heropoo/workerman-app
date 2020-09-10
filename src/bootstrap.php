<?php

$root_path = dirname(__DIR__);
require_once $root_path . '/vendor/autoload.php';

use Moon\Application;

$app = new Application($root_path);
return $app;