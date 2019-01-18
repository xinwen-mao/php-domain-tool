<?php
/**
 * Created by PhpStorm.
 * User: xinwenmao
 * Date: 2019/1/18
 * Time: 01:48
 */
header('Content-type: text/html; charset=UTF-8');

/**
 * 阿里云域名
 * Class AliyunDomain
 */
class AliyunDomain {

    /**
     * token
     * @var string
     */
    private $token = 'Yf3c3f5bc8817307613926e1f40a9941d';

    /**
     * 实例
     * @var AliyunDomain
     */
    private static $_instance = null;

    /**
     * 禁止使用魔术方法进行实例化
     * AliyunDomain constructor.
     */
    private function __construct() {}

    /**
     * 禁止使用魔术方法进行克隆
     */
    private function __clone() {}

    /**
     * 获取实例
     * 使用单例模式
     * @return AliyunDomain
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * CLI模式下运行
     */
    public function run() {
        try {
            $opt = getopt('n:');
            if (!array_key_exists('n', $opt))
                throw new Exception(sprintf('缺少参数，使用示例：php %s -n baidu.com', basename(__FILE__)));

            $name = trim($opt['n']);
            if (2 != count(explode('.', $name)))
                throw new Exception('参数错误');

            if (1 == $this->checkDomain($name)) {
                self::_output(0, "{$name} 可注册", [$name]);
            } else {
                self::_output(1, "{$name} 已被注册");
            }

        } catch (Exception $e) {
            self::_output(1, $e->getMessage());
        }
    }

    /**
     * 检查域名
     * 开放给外调用
     * @param $name
     * @return bool
     * @throws Exception
     */
    public function checkDomain($name) {
        if (empty($name))
            throw new Exception('缺少参数');

        // 接口抓取阿里云域名查询的，token有效期暂不清楚，该类已封装获取token方法，另外经测试token为空也可调用该接口。
        $url = "https://checkapi.aliyun.com/check/checkdomain?domain={$name}&command=&token={$this->token}&ua=&currency=&site=&bid=&_csrf_token=&callback=";
        $result = file_get_contents($url);
        if (empty($result))
            throw new Exception('接口异常，可能token已经失效');

        $json = json_decode($result, true);
        if (!is_array($json))
            throw new Exception('解析接口数据异常');

        if (!array_key_exists('module', $json) || !array_key_exists('0', $json['module']))
            throw new Exception('接口可能失效');

        if (-1 == $json['module'][0]['avail'])
            throw new Exception("请求参数错误：" . $json['module'][0]['reason']);

        return 1 == $json['module'][0]['avail'] ? true : false;
    }

    /**
     * 获取token
     * 开放给外调用
     * @return mixed
     * @throws Exception
     */
    public static function getToken() {
        $url    = 'https://promotion.aliyun.com/risk/getToken.htm?cback=&_=' . self::_getMillisecond();
        $result = json_decode(file_get_contents($url), true);
        if (!array_key_exists('data', $result))
            throw new Exception('获取token失败：' . $result['msg']);
        return $result['data'];
    }

    /**
     * 输出
     * @param $code
     * @param $msg
     * @param $data
     */
    private static function _output($code, $msg, $data = null) {
        $arr = [
            'code' => $code,
            'msg'  => $msg,
        ];
        if ($data) $arr['data'] = $data;
        echo json_encode($arr, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
    }

    /**
     * 获取13位时间戳
     * @return float
     */
    private static function _getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

}

(AliyunDomain::getInstance())->run();