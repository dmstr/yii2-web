<?php

namespace dmstr\web;

/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2016 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\helpers\FileHelper;
use yii\web\AssetBundle as BaseAssetBundle;

/**
 * Configuration for `backend` client script files.
 *
 * @since 4.0
 */
class AssetBundle extends BaseAssetBundle
{
    /**
     * Initializes asset bundle with optional CSS/LESS development settings
     *
     * If `APP_ASSET_FORCE_PUBLISH` is set, touch the asset folder with the highest mtime
     * of all contained files.
     * This will create a new folder in web/assets for every change and request made to the app assets.
     */
    public function init()
    {
        parent::init();

        if (getenv('APP_ASSET_FORCE_PUBLISH')) {
            $path = \Yii::getAlias($this->sourcePath);
            $files = FileHelper::findFiles($path);
            $mtimes = [];
            foreach ($files as $file) {
                $mtimes[] = filemtime($file);
            }
            touch($path, max($mtimes));
        }
    }
}
