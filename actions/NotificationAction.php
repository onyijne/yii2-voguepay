<?php

/*
 * Copyright (c) Sajflow 2016.
 * please see the LICENSE.md file for license information
 * 
 */

namespace tecsin\pay2\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;

/**
 * Description of NotificationAction
 *
 * @author Samuel Onyijne <samuel@sajflow.com>
 */
class NotificationAction extends Action {

    /**
     * @var string Login Form className
     */
    public $modelClass = 'tecsin\pay2\models\NotificationExample';
    
    public function run() {
        $request = Yii::$app->request;
       if(!$request->isPost){
           return Yii::$app->controller->goHome();
       } else {
           if(($transaction_id = $request->post('transaction_id')) == null){
                throw new InvalidParamException('transaction_id is not set');
           }
            //get the full transaction details as json from voguepay
           $client = new yii\httpclient\Client([
               'baseUrl' => 'https://www.voguepay.com', 
               'responseConfig' => [
               'format' => \yii\httpclient\Client::FORMAT_JSON
             ],]);
           $requestLink = '?v_transaction_id='.$transaction_id.'&type=json';
           $response = $client->get($requestLink)->send();
           //create new array to store our transaction detail
	    $transaction = \yii\helpers\Json::decode($response->content);
            $model = Yii::createObject($this->modelClass);
            return $model->voguepayNotification($transaction);
       }
    }
}
