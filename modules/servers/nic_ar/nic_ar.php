<?php
#
# NIC.ar Provisioning WHMCS Module v0.1
#
# A WHMCS provisioning module for the nic!alert API to NIC.ar
# https://github.com/vivaserver/whmcs-nic_ar
#
# (c)2014 Cristian R. Arroyo <cristian.arroyo@vivaserver.com>
#
# ref. http://docs.whmcs.com/Configuring_Products/Services#Module_Settings_.28aka_Provisioning.29

if (!defined("WHMCS")) die("This file cannot be accessed directly");

function nic_ar_ConfigOptions() {
  $config = array(
    "token" => array('FriendlyName'=>'Token',"Type"=>"text", "Description"=>"<a href='http://api.nicalert.me/docs#sp4' target='_blank'>API Token</a>")  # configoption1
  );
  return $config;
}

function nic_ar_CreateAccount($params) {
  $token       = $params['configoption1'];
  $service_id  = $params['serviceid'];
  $product_id  = $params['pid'];
  $domain_name = strtolower(trim($params['domain']));

  if (!empty($domain_name)) {
    $client = new \NicAr\Client($token,TRUE);
    try {
      $result = $client->whois($domain_name);
		  return "Error Message Goes Here...";
    }
    catch (\NicAr\NotFound $e) { 
      return 'success';
    }
    catch (\NicAr\CaptchaError $e) { 
		  return "Shameful Error Message Goes Here...";
    }
    catch (\Exception $e) { 
      return $e->getMessage();
    }
  }
}

function nic_ar_TerminateAccount($params) {
  # TODO
}

function nic_ar_SuspendAccount($params) {
  # TODO
}

function nic_ar_UnsuspendAccount($params) {
  # TODO
}
