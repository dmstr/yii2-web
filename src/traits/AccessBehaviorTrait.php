<?php
namespace dmstr\web\traits;

/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2016 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *
 * Trait AccessBehaviorTrait
 * @package dmstr\web
 * @author Christopher Stebe <c.stebe@herzogkommunikation.de>
 */

use yii\base\Module;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Trait to be attached to a `yii\base\Module` or `yii\web\Controller`
 *
 * Enables accessFilter for "route-access"
 */

trait AccessBehaviorTrait
{
    public function behaviors()
    {
        if ($this instanceof Module) {
            $controller = \Yii::$app->controller;
        } else {
            $controller = $this;
        }

        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow'         => true,
                            'matchCallback' => function ($rule, $action) use ($controller) {
                                return \Yii::$app->user->can(
                                    $controller->module->id . '_' . $controller->id . '_' . $action->id,
                                    ['route' => true]
                                );
                            },
                        ]
                    ]
                ]
            ]
        );
    }
}