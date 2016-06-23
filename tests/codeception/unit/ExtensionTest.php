<?php


class ExtensionTest extends yii\codeception\TestCase
{
    use \Codeception\Specify;

    public $appConfig = '/app/vendor/dmstr/yii2-web/tests/codeception/_config/test.php';

    public function testApp()
    {
        $this->assertNotEquals(\Yii::$app, null);
    }

    public function testUser()
    {
        $this->assertNotEquals(\Yii::$app->user, null);
    }

    public function testUserCan()
    {
        #\Codeception\Util\Debug::debug(\Yii::$app->user);
        $this->assertEquals(\Yii::$app->user->can('something'), false);
    }

}