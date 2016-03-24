<?php

/**
 * 对象登记簿
 */
final class Registry
{

    private $data = [];

    /**
     * 取
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    /**
     * 存
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * 判断
     * @param string $key
     * @return mixed
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

}
