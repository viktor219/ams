<?php

namespace app\vendor\mobiledetect;

use yii\base\Component;

require_once(\Yii::getAlias('@vendor/mobiledetect/Mobile_Detect.php'));

class MobileDetect extends Component
{
    protected $mobileDetect;

    public function init()
    {
        parent::init();
        $this->mobileDetect = new \Mobile_Detect();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->mobileDetect, $name], $arguments);
    }
}