<?php

class Document {
    private $head;
    private $body;
    private $javascripts;
    private $styleSheets;
    
    
    public function __construct() {
        $this->head = "";
        $this->body = "";
        
        $this->javascripts = array();
        $this->styleSheets = array();
    }
    
    public function addStylesheet($path) {
        $this->styleSheets[$path] = $path;
    }
    
    public function addJavascript($path) {
        $this->javascripts[$path] = $path;
    }
    
    public function appendHead($html) {
        $this->head .= $html;
    }
    
    public function appendBody($html) {
        $this->body .= $html;
    }
    
    public function serialize() {
        // TODO: some clever cache system here?
    }
    
    public function toHtml() {
        $html = "";
        
        // Needs some fine grain tweaking.
        $html .= "<!DOCTYPE html>" . PHP_EOL;
        $html .= "<html><head>";
        
        foreach($this->javascripts as $src) {
            $html .= '<script src="' . $src . '"></script>';
        }
        
        foreach($this->styleSheets as $src) {
            $html .= '<link href="' . $src . '" rel="stylesheet" type="text/css">';
        }
        
        $html .= $this->head;
        $html .= "</head><body>";
        $html .= $this->body;
        $html .= "</body></html>";
        
        return $html;
    }
    
    public function __toString() {
        return $this->toHtml();
    }
}