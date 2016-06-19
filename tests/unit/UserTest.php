<?php


class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testApp()
    {
        \Yii::$app;
    }
    public function testUser()
    {
        new dmstr\web\User();
        #\Yii::$app->user;
    }

}