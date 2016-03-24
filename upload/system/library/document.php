<?php

/**
 * HTML页面文档类
 */
class Document
{

    /**
     *
     * @var string 页面标题
     */
    private $title;
    
    /**
     *
     * @var string 页面描述
     */
    private $description;
    
    /**
     *
     * @var string 页面关键词
     */
    private $keywords;
    
    /**
     *
     * @var array 资源链接
     */
    private $links = [];
    
    /**
     *
     * @var array 样式文件
     */
    private $styles = [];
    
    /**
     *
     * @var array 脚本文件
     */
    private $scripts = [];

    /**
     * 
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * 
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * 
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * 
     * @param string $href
     * @param string $rel
     */
    public function addLink($href, $rel)
    {
        $this->links[$href] = [
            'href' => $href,
            'rel' => $rel
        ];
    }

    /**
     * 
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * 
     * @param string $href
     * @param string $rel
     * @param string $media
     */
    public function addStyle($href, $rel = 'stylesheet', $media = 'screen')
    {
        $this->styles[$href] = [
            'href' => $href,
            'rel' => $rel,
            'media' => $media
        ];
    }

    /**
     * 
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * 
     * @param string $href
     * @param string $postion
     */
    public function addScript($href, $postion = 'header')
    {
        $this->scripts[$postion][$href] = $href;
    }

    /**
     * 
     * @param string $postion
     * @return array
     */
    public function getScripts($postion = 'header')
    {
        if (isset($this->scripts[$postion])) {
            return $this->scripts[$postion];
        } else {
            return [];
        }
    }

}
