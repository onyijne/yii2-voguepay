<?php

namespace tecsin\pay2\widgets;

/**
 * Description of PayButton
 *
 * @author samuel
 */
class PayButton extends \yii\base\Widget
{
    public $useBootstrap4 = true;
    
    const SHOW_INPUT = 1;
    
    const HIDE_INPUT = 0;

    public $show_input = false;

    public $viewFile = '@app/modules/yii2-voguepay/widgets/views/pay-botton';

    public function init() {
        parent::init();
        if(!class_exists(\yii\bootstrap4\ActiveForm::class)){
            $this->useBootstrap4 = false;
        } 
        $this->registerAssets();
    }
    
    public function run() {
        return $this->render($this->viewFile, [
            'useBootstrap4' => $this->useBootstrap4,
            'show_input' => ($this->show_input) ? self::SHOW_INPUT : self::HIDE_INPUT,
        ]);
    }
    
    public function registerAssets()
    {
        return $this->view->registerJsFile('https://unpkg.com/axios/dist/axios.min.js');
    }
}
