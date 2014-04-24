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
  # TODO
}
