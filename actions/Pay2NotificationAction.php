<?php

/*
 * Copyright (c) Sajflow 2016.
 * please see the LICENSE.md file for license information
 * 
 */

namespace tecsin\pay2\actions;

use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Description of Pay2NotificationAction
 *
 * @author Samuel Onyijne <samuel@sajflow.com>
 */
class Pay2NotificationAction extends \yii\base\Action 
{

    /**
     * Basically there is no need changing this, if you override this model ensure to add a voguepay method to it.
     * @var string Notification className
     */
    public $modelClass = 'tecsin\pay2\models\NotificationExample';
    
    /**
     * ensure this method or any other you use in its place has a parameter which must be an array of transaction from voguepay
     * @var string Notification method
     */
    public $method = 'voguepay';
    
    /**
     *
     * @var array
     */
    protected $transaction = [];


    public function run() 
    {
        $request = Yii::$app->request;
       if(!$request->isPost){
           return Yii::$app->controller->goHome();
       } else {
           if(($transaction_id = $request->post('transaction_id')) == null){
                throw new InvalidParamException('transaction_id is not set');
           }
            //get the full transaction details as json from voguepay
           $client = new Client([
               'baseUrl' => 'https://www.voguepay.com', 
               'responseConfig' => [
               'format' => Client::FORMAT_JSON
             ],]);
           $requestLink = '?v_transaction_id='.$transaction_id.'&type=json';
           $response = $client->get($requestLink)->send();
           //create new array to store our transaction detail
           $this->transaction = Json::decode($response->content);
            return call_user_func([$this->modelClass, $this->method], $this->transaction);
       }
    }
    
}
