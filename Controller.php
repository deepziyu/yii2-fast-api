<?php
namespace deepziyu\yii\rest;

use Yii;
use yii\data\ActiveDataProvider;
use yii\base\InlineAction;
use yii\web\Request;
use yii\web\BadRequestHttpException;
use yii\validators\Validator;
use yii\base\DynamicModel;
use yii\base\Model;

/**
 * Class Controller
 * @property Request $request The request component.
 * @property Validator $validator The validator component.
 * @package deepziyu\yii\rest
 */
class Controller extends \yii\rest\Controller
{
    public $request;
    public $user;
    public $validator;

    public $enableCsrfValidation = false;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function init()
    {
        parent::init();
        $this->user = Yii::$app->user->identity;
        $this->request = Yii::$app->getRequest();
        $this->validator = Yii::createObject([
            'class'=>'yii\validators\Validator',
            'enableClientValidation' =>false,
        ]);
    }

    public function actions()
    {
        return [];
    }

    public function __get($name)
    {
        return parent::__get($name);
    }

    public function rules()
    {
        return [];
    }

    public static function getConfig(){
        return require (__DIR__.'/api.config.php');
    }

    /**
     * action参数注入
     * @param \yii\base\Action $action
     * @param array $params
     * @return array
     * @throws BadRequestHttpException
     * @throws ApiException
     */
    public function bindActionParams($action, $params)
    {
        if ($action instanceof InlineAction) {
            $method = new \ReflectionMethod($this, $action->actionMethod);
        } else {
            $method = new \ReflectionMethod($action, 'run');
        }

        $params = array_merge($params,$this->request->getBodyParams());

        $rule = $this->getRule($action);
        if ($rule) {
            $model = new DynamicModel($params,$rule);
            $model->getValidators();
            $model->validate();
            if($model){
                throw new ApiException(422,$model);
            }
            $params = $model->getAttributes();
        }

        $args = [];
        $missing = [];
        $actionParams = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = $actionParams[$name] = (array) $params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $actionParams[$name] = $params[$name];
                } else {
                    throw new BadRequestHttpException(Yii::t('yii', 'Invalid data received for parameter "{param}".', [
                        'param' => $name,
                    ]));
                }
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $actionParams[$name] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => implode(', ', $missing),
            ]));
        }

        $this->actionParams = $actionParams;

        return $args;
    }

    /**
     * @param \yii\base\Action $action $action
     * @return array
     */
    protected function getRule($action)
    {
        $rules = $this->rules();
        $commonRule = isset($rules['*']) ? $rules['*'] :  [];
        $uniqueRule = isset($rules[$action->id]) ? $rules[$action->id] : [];
        return array_merge($commonRule,$uniqueRule);
    }

    public function setExpand($expand)
    {
        $params = Yii::$app->request->getQueryParams();
        if (!is_array($expand)) {
            $expand = [$expand];
        }
        if (isset($params['expand'])) {
            $params['expand'] .= ',' . implode(',', $expand);
        } else {
            $params['expand'] = implode(',', $expand);
        }

        Yii::$app->request->setQueryParams($params);
    }

    public function getActiveDataProvider($query)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($dataProvider->models) {
            foreach ($dataProvider->models as $key => &$m) {
                if (isset($m->_id)) {
                    $m->_id = (string)$m->_id;
                }
            }
        }
        return $dataProvider;
    }


    /**
     * @param $action
     * @return bool
     * @throws Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Yii::info('请求地址：' . $this->request->absoluteUrl, 'request');
            Yii::info('请求数据：' . \yii\helpers\Json::encode($this->request->getBodyParams()), 'request');
        } else {
            return false;
        }
        return true;
    }

    public function afterAction($action, $result)
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        if ($result instanceof Model && $result->hasErrors()) {
            throw new \deepziyu\yii\rest\ApiException(422,$result);
        }
        $result = parent::afterAction($action, $result);
        $code = $response->getStatusCode();
        $result = [
            'code'=>$code,
            'data'=>$result,
            'message'=>$response->statusText
        ];
        Yii::info('请求返回结果：' . \yii\helpers\Json::encode($result), 'response');
        return $result;
    }
}