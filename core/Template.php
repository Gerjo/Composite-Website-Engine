<?php

final class Template extends Composite {
    private $fileName;
    private $html;
    
    public function __construct($templateFile = null) {
        parent::__construct();
        
        $this->fileName = $templateFile;
        $this->html     = null;
        
        if($templateFile !== null) {
            $this->html = file_get_contents($templateFile);
            
            if(false === $this->html) {
                throw new Exception("Unable to open template file for reading.");
            }
        }
    }
    
    // TODO variable calling and existance checking.
    private function injectComponent(Composite $composite) {
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
    
    public function onRender(ClientRequest $request, Document $document) {
        $this->injectComponent($this->getParent());
        $document->addTemplate($this);
    }
}