#!/usr/bin/env php
<?php
namespace api;
use \api\core\Nomnom;

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/base.php';
require CORE_PATH . DS . 'Loader.php';

Loader::addNamespace('api', LIB_PATH);
Loader::register();

$nomnom = Nomnom::instance();
$apidoc = new Apidoc();

