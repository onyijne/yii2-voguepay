<?php

namespace tecsin\pay2\traits;

use Yii;
use tecsin\pay2\Module;

/**
 * Class ModuleTrait
 *
 * @package tecsin\pay2\traits
 */
trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('pay2');
    }
}
