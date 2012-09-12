<?php


// NB: composite, is also a component. It's just seems more flexible
// to have everything as a composite, since now really everything 
//  can be combined without the "component" limitation.

abstract class Composite {
    private $children;
    private $root;
    private $isVisible;
    
    public function __construct() {
        $this->children  = array();
        $this->isVisible = true;
        $this->root      = null;
        
        $this->initialize();
        
        for($i = 0; $i < func_num_args(); ++$i) {
            $this->addChild(func_get_arg($i));
        }
        
        //call_user_func_array(array($this, "parent::__construct"), func_get_args());
        //call_user_func_array(array(get_parent_class($this), "__construct"), array());
    }
    
    public abstract function initialize();
    public abstract function onRequest(Message $message);
    public abstract function onMessage(Message $message);
    public abstract function onRender (ClientRequest $request, Document $document);
    public abstract function onPostChildRender (ClientRequest $request, Document $document);
    public abstract function onPrepare(ClientRequest $request, $isParentVisible);
    
    public final function sendRequest(Message $message) {
        $reply = new Reply();
        
        $this->getRoot()->sendRequestUpwards($message, $reply);
        
        return $reply;
    }
    
    public final function sendRequestUpwards(Message $message, Reply $reply) {
        $response = $this->onRequest($message);
            
        if($response !== null) {
            $reply->addPayload($response);
        }
            
        foreach($this->children as $child) {
            $child->sendRequestUpwards($message, $reply);
        }
    }

    public final function sendMessage(Message $message) {
        $this->getRoot()->broadcastRecurse($message);
        
        return $this;
    }
    
    public final function broadcastRecurse(Message $message) {
        $this->onMessage($message);
         
        foreach($this->children as $child) {
            $child->broadcastRecurse($message);
        }
        
        return $this;
    }
        
    public final function setRoot(Composite $root) {
        $this->root = $root;
        
        foreach($this->children as $page) {
            $page->setRoot($root);
        }

        return $this;
    }
        
    public final function getRoot() {
        if($this->root === null) {
            return $this;
        }
        return $this->root;
    }
    
    public final function isRoot() {
        return null !== $this->getRoot();
    }
    
    public final function addChild(Composite $child) {
        
        // Happens to me all the time. Java and C++ automatically
        // call super class constructors, PHP does not. -- Gerjo
        if($this->children === null) {
            throw new Exception(
                    "Unable to addChild at this time, a " .
                    "few variables are still unset, this probably " .
                    "means that you forgot to call the super class' " .
                    "constructor. Class: " . get_class($this)
            );
        }
        
        $child->setRoot($this->getRoot());
        
        $this->children[] = $child;
        
        // If more than one argument was found, apply each
        // argument again to this function.
        if(func_num_args() > 1) {
            for($i = 1; $i < func_num_args(); ++$i) {
                $this->addChild(func_get_arg($i));
            }
        }
        
        return $this;
    }
    
    public final function addChildren(array $children) {
        foreach ($children as $child) {
            $this->addChild($child);
        }
        
        return $this;
    }
    
    public final function prepareRecurse(ClientRequest $request, $isParentVisible) {
        
        // If the parentis not visible, everything should be invisible from
        // this point onwards.
        $isVisible = $isParentVisible && $this->isVisible();
        
        $this->onPrepare($request, $isVisible);
        
        foreach($this->children as $page) {
            $page->prepareRecurse($request, $isVisible);
        }
    }
    
    public function renderRecurse(ClientRequest $request, Document $document) {
        
        // Hidden? Then halt the render loop here. Since this is depth first,
        // other composites are still allowed to render (should they be
        // visible to begin with).
        if(!$this->isVisible()) {
            return;
        }
        
        $this->onRender($request, $document);
        
        foreach($this->children as $page) {
            $page->renderRecurse($request, $document);
        }
        
        $this->onPostChildRender($request, $document);
    }
    
    public final function getChildren() {
        return $this->children;
    }
    
    public function isVisible() {
        return $this->isVisible;
    }
    
    public function setVisible($isVisible) {
        if(!is_bool($isVisible)) {
            throw new InvalidArgumentException("Hey! that's not a boolean.");
        }
        
        $this->isVisible = $isVisible;
    }
    
    // Debug logging? Specify log location?
    public function log($string) {
        print $string . "<br>";
    }
    
    public function compileDocument() {
        $request  = new ClientRequest($_GET, $_POST, $_COOKIE, $_SERVER);
        $document = new Document();
        
        // Prepare each composite, they may communicate between each other.
        foreach($this->getChildren() as $composite) {
            $composite->prepareRecurse($request, $composite->isVisible($request));
        }
        
        $this->renderRecurse($request, $document);
        
        return $document;
    }
}
