<?php
namespace deepziyu\yii\rest;

use yii\base\Exception as BaseException;
use yii\base\UserException as Exception;
use yii\base\Model;

/**
 *
 * 非系统原因报错，支持返回数组和字符串错误信息
 */
class ApiException extends Exception
{
    public $model;

    public function __construct($code, $message = '', \Exception $previous = null)
    {
        //parent::__construct($message, $code, $previous);
        if (is_array($message)) {
            $this->arrMessage = $message;
            $error = array_shift($message);
            $this->message = is_array($error) ? $error[0] : $error;
        } else if (is_string($message)) {
            $this->message = $message;
        } else if ($message instanceof Model) {
            $this->model = $message;
        } else {
            throw new BaseException('未知错误。', 500);
        }
        if (empty($this->message) && in_array($code, array_keys(self::$codesList))) {
            $this->message = self::getCodeMessage($code);
        }
        $this->code = $code;
    }

    public function getName()
    {
        return 'UserException';
    }

    public static $codesList = [
        //基础的错误
        400 => '参数错误。',
        401 => '用户未登陆。',
        404 => '服务不存在。',
        500 => '未知错误。',
        501 => '服务器繁忙，请稍后重试。',//用于限流的时候，用于数据库异常
    ];

    /**
     * 获取默认的报错信息
     * @return string
     */
    public static function getCodeMessage($code = 500)
    {
        if (!isset(self::$codesList[$code])) {
            $code = 500;
        }
        return self::$codesList[$code];
    }
}
