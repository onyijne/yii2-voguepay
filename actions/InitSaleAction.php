<?php

namespace tecsin\pay2\actions;


use tecsin\pay2\models\Money;
use Yii;
use yii\web\Response;
use yii\helpers\Json;

/**
 * Description of InitSalesAction
 *
 * @author samuel
 */
class InitSaleAction extends \yii\base\Action 
{
    
    public function run()
    {
        $re = Yii::$app->request;
        $model = new Money();
        if($model->load($re->post())){
            $data = $model->payButtonInitVariable();                    
        } else {
            $data = ['status' => 'invalid', 'message' => 'model did not load via post.'];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Json::encode($data);
    }
    
}
