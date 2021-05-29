<?php

namespace Trink\Core\Helper;

use JsonException;
use ReflectionObject;

/**
 * Class Result
 * @package Trink\Core\Helper
 * @author  trink
 */
class Result
{
    /** @var int $status 状态码 */
    protected int $status;

    /** @var string $msg 返回信息 */
    protected string $msg;

    /** @var array $data 数据 */
    protected array $data;

    /** @var array $debug 错误处理 */
    protected array $debug = [];

    public const STATUS_SUCCESS = 1;
    public const STATUS_ERROR = 0;

    /**
     * @param string $msg
     * @param array  $data
     * @param int    $status
     */
    protected function __construct(string $msg, array $data = [], int $status = self::STATUS_SUCCESS)
    {
        $this->status = $status;
        $this->msg = $msg;
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->asCamelJson();
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param string $msg
     *
     * @return $this
     */
    public function setMsg(string $msg): Result
    {
        $this->msg = $msg;
        return $this;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data): Result
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getDebug(): array
    {
        return $this->debug;
    }

    /**
     * @param array $debug
     *
     * @return Result
     */
    public function setDebug(array $debug): Result
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * 无此键会自动创建此键
     *
     * @param string $key
     * @param mixed  $data
     *
     * @return $this
     */
    public function setDataByKey(string $key, $data): Result
    {
        $this->data = Arrays::set($this->data, $key, $data);
        return $this;
    }

    public function getDataByKey(string $key, $default = null)
    {
        return Arrays::get($this->data, $key, $default);
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function addData(array $data): Result
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->data['list'] ?? [];
    }

    /**
     * @param array $list
     *
     * @return $this
     */
    public function setList(array $list): Result
    {
        $this->data['list'] = $list;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->status !== self::STATUS_SUCCESS;
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        $object = new ReflectionObject($this);
        $fieldList = $object->getProperties();
        $fieldNameList = array_column($fieldList, 'name');

        $properties = [];
        foreach ($fieldNameList as $fieldName) {
            $properties[$fieldName] = $this->$fieldName;
        }
        return $properties;
    }

    /**
     * @return string
     */
    public function asJson(): string
    {
        try {
            return (string)json_encode($this->asArray(), JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return '{}';
        }
    }

    /**
     * @return array
     */
    public function asCamelArray(): array
    {
        if (property_exists($this, 'data')) {
            $this->data = Format::array2CamelCase($this->data);
        }
        return $this->asArray();
    }

    /**
     * @return string
     */
    public function asCamelJson(): string
    {
        try {
            return (string)json_encode($this->asCamelArray(), JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return '{}';
        }
    }

    /**
     * 错误返回
     *
     * @param string $msg    返回消息
     * @param array  $debug  调试信息
     * @param int    $status 状态码
     *
     * @return $this
     */
    public static function fail(string $msg = 'FAIL', array $debug = [], int $status = self::STATUS_SUCCESS): Result
    {
        $result = new static($status, [], $msg);
        $result->setDebug($debug);
        return $result;
    }

    /**
     * 正常返回
     *
     * @param array  $data   返回数据
     * @param string $msg    返回消息
     * @param int    $status 状态码
     *
     * @return $this
     */
    public static function success(string $msg = 'OK', array $data = [], int $status = self::STATUS_SUCCESS): Result
    {
        return new static($msg, $data, $status);
    }

    /**
     * 列表成功返回
     *
     * @param array $list
     * @param int   $total
     * @param int   $currentPage
     * @param int   $pageSize
     *
     * @return $this
     */
    public static function lists(array $list, int $total, int $currentPage, int $pageSize = 15): Result
    {
        $totalPages = (int)ceil($total / $pageSize);
        return new static('OK', compact('list', 'currentPage', 'total', 'pageSize', 'totalPages'));
    }

    /**
     * 基础返回
     *
     * @param int    $status 状态码
     * @param string $msg    返回消息
     * @param array  $data   返回数据
     * @param array  $extra  扩展使用
     *
     * @return $this
     */
    public static function result(int $status, string $msg, array $data, array $extra = []): Result
    {
        $result = new static($msg, $data, $status);
        foreach ($extra as $key => $value) {
            $result->$key = $value;
        }
        return $result;
    }
}
