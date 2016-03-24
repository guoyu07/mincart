<?php

/**
 * 资源加载器
 */
final class Loader
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
     * 加载并执行指定控制器
     * @param string $route
     * @param array $data
     * @return mixed
     */
    public function controller($route, array $data = [])
    {
        // Sanitize the call
        $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string) $route);

        // Trigger the pre events
        $result = $this->registry->get('event')->trigger('controller/' . $route . '/before', array(&$route, &$data));

        if ($result) {
            return $result;
        }

        $action = new Action($route);
        $output = $action->execute($this->registry, [&$data]);

        // Trigger the post events
        $result = $this->registry->get('event')->trigger('controller/' . $route . '/after', array(&$route, &$data, &$output));

        if (!($output instanceof Exception)) {
            return $output;
        } else {
            return false;
        }
    }

    /**
     * 加载指定模型并将实例化对象存入全局对象中
     * @param string $route
     * @throws \Exception
     */
    public function model($route)
    {
        // Sanitize the call
        $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string) $route);

        $file = DIR_APPLICATION . 'model/' . $route . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

        if (is_file($file)) {
            include_once($file);
            //echo $class;
            $proxy = new Proxy();

            foreach (get_class_methods($class) as $method) {
                $proxy->{$method} = $this->callback($this->registry, $route . '/' . $method);
            }

            $this->registry->set('model_' . str_replace(['/', '-', '.'], ['_', '', ''], (string) $route), $proxy);
        } else {
            throw new \Exception('Error: Could not load model ' . $route . '!');
        }
    }

    /**
     * 加载并渲染执行视图文件
     * @param string $route
     * @param array $data
     * @return string
     */
    public function view($route, array $data = [])
    {
        // Sanitize the call
        $route = str_replace('../', '', (string) $route);

        // Trigger the pre events
        $result = $this->registry->get('event')->trigger('view/' . $route . '/before', [&$route, &$data]);

        if ($result) {
            return $result;
        }

        $template = new Template('basic');

        foreach ($data as $key => $value) {
            $template->set($key, $value);
        }

        $output = $template->render($route . '.tpl');

        // Trigger the post e
        $result = $this->registry->get('event')->trigger('view/' . $route . '/after', [&$route, &$data, &$output]);

        if ($result) {
            return $result;
        }

        return $output;
    }

    /**
     * 加载指定库并将实例化对象存入registry中
     * @param string $route
     * @throws \Exception
     */
    public function library($route)
    {
        // Sanitize the call
        $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string) $route);

        $file = DIR_SYSTEM . 'library/' . $route . '.php';
        $class = str_replace('/', '\\', $route);

        if (is_file($file)) {
            include_once($file);

            $this->registry->set(basename($route), new $class($this->registry));
        } else {
            throw new \Exception('Error: Could not load library ' . $route . '!');
        }
    }

    /**
     * 加载指定助手函数文件
     * @param string $route
     * @throws \Exception
     */
    public function helper($route)
    {
        $file = DIR_SYSTEM . 'helper/' . str_replace('../', '', (string) $route) . '.php';

        if (is_file($file)) {
            include_once($file);
        } else {
            throw new \Exception('Error: Could not load helper ' . $route . '!');
        }
    }

    /**
     * 读取指定配置项
     * @param string $route
     */
    public function config($route)
    {
        $this->registry->get('event')->trigger('config/' . $route . '/before', $route);

        $this->registry->get('config')->load($route);

        $this->registry->get('event')->trigger('config/' . $route . '/after', $route);
    }

    /**
     * 读取指定语言项
     * @param string $route
     * @return string
     */
    public function language($route)
    {
        $this->registry->get('event')->trigger('language/' . $route . '/before', $route);

        $output = $this->registry->get('language')->load($route);

        $this->registry->get('event')->trigger('language/' . $route . '/after', $route);

        return $output;
    }

    /**
     * 利用PHP闭包特性实现对模型方法执行结果的回调处理
     * @param Registry $registry
     * @param string $route
     * @return mixed
     */
    protected function callback($registry, $route)
    {
        return function($args) use($registry, &$route) {
            // Trigger the pre events
            $result = $registry->get('event')->trigger('model/' . $route . '/before', array_merge(array(&$route), $args));

            if ($result) {
                return $result;
            }

            $file = DIR_APPLICATION . 'model/' . substr($route, 0, strrpos($route, '/')) . '.php';
            $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', substr($route, 0, strrpos($route, '/')));
            $method = substr($route, strrpos($route, '/') + 1);

            if (is_file($file)) {
                include_once($file);

                $model = new $class($registry);
            } else {
                throw new \Exception('Error: Could not load model ' . substr($route, 0, strrpos($route, '/')) . '!');
            }

            if (method_exists($model, $method)) {
                $output = call_user_func_array(array($model, $method), $args);
            } else {
                throw new \Exception('Error: Could not call model/' . $route . '!');
            }

            // Trigger the post events
            $result = $registry->get('event')->trigger('model/' . $route . '/after', array_merge(array(&$route, &$output), $args));

            if ($result) {
                return $result;
            }

            return $output;
        };
    }

}
