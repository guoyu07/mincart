<?php

/**
 * 前端控制器
 */
final class Front
{
    /**
     *
     * @var Registry 
     */
    private $registry;
    
    /**
     *
     * @var array 
     */
    private $pre_action = array();
    
    /**
     *
     * @var Action 
     */
    private $error;

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
     * @param Action $pre_action
     * @return void
     */
    public function addPreAction(Action $pre_action)
    {
        $this->pre_action[] = $pre_action;
    }

    /**
     * 
     * @param Action $action
     * @param Action $error
     * @return void
     */
    public function dispatch(Action $action, Action $error)
    {
        $this->error = $error;

        foreach ($this->pre_action as $pre_action) {
            $result = $this->execute($pre_action);

            if ($result instanceof Action) {
                $action = $result;

                break;
            }
        }

        while ($action instanceof Action) {
            $action = $this->execute($action);
        }
    }

    /**
     * 执行指定动作
     * @param Action $action
     * @return \Action
     */
    private function execute(Action $action)
    {
        $result = $action->execute($this->registry);

        if ($result instanceof Action) {
            return $result;
        }

        if ($result instanceof Exception) {
            $action = $this->error;

            $this->error = null;

            return $action;
        }
    }

}
