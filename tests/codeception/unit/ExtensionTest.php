<?php


class ExtensionTest extends yii\codeception\TestCase
{
    use \Codeception\Specify;

    public $appConfig = '/app/vendor/dmstr/yii2-web/tests/codeception/_config/test.php';

    public function testYiiApp()
    {
        $this->assertNotEquals(\Yii::$app, null);
    }

    public function testDebugUser()
    {
        $this->assertNotEquals(\Yii::$app->user, null);
    }
}