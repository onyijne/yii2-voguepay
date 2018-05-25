<?php

namespace tecsin\pay2\models;

use Yii;
use yii\validators\NumberValidator;
use tecsin\pay2\models\Pay2Sales;
use tecsin\pay2\models\Pay2Setup;
use tecsin\pay2\models\CommandApi;
use yii\base\InvalidConfigException;

/**
 * Handle all money related logics
 * @author Samuel Onyijne <samuel@sajflow.com>
 */
class Money extends \yii\base\Model
{
    public $merchant_ref;
    
    public $v_merchant_id;
    
    public $success_url;
    
    public $failure_url;
    
    public $command_api_token;

    public $merchant_email_on_voguepay;

    public $developer_code;
    
    public $user_id;
    
    public $total;
    
    public $memo;
    
    public $notify_url;
    
    public $receiving_address;
    public $tran;

    /**
     *
     * @var \tecsin\pay2\models\Pay2Setup
     */
     public $_pay2Setup;
     
     public $profileModel = 'app\models\Profile';
     
     public $profileId = 'site_id';
     
     public function __construct($config = array()) {
         $this->setValues(Yii::$app->getSession()->get('pay2_ref'));
         parent::__construct($config);
     }

     public function rules() 
     {
         return [
             [['merchant_ref', 'v_merchant_id', 'success_url', 'failure_url', 'developer_code', 
                 'memo', 'receiving_address', 'command_api_token'], 'string'],
             [['user_id', 'tran'], 'integer'],
             [['total'], 'number'],
             ['merchant_email_on_voguepay', 'email'],
             [['marchant_ref', 'user_id' ,'v_merchant_id', 'success_url', 'failure_url', 
                 'developer_code', 'total', 'notify_url'], 'required'],
             [['merchant_email_on_voguepay', 'command_api_token', 'v_merchant_id'], 'required', 'on' => 'withdraw']
         ];
     }
     
     public function formName() {
         return '';
     }

     public function setValues($ref = null) 
    {
        $id = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
        $this->_pay2Setup = Pay2Setup::getModel();
        $values = [];
        if($this->_pay2Setup){
            $values = [
                'merchant_ref' => ($ref) ? $ref : $id.'_user'. rand(5, 9). time(),
                'user_id' => $id,
                'v_merchant_id' => $this->_pay2Setup->merchant_id,
                'success_url' => $this->_pay2Setup->success_url,
                'failure_url' => $this->_pay2Setup->failure_url,
                'developer_code' => $this->_pay2Setup->getDevCode(),
                'merchant_email_on_voguepay' => $this->_pay2Setup->voguepay_email,
                'command_api_token' => $this->_pay2Setup->api_key,
                '_pay2Setup' => $this->_pay2Setup
            ];
        }
        $this->setAttributes($values, FALSE);
        return $this;
    }
    
    public function withdraw()
    {
        if($this->total < 100){
            return ['report' => 'error', 'message' => 'Your inputed value is too low, you can withdraw from &#8358;100 upwards.'];
        }
        $profile  = $this->profile;
        $a = $this->total + 100;
        if(!$profile->walletHasFund($a)){//check if user has enough balance in wallet
             return ['report' => 'error', 'message' => 'Your inputed value is higher than you wallet balance.'];
        }
        $command = new CommandApi(['command_api_token' => $this->command_api_token, 
            'merchant_email_on_voguepay'=> $this->merchant_email_on_voguepay, 
            'merchant_id' => $this->v_merchant_id]);
        $data = [
            'amount' => $this->total,
            'bank_name' => $profile->bank_name,
            'bank_acct_name' => $profile->bank_account_name,
            'bank_account_number' => $profile->bank_account_number
        ];
        $command->filterWithdrawData = $data;
        $re = $command->withdraw();
        if(array_key_exists('error', '$re')){
             return ['report' => 'error', 'message' => $re['error']['message']];
        }
        return ['report' => 'success', 'message' => $re['status'].': '['description']];
    }
    
    /**
     * Setup Pay2Sales data from pay button form before taking to voguepay.
     * 
     * @return array
     */
    public function payButtonInitVariable()
    {        
        $validator = new NumberValidator();
        if(!$validator->validate($this->total)){
            return ['status' => 'invalid', 'message' => 'Amount entered is not a number.'];
        } else {
            $sales = Pay2Sales::findOne(['ref' => $this->merchant_ref, 'remark' => 'Unprocessed', 'total' =>Yii::$app->getSession()->get('pay2_total')]);
                if(!$sales){
                    $sales = new Pay2Sales([
                        'memo' => $this->memo,
                        'ref' => $this->merchant_ref,
                        'user_id' => $this->user_id,
                        'total' => $this->total,
                        'remark' => 'Unprocessed',
                        'transaction_date' => date('Y-m-d H:i:s')
                    ]);
                    $sales->save(false);
                }                
                //set session to check for when this class is instatited, to search for unprocessed sale record with same total.
                Yii::$app->session->set('pay2_ref', $this->merchant_ref);
                Yii::$app->session->set('pay2_total', $this->total);
                return ['status' => 'valid','message' => 'Taking to VoguePay...' ];
            }  
    }

    /**
     *
     * @return \app\models\Profile
     */
    private function getProfile()
    {
        return Yii::createObject(function(){
            $model = Yii::createObject($this->profileModel);
            if(($profile = $model::findOne([$this->profileId => $this->user_id])) !== null){
                return $profile;
            } else {
                throw new InvalidConfigException('check profileModel property and ensure it is '.$this->profileModel. ' Also the profileId property');
            }
        });
    }
}
