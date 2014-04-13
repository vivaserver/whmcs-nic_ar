<?php
namespace NicAr;

# NicAr\Client
# This is the _official_ PHP client for accessing the public nic!alert API at http://api.nicalert.me
#
# (c)2014 Cristian R. Arroyo <cristian.arroyo@vivaserver.com>

class NoContent extends \Exception {}
class NotFound  extends \Exception {}

class CaptchaError      extends \Exception {}
class ExpectationError  extends \Exception {}
class ParameterError    extends \Exception {}
class PreconditionError extends \Exception {}
class RequestError      extends \Exception {}
class ServiceError      extends \Exception {}
class TimeoutError      extends \Exception {}
class UnavailableError  extends \Exception {}

class Client {
  const REGEXP_DOMAIN = '/^(www\.)?(?<name>[a-z0-9-]{1,19})(?<domain>\.\w{3}\.ar)$/';
  const REGEXP_HOST   = '/^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/';
  const REGEXP_IP     = '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';

  const API_URI       = 'http://api.nicalert.me/v1';

  private $agent;  # Restful_agent is the only dependency
  private $token;  # set token to resolve CAPTCHAs. see http://api.nicalert.me/pricing
  private $assoc;  # return responses as associative arrays?
  private $api_host;

  public function __construct($token=NULL, $assoc=FALSE, $api_hosts=array()) {
    if (!empty($api_hosts)) {  # multiple API hosts, anyone?
      $idx = array_rand($api_hosts);
      $this->api_host = $api_hosts[$idx];
    }
    else $this->api_host = self::API_URI;

    $this->token = $token;
    $this->assoc = $assoc;
    $this->agent = new \Restful\Agent;
  }

  public function whois($domain=NULL) {
    if (!empty($domain)) {
      $domain = strtolower(trim($domain));
      if ($this->is_valid_domain($domain)) {
        $with_token = empty($this->token) ? '' : "?token={$this->token}";
        $response = $this->agent->get("{$this->api_host}/whois/{$domain}{$with_token}");
      }
      else throw ParameterError;
    }
    else $response = $this->agent->get("{$this->api_host}/whois");

    return $this->result_for($response);
  }

  public function status($domain) {
    $domain = strtolower(trim($domain));
    if ($this->is_valid_domain($domain)) {
      $response = $this->agent->get("{$this->api_host}/status/{$domain}");
      return $this->result_for($response);
    }
    else throw ParameterError;
  }

  private function is_valid_domain($domain) {
    return preg_match(self::REGEXP_DOMAIN,$domain) === 1;
  }

  private function is_valid_host($host) {
    return preg_match(self::REGEXP_HOST,$host) === 1;
  }

  private function is_valid_ip($ip) {
    return preg_match(self::REGEXP_IP,$ip) === 1;
  }

  private function result_for($response) {
    try {
      switch ($response->code) {
        case 200:
          return json_decode($response->body,!empty($this->assoc));
        break;
        case 204:
          throw new NoContent;
        break;
        case 400:  # Bad Request
          throw new ParameterError($response->body);
        break;
        case 404:  # Not Found
          throw new NotFound;
        break;
        case 406:  # Not Acceptable
          throw new RequestError($response->body);
        break;
        case 408:  # Request Timeout
          throw new TimeoutError($response->body);
        break;
        case 412:  # Precondition Failed
          throw new PreconditionError($response->body);
        break;
        case 417:  # Expectation Failed
          throw new ExpectationError($response->body);
        break;
        case 424:  # Failed Dependency
          throw new CaptchaError($response->body);
        break;
        case 500:  # System Error
          throw new ServiceError($response->body);
        break;
        case 503:  # Service Unavailable
          throw new UnavailableError($response->body);
        break;
      }
    }
    catch (\Exception $e) { throw $e; }  # whatever else ought to be non-API's
  }
}
