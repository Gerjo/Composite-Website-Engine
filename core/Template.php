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

    private function apply($html, $composite) {
        preg_match_all('#\[get([a-z]{1,})\]#i', $html, $keys);

        foreach($keys[1] as $k => $v) {
            $method = "get" . $v;

            if(is_callable(array(get_class($composite), $method))) {
                $data = $composite->$method();
                $html = str_replace($keys[0][$k], $data, $html);
            }
        }

        foreach(array("has", "can", "is", "enable", "should") as $prefix) {

            preg_match_all('#\[' . $prefix . '([a-z]{1,})\]#i', $html, $keys);

            foreach($keys[1] as $k => $v) {
                $method = $prefix . $v;
                $open   = '[' . $method . ']';
                $close  = '[/' . $method . ']';
                $regex  = '#' . preg_quote($open) . '(.+?)' . preg_quote($close) . '#s';
                
                while(preg_match($regex, $html, $htmlMatches) == 1) {
                    if(is_callable(array(get_class($composite), $method))) {
                        if(true === $composite->$method()) {
                            $html = preg_replace($regex,  $htmlMatches[1], $html);
                        } else {
                            $html = preg_replace($regex, "", $html);
                        }
                    }
                }
            }
        }
    
        return $html;
    }
    
    private function injectComponent(Composite $composite) {
        preg_match_all('#\[iterator\ ([a-z]{1,})\]#i', $this->html, $keys);

        foreach($keys[1] as $k => $v) {
            if(empty($v)) continue;
            
            $method = $v;
            $open   = '[iterator ' . $method . ']';
            $close  = '[/iterator ' . $method . ']';
            $regex  = '#' . preg_quote($open) . '(.+?)' . preg_quote($close) . '#s';
            
            preg_match($regex, $this->html, $out);
            $source    = $out[1];
            $formatted = "";
            
            $iterator  = $composite->$method();
            
            foreach($iterator as $k => $v) {
                $formatted .= $this->apply($source, $v);
            }
            
            $this->html = str_ireplace($out[0], $formatted, $this->html);
        }
        
        $this->html = $this->apply($this->html, $composite);
 
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
