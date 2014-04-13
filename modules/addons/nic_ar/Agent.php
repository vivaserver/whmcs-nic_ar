<?php
  namespace Restful;

  # Restful_agent
  # A little library for accessing URLs using Curl in a RESTful way
  #
  # (c)2013 Cristian R. Arroyo <cristian.arroyo@vivaserver.com>

  class Agent {
    const AGENT = 'Restful_agent';

    private $handler;

    public function delete($url,$params=array()) {
      $this->curl_begin();
      curl_setopt($this->handler,CURLOPT_CUSTOMREQUEST,'DELETE');
      curl_setopt($this->handler,CURLOPT_URL,$url);
      curl_setopt($this->handler,CURLOPT_POSTFIELDS,http_build_query($params));
      return $this->curl_end();
    }

    public function get($url) {
      $this->curl_begin();
      curl_setopt($this->handler,CURLOPT_URL,$url);
      return $this->curl_end();
    }

    public function post($url,$params) {
      $this->curl_begin();
      curl_setopt($this->handler,CURLOPT_URL,$url);
      curl_setopt($this->handler,CURLOPT_POST,TRUE);
      curl_setopt($this->handler,CURLOPT_POSTFIELDS,http_build_query($params));  # do not set to "multipart/form-data"
      return $this->curl_end();
    }

    public function put($url,$params) {
      $this->curl_begin();
      curl_setopt($this->handler,CURLOPT_CUSTOMREQUEST,'PUT');
      curl_setopt($this->handler,CURLOPT_URL,$url);
      curl_setopt($this->handler,CURLOPT_POSTFIELDS,http_build_query($params));
      return $this->curl_end();
    }

    private function curl_begin() {
      if (gettype($this->handler) != 'resource') {
        if (in_array('curl',get_loaded_extensions())) {
          $this->handler = curl_init();
          curl_setopt($this->handler,CURLOPT_HEADER,FALSE);
          curl_setopt($this->handler,CURLOPT_RETURNTRANSFER,TRUE);
          curl_setopt($this->handler,CURLOPT_USERAGENT,self::AGENT);
          //
          // MAMP does not install the proper CA cert bundle (http://curl.haxx.se/ca/cacert.pem) for
          // verifying the peer's certificate, to avoid this, explicitly prevent cURL from trying:
          //
          curl_setopt($this->handler,CURLOPT_SSL_VERIFYPEER,FALSE);
        }
        else throw new Exception("{$this->agent} class requires the CURL extension loaded. Please check your PHP configuration.");
      }
    }

    private function curl_end() {
      $result = curl_exec($this->handler);
      if ($result !== FALSE) {
        $response = new \stdClass;
        $response->code = curl_getinfo($this->handler,CURLINFO_HTTP_CODE);
        $response->body = $result;
        curl_close($this->handler);
        return $response;
      }
	    else throw new \Exception(curl_error($this->handler));
    }
  }

/* End of file Restful_agent */
