<?php

class Reply {
    private $payload;
    
    public function __construct() {
        $this->payload = array();
    }
    
    /**
     * Add some arbitrary payload to this message. Adding payload will
     * never override existing payload. It's merely appended in the internal
     * array.
     *
     * @param mixed The payload.
     * 
     * @return Reply Returns a $this instance, for easy method chaining.
     */
    public function addPayload($payload) {
        $this->payload[] = $payload;
        return $this;
    }
    
    /**
     * Retrieve the number of payload items available.
     * 
     * @return int The number of payload items available.
     * 
     * @see Reply::hasPayload()
     */
    public function getPayloadSize() {
        return sizeof($this->payload);
    }
    
    /**
     * Determine whether any payload is available.
     * 
     * @return boolean Indication whether there is payload available.
     * 
     * @see Reply::getPayloadSize()
     */
    public function hasPayload() {
        return $this->getPayloadSize() > 0;
    }
    
    /**
     * Retrieve the internal payload collection. Payload is stored as an array
     * since more than one composite may reply to a request.
     * 
     * @return mixed[] An array with the payload.
     * 
     * @see Reply::getPayloadAt(integer)
     */
    public function getPayloadArray() {
        return $this->payload;
    }
    
    /**
     * Retrieve a specific payload item.
     * 
     * @param integer The index of the payload item.
     * 
     * @return mixed The requested payload item.
     * 
     * @throws InvalidArgumentException When a non integer type index is requested.
     * @throws OutOfBoundsException When a nonexistent index is requested.
     * 
     * @see Reply::getPayloadArray(integer)
     * @see Reply::getPayloadSize(integer)
     */
    public function getPayloadAt($index) {
        // FILTER_VALIDATE_INT considers zero not a number, for this reason
        // we perform a explicit "0" comparison.
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
