<?php

/*
 * Copyright (c) Sajflow 2016.
 * please see the LICENSE.md file for license information
 * 
 */

namespace tecsin\pay2\models;

/**
 * Description of NotificationExample
 *
 * @author Samuel Onyijne <samuel@sajflow.com>
 */
class NotificationExample  {
    
     public function voguepayNotification(array $transaction ) {
        $ref = $transaction['merchant_ref'];
        $sales = (Sales::find()->where(['ref'=> $ref])->exists() ? Sales::findOne(['ref'=> $ref]) : new Sales());
        if($sales->remark == 'Payment complete'){
            //to avoid crediting a transaction twice
            return true;
        };
        if($transaction['status'] == 'Approved'){
            if($transaction['total'] !== $sales->total){
               $sales->remark = 'Partial payment of '.$transaction['total'];
            } else {             
                $this->topUpUnit('something to query with');        
                $sales->remark = 'Payment complete';
            }    
            $sales->total_paid = $transaction['total_paid'];
            $sales->credit = $transaction['total_credited_to_merchant'];
            $sales->matureDate = $transaction['fund_maturity'];
            $sales->salesDate = $transaction['date'];
        } else {
            $sales->remark = $transaction['status'];
        }

        $sales->gateway = 'VoguePay';
        $sales->update('false');
        return true;
    }
    
    public function topUpUnit($param) {
        //send email to admin, user and do any other stuffs that a paid clients or so has right to.
    }
}
