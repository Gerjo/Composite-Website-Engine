<?php

class Document {
    private $head;
    private $body;
    private $javascripts;
    private $styleSheets;
    private $hssDecoder;
    private $title;

    public function __construct() {
        $this->title = "Composites website engine"; // Default, some advertisement!
        $this->head  = "";
        $this->body  = "";
        $this->styleClasses = array();
        $this->javascripts  = array();
        $this->styleSheets  = array();
        $this->hssDecoder   = new HssDecoder();
    }

    public function addDocument(Document $document) {
        $this->appendHead($document->getHead());
        $this->appendBody($document->getBody());

        foreach($document->getStylesheets() as $sheet) {
            $this->addStylesheet($sheet);
        }

        foreach($document->getJavascripts() as $script) {
            $this->addJavascript($script);
        }
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function addTemplate(Template $template) {
        $this->appendBody($template->getHtml());
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

    // This whole method needs to be reworked. Too much harded stuff going on here.
    public function toHtml() {
        $html = "";


        $html .= "<!DOCTYPE html>" . PHP_EOL;
        $html .= "<html><head>";

        foreach($this->javascripts as $src) {
            $html .= '<script src="' . $src . '" type="text/javascript"></script>';
        }

        foreach($this->styleSheets as $src) {
            $html .= '<link href="' . $src . '" rel="stylesheet" type="text/css">';
        }

        // Filter CSS:
        $regex = "#<style([a-z0-9/\" =]{0,})>(.+?)<\/style>#is";
        while(preg_match($regex, $this->body, $out) == 1) {
            $this->body = preg_replace($regex, "", $this->body, 1);
            $this->hssDecoder->addHss($out[2]);
        }

        $html .= $this->head;
        $html .= "<style>" . PHP_EOL . $this->hssDecoder->toCss() . "</style>";
        $html .= "<title>" . $this->title . "</title>";
        $html .= "</head><body>";
        $html .= $this->body;
        $html .= "</body></html>";

        return $html;
    }

    public function __toString() {
        return $this->toHtml();
    }

    public function getHead() {
        return $this->head;
    }

    public function getBody() {
        return $this->body;
    }

    public function getStylesheets() {
        return $this->styleSheets;
    }

    public function getJavascripts() {
        return $this->javascripts;
    }
}