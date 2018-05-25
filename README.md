Pay2
====
VoguePay Payment Processor Extension for Yii2 Framework (Command API is in beta).
 
Pay2 is a Yii2 wrapper for VoguePay Payment Processor Mobile/Server-to-Server and Command Api. Mobile/Server-to-Server API let you get a link token for payment by sending your merchant ID and other necessary parameters. 

Command API allows you to directly perfom several actions (fetch transactions details, pay a user, withdraw money to bank accounts, create a new user) on VoguePay from your application.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist tecsin/yii2-voguepay "~2.0.0"
```

or add

```
"tecsin/yii2-voguepay": "~2.0.0"
```

to the require section of your `composer.json` file.


Usage
-----

First set up the database by running the migration code :

```
php yii migrate --migrationPath="@vendor/tecsin/yii2-voguepay/migrations"
```

After which you should add pay2 to the modules section of your application component like

```php
'components' => [
    //...
    'modules' => [
        //...
        'pay2' => [
            'class' => 'tecsin\pay2\Module',
            'userModelClass' => 'app\models\User',
            'controllerMap' => [
                'manage' => [
                    'class' => 'yii2mod\comments\controllers\ManageController',
                    'layout' => '@app/modules/admin/views/layouts/main',
                    'accessControlConfig' => [
                        'class' => 'yii\filters\AccessControl',
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['admin', 'manager'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
],
```

 and setup your VoguePay details via project.com/pay2. 

Notification and Pay Now Button
------

Get notifications from VoguePay and save data to db before sending user to voguepay.

```php
<?php
namespace app\controllers;

class SiteController extends \yii\web\Controller
{
    //...
    public function actions()
    {
        return [
            //...
            'voguepay-notification' => [
                'class' => 'tecsin\pay2\actions\Pay2NotificationAction', // see this class if you will change anything for better explanations
                'modelClass' => 'tecsin\pay2\models\NotificationExample'//this is the default model to run for every notification 
                'method' => 'voguepay'//the method to be called in modelClass, and must have a parameter which should be an array of transaction from voguepay
            ],
            'set-data' => [
                'class' => 'tecsin\pay2\actions\InitSaleAction', //redirects user to voguepay payment page after saving the pay now form data to db
                //this is mandatory if you use the PayButton widget
            ],
        ];
    }
}
```

Display pay now button
----

```php
    <?= tecsin\pay2\widgets\PayButton::widget() ?>
```

Mobile/Server-to-Server
-----

You can either send user to VoguePay payment page directly (this is the default):

```php
    $MsModel = new \tecsin\pay2\models\VoguepayMs(['aaaMerchantId' => '11111', 'mmmMemo' => 'one sparklyn yellow wedding dress', 'tttTotalCost' => '200310', 'rrrMerchantRef' => time().mt_rand(0,999999999)]);
    if($MsModel->validate()){
       return $MsModel->setRequest()->sendRequest()->sendResponse();
    } 
```

Or show the user a pay button (set showPayButton property to true):

```php
    $MsModel = new \tecsin\pay2\models\VoguepayMs(['aaaMerchantId' => '11111', 'mmmMemo' => 'one sparklyn yellow wedding dress', 'tttTotalCost' => '200310', 'rrrMerchantRef' => time().mt_rand(0,999999999), 'showPayButton' => true]);
    if($MsModel->validate()){
        $response =  $MsModel->setRequest()->sendRequest()->sendResponse();
        return $response;//response is json {status: "success|error", success|error : { message: "https://www.voguepay.com/payment-url|errorMesssge"}}
    } 
```

Command API
-----

With the Command API you can Fetch records of transactions, Pay (send money) to VoguePay merchants, Withdraw money to various bank accounts, and create a new user on VoguePay.

See tecsin\pay2\models\Money for withdrawal example
---

## Read More.
 
See [VoguePay](https://voguepay.com/developers) Developer Page

Contributions
-----

Contributions re highly welcome in any form deemed fit
