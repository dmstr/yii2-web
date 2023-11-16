<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2023 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dmstr\web\filters;

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * This is an AccessControl filter with a "hardcoded" rule, that checks access to the current route
 * - Access can be granted to each "level" of the current route
 * - the permission name(s) that will be checked, are the "levels" of the route with concatinated with self::routePartSeparator
 * Example: 
 * - route: 'user/admin/role/index'
 * - self::routePartSeparator = '_'
 * - checks:
 *   - 'user'
 *   - 'user_admin'
 *   - 'user_admin_role'
 *   - 'user_admin_role_index'
 */
class RouteAccessControl extends AccessControl
{

    /**
     * separator that is used to build the rbac item name from the route
     *
     * @var string
     */
    public $routePartSeparator = '_';

    /**
     * optional params for the User:can() check
     *
     * @var array
     */
    public $routeCheckParams = [];
    /**
     * index name that is used in the AccessControl::rules array
     *
     * @var string
     */
    public $accessCheckRuleIndex = 'routeAccess';

    public function beforeAction($action)
    {
        $this->rules[$this->accessCheckRuleIndex] = $this->getRouteAcessControlRule();
        return parent::beforeAction($action);
    }

    protected function getRouteAcessControlRule()
    {
        if ($this->owner instanceof Controller) {
            $controller = $this->owner;
        } else {
            $controller = \Yii::$app->controller;
        }
        $separator = $this->routePartSeparator;
        $routeCheckParams = $this->routeCheckParams;
        $ruleParams = [
                'allow'         => true,
                'matchCallback' => function ($rule, $action) use ($controller, $separator, $routeCheckParams) {
                    // use id including parent modules, if empty (eg. 'app') fall-back to id
                    $moduleId = empty($controller->module->uniqueId) ? $controller->module->id : $controller->module->uniqueId;
                    // get parts of the route to the current controller action
                    $permParts = array_merge(explode('/', trim($moduleId,'/')), explode('/', trim($controller->id, '/')), [$action->id]);
                    # ... and check each level of the fullName
                    $perm = '';
                    foreach ($permParts as $permPart) {
                        $perm = implode($separator, array_filter([$perm, $permPart]));
                        if ($this->user->can($perm, $routeCheckParams)) {
                            return true;
                        }
                    }
                    return false;
                },
            ];

        return \Yii::createObject(array_merge($this->ruleConfig, $ruleParams));
    }

}