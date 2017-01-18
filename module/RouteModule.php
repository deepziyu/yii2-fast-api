<?php

namespace deepziyu\yii\rest\module;

use yii\base\Exception;

class RouteModule extends \yii\base\Module
{
    public $controllerNamespace = 'deepziyu\yii\rest\module\controllers';

    public function init()
    {
        if(!YII_DEBUG){
            throw new Exception('only accessed on debug evn!',500);
        }
        parent::init();

        // custom initialization code goes here
    }
}
