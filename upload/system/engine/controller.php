<?php

/**
 * 控制器基类
 * 
 * @property Loader $load
 * @property Language $language
 * @property Config $config
 * @property Request $request
 * @property Response $response
 * @property Url $url Url对象
 * @property DB $db DB对象
 * @property Session $session Session对象
 * @property Cache $cache Cache对象
 * @property Event $event Event对象
 * @property Document $document
 */
abstract class Controller
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
