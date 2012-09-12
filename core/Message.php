<?php

class Message {
    private $type;
    private $payload;
    
    public function __construct($type, $payload = null) {
        if(!is_string($type)) {
            throw new InvalidArgumentException("Type must be a string.");
        }
        
        $this->type    = $type;
        $this->payload = $payload;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getPayload() {
        return $this->payload;
    }
    
    public function hasPayload() {
        return null !== $this->payload;
    }
    
    public function compareType($compareType) {
        // NB: since PHP isn't always case sensitive, we'll enforce some
        // flexible rules here, too.
        return strtolower($this->type) == strtolower($compareType);
    }
    
    public function comparePayload($comparePayload) {
        return $comparePayload === $this->payload;
    }
}
