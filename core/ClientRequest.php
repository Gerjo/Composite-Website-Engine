<?php

class ClientRequest {
    private $get;
    private $post;
    private $cookie;
    private $server;

    public function __construct(array $get, array $post, array $cookie, array $server) {

	if(isset($_SERVER['REDIRECT_QUERY_STRING'])) {
  		parse_str($_SERVER['REDIRECT_QUERY_STRING'], $_GET);
        }

        $this->get      = $get;
        $this->post     = $post;
        $this->cookie   = $cookie;
        $this->server   = $server;
	print "<!--"; echo $this->getCanonicalRequest(); print "-->";

    }

    /**
     * Retrieve the requested file, includes a path, but no query string.
     *
     * @return string The requested file or path.
     */
    public function getRequest() {
	if(isset($_SERVER['REDIRECT_URL'])) {
		return $_SERVER['REDIRECT_URL'];
	}

        $info = parse_url($_SERVER['REQUEST_URI']);

        return $info['path'];
    }

    public function getCanonicalRequest() {
        $info    = parse_url($_SERVER['REQUEST_URI']);
        $file    = pathinfo($this->getRequest());


        $request = "/" . trim($this->getRequest(), "/");

        if(!isset($file['extension']) && !empty($file['filename'])) {
            $request .= "/";
        }

        return $request;
    }
}
