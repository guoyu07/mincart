<?php

/*
 * 事件系统处理类
 * 
 * Event机制用于解耦复杂相关逻辑代码
 * 
 * 简单举例如用户下单后发送通知邮件或通知短信这样的场景,下单是核心业务,但下单步骤完成后,有很多附加操作,是动态不确定的,如果直接硬编码到下单流程会影响代码的稳定性和可维护性,而通过Event机制,可以分离相关代码
 * 
 * Event System Userguide
 * 
 * https://github.com/opencart/opencart/wiki/Events-(script-notifications)-2.2.x.x
 */

class Event
{

    /**
     *
     * @var Registry 
     */
    protected $registry;
    
    /**
     *
     * @var array 
     */
    public $data = [];

    /**
     * 
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * 注册触发器
     * @param string $trigger
     * @param \Action $action
     * @return void
     */
    public function register($trigger, $action)
    {
        $this->data[$trigger][] = $action;
    }

    /**
     * 注销触发器
     * @param string $trigger
     * @param \Action $action
     * @return void
     */
    public function unregister($trigger, $action)
    {
        if (isset($this->data[$trigger])) {
            unset($this->data[$trigger]);
        }
    }

    /**
     * 触发事件执行
     * @param string $trigger
     * @param array $args
     * @return mixed
     */
    public function trigger($trigger, array $args = [])
    {
        foreach ($this->data as $key => $value) {
            if (preg_match('/^' . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($key, '/')) . '/', $trigger)) {
                foreach ($value as $event) {
                    $result = $event->execute($this->registry, $args);

                    if (!is_null($result) && !($result instanceof Exception)) {
                        return $result;
                    }
                }
            }
        }
    }

}
