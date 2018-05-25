<?php

namespace tecsin\pay2\controllers;
use Yii;
use tecsin\pay2\models\Pay2Sales;
use tecsin\pay2\models\Pay2CommandHistory;
use tecsin\pay2\models\Pay2Ms;
use yii\web\NotFoundHttpException;
use tecsin\pay2\models\Pay2Setup;
use tecsin\pay2\traits\ModuleTrait;
use yii\filters\VerbFilter;

/**
 * Description of Pay2Controller
 *
 * @author samuel onyijne 
 */
class Pay2Controller extends \yii\web\Controller
{
    use ModuleTrait;
    /**
     * @var string|array URL, which user should be redirected to on success.
     * This could be a plain string URL, URL array configuration which returns actual URL
     */
    public $returnUrl;
    
     /**
     * @var array access control config
     */
    public $accessControlConfig = [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'allow' => true,
                'roles' => ['admin'],
            ],
        ],
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                    'update' => ['get', 'post'],
                    'delete' => ['post'],
                ],
            ],
            'access' => $this->accessControlConfig,
        ];
    }
    
    /**
     * @param string $defaultActionId
     *
     * @return \yii\web\Response
     */
    public function redirectTo($defaultActionId = 'index')
    {
        if ($this->returnUrl !== null) {
            return $this->redirect($this->returnUrl);
        }

        return $this->redirect($defaultActionId);
    }
    
     /**
      * renders a file depending on the request type
      * 
      * @param string $view
      * @param array $params
      * @return yii\web\Response ajax|normal
      */
    public function renderBoth($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        }

        return $this->render($view, $params);
    }
   
    
    /**
     * Finds the Pay2Sales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pay2Sales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPay2SalesModel($id)
    {
        if (($model = Pay2Sales::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * Finds the Pay2CommandHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pay2CommandHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPay2CommandHistoryModel($id)
    {
        if (($model = Pay2CommandHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * Finds the Pay2Ms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pay2Ms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPay2MsModel($id)
    {
        if (($model = Pay2Ms::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
     /**
     * Finds the Pay2Setup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return Pay2Sales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPay2SetupModel()
    {
        return Pay2Setup::getModel();
    }
}
