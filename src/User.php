<?php

namespace dmstr\web;

/**
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class User.
 *
 * Custom user class with additional checks and implementation of a 'root' user, who
 * has all permissions (`can()` always return true)
 *
 * Special Roles:
 * - PUBLIC_ROLE: Default Role for all User, can be configured in authManager['defaultRoles']
 * - GUEST_ROLE:  Special Role for GuestUser. Should have PUBLIC_ROLE as child
 *
 * It additionally performs checks for:
 * - route permissions. eg. a permission `app_site` will match, `app_site_foo`
 * - if `user->isGuest() == true` it checks if permission is assigned to a GUEST_ROLE
 *
 */
class User extends \yii\web\User
{
    const PUBLIC_ROLE = 'Public';
    const GUEST_ROLE = 'Guest';

    /**
     * @var array Users with all permissions
     */
    public $rootUsers = [];

    /**
     * @var boolean whether to show a warning flash message for root users
     */
    public $enableRootWarningFlash = true;

    /**
     * Extended permission check with `Guest` role and `route`.
     *
     * @param string $permissionName
     * @param array $params
     * @param bool|true $allowCaching
     *
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        switch (true) {
            // root users have all permissions
            case \Yii::$app->user->identity && in_array(\Yii::$app->user->identity->username, $this->rootUsers):
                $this->addRootWarningFlash();
                return true;
                break;
            case !empty($params['route']):
                $return = $this->checkAccessRoute($permissionName, $params, $allowCaching);
                \Yii::trace("Checking route permissions for '{$permissionName}', result: " . (int)$return, __METHOD__);
                return $return;
                break;
            default:
                return self::canUserOrGuest($permissionName, $params, $allowCaching);
        }
    }

    /**
     * Checks permissions from guest role, when no user is logged in.
     *
     * @param $permissionName
     * @param $params
     * @param $allowCaching
     *
     * @return bool
     */
    private function canGuest($permissionName, $params, $allowCaching)
    {
        static $guestPermissions;

        if ($guestPermissions === null) {
            \Yii::trace('Fetching guest permissions form auth manager',  __METHOD__);
            $guestPermissions = $this->getAuthManager()->getPermissionsByRole(self::GUEST_ROLE);
        }

        return array_key_exists($permissionName, $guestPermissions);
    }

    /**
     * Checks permissions for authenticated User || guest role
     *
     * @param $permissionName
     * @param $params
     * @param $allowCaching
     *
     * @return bool
     */
    private function canUserOrGuest($permissionName, $params, $allowCaching) {
        if (\Yii::$app->user->id) {
           return parent::can($permissionName, $params, $allowCaching);
        } else {
            return $this->canGuest($permissionName, $params, $allowCaching);
        }
    }

    /**
     * Checks route permissions.
     *
     * Splits `permissionName` by underscore and match parts against more global rule
     * eg. a permission `app_site` will match, `app_site_foo`
     *
     * @param $permissionName
     * @param $params
     * @param $allowCaching
     *
     * @return bool
     */
    private function checkAccessRoute($permissionName, $params, $allowCaching)
    {
        $route = explode('_', $permissionName);
        $routePermission = '';
        foreach ($route as $part) {
            $routePermission .= $part;
            $canRoute = self::canUserOrGuest($routePermission, $params, $allowCaching);
            if ($canRoute) {
                return true;
            }
            $routePermission .= '_';
        }

        return false;
    }

    private function addRootWarningFlash()
    {
        static $found = false;

        if ($this->enableRootWarningFlash && !$found && !\Yii::$app->request->isAjax) {
            $warning = 'You are logged in as an unrestricted root user, this is only recommended for maintenance tasks.';
            $warnings = \Yii::$app->session->getFlash('warning');
            if (!$warnings || array_search($warning, $warnings) === false) {
                \Yii::$app->session->addFlash(
                    'warning',
                    'You are logged in as an unrestricted root user, this is only recommended for maintenance tasks.'
                );
                $found = true;
            }
        }
    }
}
