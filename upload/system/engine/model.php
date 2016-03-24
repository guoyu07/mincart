<?php

/**
 * 模型基类
 */
abstract class Model
{

    /**
     *
     * @var Registry 
     */
    protected $registry;

    /**
     * 
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->registry->get($key);
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

}
