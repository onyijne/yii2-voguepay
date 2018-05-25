<?php

/*
 * Copyright (c) Sajflow 2016 - 2018.
 * please see the LICENSE.md file for license information
 * 
 */

namespace tecsin\pay2\controllers;

use Yii;
use tecsin\pay2\models\searches\Pay2SalesSearch;
use tecsin\pay2\models\searches\Pay2MsSearch;
use tecsin\pay2\models\searches\Pay2CommandHistorySearch;
use tecsin\pay2\controllers\Pay2Controller as Controller;
use tecsin\pay2\models\Pay2Setup;
use yii\filters\VerbFilter;

/**
 * Description
 *
 * @author Samuel Onyijne <samuel@sajflow.com>
 */
class ManageController extends Controller 
{
    public $indexFile = '@vendor/tecsin/yii2-voguepay/views/manage/index' ;  
    public $updateFile = '@vendor/tecsin/yii2-voguepay/views/manage/setup' ; 
    public $salesHistoryFile = '@vendor/tecsin/yii2-voguepay/views/manage/all-sales' ;
    public $saleFile = '@vendor/tecsin/yii2-voguepay/views/manage/view-sale' ; 
    public $commandHistoryFile = '@vendor/tecsin/yii2-voguepay/views/command-history' ;    
    public $msHistoryFile = '@vendor/tecsin/yii2-voguepay/views/ms-history' ; 
    
     /**
     * @var array access control config
     */
    public $accessControlConfig = [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'allow' => true,
                'roles' => ['admin', 'manager'],
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
    
    public function actionIndex() 
    {
        if(($model = Pay2Setup::findOne(1)) == null){
            $model = new Pay2Setup();
            return $this->render($this->updateFile, [
                'model' => $model,
            ]);
        }
        return $this->render($this->indexFile, [
            'model' => $model,
        ]);
    }
   
    
    public function actionSalesHistory() {
        $searchModel = new Pay2SalesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render($this->salesHistoryFile, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
     /**
     * 
     * @param integer $id
     * @return yii\web\Response
     */
    public function actionView($id) {
        return $this->render($this->saleFile, [
            'model' => $this->findPay2SalesModel($id),
        ]);
    }
    
     public function actionMsHistory() {
        
        $searchModel = new Pay2MsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render($this->msHistoryFile, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCommandHistory() {
        $searchModel = new Pay2CommandHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render($this->commandHistoryFile, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionUpdate() 
    {
        $model = $this->findPay2SetupModel();
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->getSession()->setFlash('success', 'Setup updated');
            return $this->redirect(['index']);
        } else {
            return $this->render($this->updateFile, [
                'model' => $model,
            ]);
        }
    }
    
    public function actionDelete($id)
    {
        $this->findPay2MsModel($id)->delete();
        return $this->redirect(['ms-history']);
    }
     
}
