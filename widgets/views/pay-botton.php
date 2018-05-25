<?php
/* @var $model \tecsin\pay2\models\Money */
/* @var $show_input boolean wheather to show total input field or not */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap4\Html as Html4;
use yii\helpers\Url;

if($useBootstrap4){
    $from = yii\bootstrap4\ActiveForm::begin(['id'=>'voguepay-form']);
    $submitButton = Html4::submitButton('Submit', ['class' => 'btn btn-success btn-md submit-button']);
    $inputTemplate = '<div class="input-group"><span class="input-group-prepend"><span class="input-group-addon">&#8358;</span></span>{input}</div>';
} else{
    $form =  ActiveForm::begin(['id'=>'voguepay-form']);
    $inputTemplate = '<div class="input-group"><span class="input-group-addon">&#8358;</span>{input}</div>';
    $submitButton = Html::submitButton('Submit', ['class' => 'btn btn-success btn-md submit-button']);
}
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-sm-6 col-xs-12">
                <?php 
                    echo $form->field($model, 'v_merchant_id')->hiddenInput()->label(false);
                    echo $form->field($model, 'merchant_ref')->hiddenInput()->label(false);
                    echo $form->field($model, 'user_id')->hiddenInput()->label(false);
                    echo $form->field($model, 'memo')->hiddenInput()->label(false);
                    if($model->_pay2Setup){
                        echo ($model->_pay2Setup->success_url) ? $form->field($model, 'success_url')->hiddenInput()->label(false) : '';
                        echo ($model->_pay2Setup->failure_url) ? $form->field($model, 'failure_url')->hiddenInput()->label(false) : '';
                        if(!$model->_pay2Setup->merchant_id){
                            echo Html::hiddenInput('hidden', '', ['id'=>'total'])->label(false).'<div class="alert alert-info">Missing merchant_id</div>';
                        } else {
                            echo (!$show_input) ? $form->field($model, 'total')->hiddenInput(['value' => $model->total])->label(false) : 
                                $form->field($model, 'total',['inputTemplate' => $inputTemplate])->input('number')->label(false);
                            echo '<div class="form-group">'.$submitButton.'</div>';
                        }
                    }                    
                    ActiveForm::end();
                ?>
                <p class="muted card-text">Payments are processed by  <?php echo \yii\helpers\Html::a('VoguePay', 'https://voguepay.com/13227-11873', ['class'=>'link']) ?></p>
            </div>
            
            <div class="col-md-7 col-lg-8 col-sm-6 col-xs-12">
                <?php 
                    if($model->_pay2Setup){
                        echo '<div class="alert alert-info"> <span class="fa fa-info-circled"></i> '.$model->_pay2Setup->payment_instruction.'</div>';
                       echo '<strong>Bank Name: </strong>'.$model->_pay2Setup->bank_name. '<br>
                        <strong>Account Name: </strong>'. $model->_pay2Setup->bank_account_name.'<br>
                        <strong>Account Number :</strong>'. $model->_pay2Setup->bank_account_number;
                    }
                    
                ?>
            </div>
        </div>
    </div>
</div>
                    
<?php
$link = Url::toRoute(['/site/init-sale']);

$js = <<< JS
   //'use strict';
     
    let variable_set_link = '{$link}';
    let show_input = {$show_input};
    const voguePay = {
        variable_link : () => {return variable_set_link;},
        form : function(){return document.getElementById('v-form');},
        memo : () => {return document.getElementById('memo').value;},
        amount : () => {return document.getElementById('total').value;},
        successCallback : data => {return success(data);},
        submitButton : function(){return this.form().querySelector('.submit-button');}
    };
    voguePay.submitButton().addEventListener('click', event => {
        event.preventDefault();  
        voguePay.submitButton().setAttribute('disabled', '');        
        voguePay.submitButton().textContent = 'Working...';
        if(show_input === 1){
            document.getElementById('memo').value = voguePay.memo() + ' at ' +voguePay.amount();
        }        
       let obj = {};
       let objs = voguePay.form().querySelectorAll("input");
       objs.map(function (ele){
            obj[ele.name] = ele.value;
        });

        axios({
                url: voguePay.variable_link(),
                data: obj,
                method: "POST",
            }).then(function(data){   
                return voguePay.successCallback(data);
            }).catch(function(error) {
                alert( "error, try again" );
                voguePay.submitButton().removeAttribute('disabled');
                voguePay.submitButton().textContent = 'Submit';
                return false;
        });        
            return false;
   });
   function success(jsonData){       
        let obj = jsonData.data;
        if(obj.status === 'invalid'){
            alertNode(obj.message, 'alert alert-info');
            voguePay.submitButton().removeAttribute('disabled');
            voguePay.submitButton().textContent = 'Submit';
            return false;
        }
       alertNode(obj.message, 'alert alert-info');
       return voguePay.form().dispatchEvent('submit');
    }
   function alertNode(msg, alertType){
        let g = document.createElement('div');
        g.setAttribute('class', alertType);
        g.textContent = msg;
        voguePay.form().parentNode.insertBefore(g, voguePay.form());
    }
JS;
$this->registerJs($js);
