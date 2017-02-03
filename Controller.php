<?php
namespace deepziyu\yii\rest;

use Yii;
use yii\data\ActiveDataProvider;
use yii\base\InlineAction;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\BadRequestHttpException;
use yii\validators\Validator;
use yii\base\DynamicModel;
use yii\base\Model;
use deepziyu\yii\rest\ApiException;
use yii\web\User;

/**
 * Class Controller
 * @property Request $request The request component.
 * @property User $user The user model.
 * @package deepziyu\yii\rest
 */
class Controller extends \yii\rest\Controller
{
    public $request;
    public $user;

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
    }

    /**
     * 默认的action为空
     * @return array
     */
    public function actions()
    {
        return [];
    }

    /**
     * 参数的检验规则
     * 次方法返回的预设规则将在beforeAction事件中被校验
     * - 简单示例：
     * ```php
     * return [
     *    //设置 indexAction()的rule
     *    'index' => [
     *         ['param1','string','min'=>1,'max'=>6],
     *         ['param2','integer'],
     *    ];
     * ];
     * ```
     * - 可以用 * 号通配所有的actions
     * ```php
     * return [
     *    '*' => [
     *         ['user_id','integer']//所有的user_id都将被IntegerVa校验
     *    ];
     * ];
     * ```
     * - 可以 指定到某个 model 的 rules
     * ```php
     * return [
     *    //设置并唯一用 YouModel::rules() 检验路由 index
     *    'index' => 'app\modes\YouModel';
     * ];
     * ```
     * - 更多检验器的设置方法见
     * http://www.yiichina.com/doc/guide/2.0/input-
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * 获取api-config
     * @return mixed
     */
    public static function getConfig()
    {
        return require(__DIR__ . '/api.config.php');
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

        $params = array_merge($params, $this->request->getBodyParams());

        $args = [];
        $missing = [];
        $actionParams = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = $actionParams[$name] = (array)$params[$name];
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

        $rule = $this->getRule($action);
        if ($rule) {
            if($rule instanceof Model){
                $model = $rule;
                $model->load($actionParams,'');
            }else{
                $model = DynamicModel::validateData($actionParams, $rule);
            }
            $model->validate();
            if ($model->hasErrors()) {
                throw new ApiException(422, $model);
            }
            $actionParams = $model->getAttributes();
        }

        $this->actionParams = $actionParams;

        return $actionParams;
    }

    /**
     * 获取action对应的rule规则
     * @param \yii\base\Action $action $action
     * @return array|\yii\base\Model
     */
    protected function getRule($action)
    {
        $rules = $this->rules();
        $commonRule = isset($rules['*']) ? $rules['*'] : [];
        $uniqueRule = isset($rules[$action->id]) ? $rules[$action->id] : [];
        if (is_string($uniqueRule) || (is_array($uniqueRule) && isset($uniqueRule['class']))) {
            /* @var $model \yii\base\Model */
            $model = Yii::createObject($uniqueRule);
            //$uniqueRule = $model->rules();
            return $model;
        }
        return array_merge($commonRule, $uniqueRule);
    }

    /**
     * 设置expand
     * 详见 \yii\base\Model::toArray() 的介绍
     * @param Model $expand
     */
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

    /**
     * 简单构造一个 DataProvider 用以返回数据
     * @param $query
     * @return ActiveDataProvider
     */
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

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return array|mixed
     * @throws \deepziyu\yii\rest\ApiException
     */
    public function afterAction($action, $result)
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        if ($result instanceof Model && $result->hasErrors()) {
            throw new \deepziyu\yii\rest\ApiException(422, $result);
        }
        $result = parent::afterAction($action, $result);
        $code = $response->getStatusCode();
        $result = [
            'code' => $code,
            'data' => $result,
            'message' => $response->statusText
        ];
        Yii::info('请求返回结果：' . \yii\helpers\Json::encode($result), 'response');
        return $result;
    }
}