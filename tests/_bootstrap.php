<?php
// This is global bootstrap for autoloading
$basePath = '/app';

require($basePath . '/vendor/autoload.php');
require($basePath . '/src/config/env.php');


require_once($basePath . '/vendor/yiisoft/yii2/Yii.php');
