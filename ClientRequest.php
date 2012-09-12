<?php

class ClientRequest {
    private $get;
    private $post;
    private $cookie;
    private $server;
    
    public function __construct(array $get, array $post, array $cookie, array $server) {
        $this->get      = $get;
        $this->post     = $post;
        $this->cookie   = $cookie;
        $this->server   = $server;
    }
    
    public function getRequest() {
        if(isset($this->server['PATH_INFO'])) {
            return $this->server['PATH_INFO'];
        }
        
        return $this->server['SCRIPT_NAME'];
    }
}