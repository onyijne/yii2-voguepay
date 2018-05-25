<?php
/*
 * Copyright (c) Sajflow 2016 - 2018.
 * please see the LICENSE.md file for license information
 * 
 */
namespace tecsin\pay2;

/**
 * Pay2Module module definition class
 * @package yii2-voguepay
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'tecsin\pay2\controllers';
    
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'manage/index';
    
    public $userModelClass = 'app\models\User';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
