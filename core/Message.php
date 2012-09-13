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
    
    /**
     * Retrieve the type of this message.
     * 
     * @return string The type of this message.
     * 
     * @see Message::compareType()
     * @see Message::setType() 
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Retrieve the payload of this message. If no payload is available, null is
     * returned.
     * 
     * If you wish to determine if there is payload, use Message::hasPayload()
     * instead.
     * 
     * @return mixed The payload of this message.
     * 
     * @see Message::comparePayload()
     * @see Message:hasPayload()
     */
    public function getPayload() {
        return $this->payload;
    }
    
    /**
     * A proper way to determine if this message instance has any payload data.
     * 
     * @return boolean Indication whether or not payload is carried. 
     */
    public function hasPayload() {
        return null !== $this->payload;
    }
    
    /**
     * Compare the type of this messge. This is case insensitive, but you are
     * encouraged to keep any types lowercase.
     * 
     * @param string The message type to compare with.
     * 
     * @return boolean Indication whether the comparison is true. 
     */
    public function compareType($compareType) {
        return strtolower($this->type) == strtolower($compareType);
    }
    
    
    /**
     * Compare the payload for equality. This method may work unexpected
     * if no payload is available.
     * 
     * @param mixed A payload to test with.
     * 
     * @return boolean Indication whether the comparison is true. 
     * 
     * @see Message::hasPayload()
     */
    public function comparePayload($comparePayload) {
        return $comparePayload === $this->payload;
    }
}
