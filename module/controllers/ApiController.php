<?php
namespace deepziyu\yii\rest\module\controllers;

use deepziyu\yii\rest\models\Route;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class ApiController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = 'html';
    }

    /**
     * Displays homepage.
     * @param int $id
     * @return array
     */
    public function actionIndex()
    {
        $model = new Route();

        $routes =  $model->getRoutes();
        unset($routes['/*']);
        foreach ($routes as $key =>  &$route) {
            if(preg_match('#^/route|gii|debug/#',$key,$m)){
                unset($routes[$key]);
            }
        }
        $routes = array_reverse($routes);
        return $this->renderPartial('index',[
            'routes'=>$routes
        ]);
    }

    public function actionRoutes()
    {

    }

}
