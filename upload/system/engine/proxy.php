<?php

/**
 * 代理
 */
class Proxy
{

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->{$key};
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }

    /**
     * 未通过代理定义的方法如果被执行将会导致exit
     * @param string $key
     * @param array $args
     * @return mixed
     */
    public function __call($key, $args)
    {
        if (isset($this->{$key})) {
            return call_user_func($this->{$key}, $args);
        } else {
            $trace = debug_backtrace();

            exit('<b>Notice</b>:  Undefined property: Proxy::' . $key . ' in <b>' . $trace[1]['file'] . '</b> on line <b>' . $trace[1]['line'] . '</b>');
        }
    }

}
