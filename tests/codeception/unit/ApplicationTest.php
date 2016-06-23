<?php

class ApplicationTest extends yii\codeception\TestCase
{
    public $appConfig = '/app/vendor/dmstr/yii2-web/tests/codeception/_config/test.php';

    // tests
    public function testApp()
    {
        $this->assertNotEquals(null,\Yii::$app);
    }

    public function testUser()
    {
        $this->assertNotEquals(null,\Yii::$app->user);
    }
}
