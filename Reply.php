<?php

class Reply {
    private $payload;
    
    public function __construct() {
        $this->payload = array();
    }
    
    public function addPayload($payload) {
        $this->payload[] = $payload;
        return $this;
    }
    
    public function getPayloadSize() {
        return sizeof($this->payload);
    }
    
    public function hasPayload() {
        return $this->getPayloadSize() > 0;
    }
    
    public function getPayloadArray() {
        return $this->payload;
    }
    
    public function getPayloadAt($index) {
        // NB: FILTER_VALIDATE_INT considers zero not a number.
        if($index !== 0 && !filter_var($index, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException("That's not an integer!");
        }
        
        if(isset($this->payload[$index])) {
            return $this->payload[$index];
        }
        
        throw new OutOfBoundsException("Unable to acquire payload at index " . $index);
    }
}

?>
