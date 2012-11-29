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

    /**
     * Retrieve the requested file, includes a path, but no query string.
     *
     * @return string The requested file or path.
     */
    public function getRequest() {
        $info = parse_url($_SERVER['REQUEST_URI']);

        return $info['path'];
    }

    public function getCanonicalRequest() {
        $info    = parse_url($_SERVER['REQUEST_URI']);
        $file    = pathinfo($info['path']);
        $request = trim($this->getRequest(), "/");

        if(isset($file['basename'])) {
            return "/" . $request;

        } else {
            return "/" . $request . '/';
        }
    }
}