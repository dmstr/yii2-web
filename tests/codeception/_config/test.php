<?php

$basePath = '/app';

return yii\helpers\ArrayHelper::merge(
    require($basePath . '/src/config/main.php'),
    [
        'aliases' => [
            'dmstr/web' => '@vendor/dmstr/yii2-web/src'
        ],
        'components' => [
            'user' => [
                'class' => 'dmstr\web\User',
                'identityClass' => 'yii\web\User',
            ]
        ]
    ]
);
