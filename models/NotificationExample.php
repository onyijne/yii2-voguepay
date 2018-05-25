<?php

/*
 * Copyright (c) Sajflow 2016.
 * please see the LICENSE.md file for license information
 * 
 */

namespace tecsin\pay2\models;

use tecsin\pay2\models\Pay2Sales;

/**
 * Description of NotificationExample
 *
 * @author Samuel Onyijne <samuel@sajflow.com>
 */
class NotificationExample  {
    
    /**
     * 
     * @param array $transaction
     * @return boolean
     */
     public function voguepay($transaction ) {
        $ref = $transaction['merchant_ref'];
        if(($sales = Pay2Sales::findOne(['ref'=> $ref])) == null){
            $sales = new Pay2Sales([
                'memo' => $transaction['memo'],
                'ref' => $transaction['ref'],
                'total' => $transaction['total']
                ]);
        } 
        if(!$sales->isNewRecord){
            if($sales->remark == 'Payment complete'){
               //to avoid crediting a transaction twice
                return true;
            }
        }
        
        if($transaction['status'] == 'Approved'){
            if($transaction['total'] !== $sales->total){
                    //it is not a new record and total did not match
                $sales->remark = 'Paid '.$transaction['total'].' instead of '.$sales->total;
            } else {
                $this->topUpUnit('something to query with');        
                $sales->remark = 'Payment complete';
            }                
               
            $sales->total_paid = $transaction['total_paid'];
            $sales->received_amount = $transaction['total_credited_to_merchant'];
            $sales->mature_date = $transaction['fund_maturity'];            
            $sales->extra_charges = $transaction['extra_charges'];           
        } else {
            $sales->remark = $transaction['status'];
        }
        $sales->referrer = $transaction['referrer'];
        $sales->transaction_date = $transaction['date'];
        $sales->gateway = $transaction['gateway'];
        $sales->save(false);
        return true;
    }
    
    public function topUpUnit($param) {
        //send email to admin, user and do any other stuffs that a paid clients or so has right to.
    }
}
