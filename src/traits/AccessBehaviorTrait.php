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

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

trait AccessBehaviorTrait
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow'         => true,
                            'matchCallback' => function ($rule, $action) {
                                return \Yii::$app->user->can(
                                    $this->module->id . '_' . $this->id . '_' . $action->id,
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