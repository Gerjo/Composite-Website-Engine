<?php

class Template {
    private $fileName;
    private $html;
    
    public function __construct($templateFile = null) {
        $this->fileName = $templateFile;
        $this->html     = null;
        
        if($templateFile !== null) {
            $this->html = file_get_contents($templateFile);
            
            if(false === $this->html) {
                throw new Exception("Unable to open template file for reading.");
            }
        }
    }
    
    public function injectArray(array $data) {
        foreach($data as $k => $v) {
            $this->html = str_replace("[" . $k . "]", $v, $this->html);
        }
        
        return $this;
    }
    
    // TODO variable calling and existance checking.
    public function injectComponent(Composite $composite) {
        preg_match_all('#\[([a-z]{1,})\]#i', $this->html, $keys);
        
        foreach($keys[1] as $k => $v) {
            $data       = $composite->$v();
            $this->html = str_replace($keys[0][$k], $data, $this->html);
        }
        
        return $this;
    }
    
    public function getHtml() {
        return $this->html;
    }
    
    public function setHtml($html) {
        $this->html = $html;
        return $this;
    }
    
    public function __clone() {
        $clone = new Template();
        $clone->setHtml($this->getHtml());
        
        return $clone;
    }
}