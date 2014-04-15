<?php
#
# NIC.ar Addon WHMCS Module v0.1
#
# A WHMCS admin. addon for the nic!alert API to NIC.ar
# Provides quick Argentinian domain lookups on a convenient Admin page.
# https://github.com/vivaserver/whmcs-nic_ar
#
# (c)2014 Cristian R. Arroyo <cristian.arroyo@vivaserver.com>
#

if (!defined("WHMCS")) die("This file cannot be accessed directly");

require_once 'Agent.php';
require_once 'Client.php';

function nic_ar_config() {
  $params = array(
    "name"    => "NIC.ar Addon Module",
    "description" => "Provides quick Argentinian domain name lookups.",
    "version" => "0.1",
    "author"  => "Cristian R. Arroyo",
    'fields'  => array(
      # the "token" allows for automatic resolution of CAPTCHAs that NIC.ar might ask before resolving the domain lookup
      "token" => array('FriendlyName'=>'Token',"Type"=>"text", "Description"=>"<a href='http://api.nicalert.me/docs#sp4' target='_blank'>API Token</a>")
    )
  );
  return $params;
}

function nic_ar_output($params) {
  $client = new \NicAr\Client($params['token'],TRUE);
  try {
    if ($_REQUEST['name'] && $_REQUEST['domain']) {
      $domain = trim($_REQUEST['name']).trim($_REQUEST['domain']);
      $result = $client->whois($domain);
      $link   = $result['status']['delegated'] ? "<a href='http://www.{$domain}' target='_blank'>{$domain}</a>" : $domain;
      echo '<div class="infobox">';
      echo "<strong><span class='title'>{$link}</span></strong>";
      echo '<br>';
      echo 'This domain name is already registered.';
      echo '</div>';
      echo '<table class="form" width="50%">';
      #
      echo '<tr>';
      echo "<td class='fieldlabel'>Message:</td>";
      echo "<td class='fieldarea'>{$result['message']}</td>";
      echo '</tr>';
      #
      echo '<tr>';
      echo "<td class='fieldlabel'>Created on:</td>";
      echo "<td class='fieldarea'>{$result['created_on']}</td>";
      echo '</tr>';
      echo '<tr>';
      echo "<td class='fieldlabel'>Expires on:</td>";
      echo "<td class='fieldarea'>{$result['expires_on']}</td>";
      echo '</tr>';
      #
      echo '<tr>';
      echo '<td class="fieldlabel">Registrant Contact</td>';
      echo '<td></td>';
      echo '</tr>';
      #
      echo '<tr>';
      echo "<td class='fieldlabel'>Name:</td>";
      echo "<td class='fieldarea'>{$result['contacts']['registrant']['name']}</td>";
      echo '</tr>';
      echo '<tr>';
      echo "<td class='fieldlabel'>ID:</td>";
      echo "<td class='fieldarea'>{$result['contacts']['registrant']['id']}</td>";
      echo '</tr>';
      echo '<tr>';
      echo "<td class='fieldlabel'>Activity:</td>";
      echo "<td class='fieldarea'>{$result['contacts']['registrant']['activity']}</td>";
      echo '</tr>';
      if (!empty($result['contacts']['registrant']['addresses']['local'])) {
        $location = $result['contacts']['registrant']['addresses']['local'];
        echo '<tr>';
        echo "<td class='fieldlabel'>Phone:</td>";
        echo "<td class='fieldarea'>{$location['phone']}</td>";
        echo '</tr>';
        echo '<tr>';
        echo "<td class='fieldlabel'>Address:</td>";
        echo "<td class='fieldarea'>{$location['address']}</td>";
        echo '</tr>';
        echo '<tr>';
        echo "<td class='fieldlabel'>City:</td>";
        echo "<td class='fieldarea'>{$location['city']}</td>";
        echo '</tr>';
        echo '<tr>';
        echo "<td class='fieldlabel'>Province:</td>";
        echo "<td class='fieldarea'>{$location['province']}</td>";
        echo '</tr>';
        echo '<tr>';
        echo "<td class='fieldlabel'>Zip Code:</td>";
        echo "<td class='fieldarea'>{$location['zip_code']}</td>";
        echo '</tr>';
        echo '<tr>';
        echo "<td class='fieldlabel'>Country:</td>";
        echo "<td class='fieldarea'>{$location['country']}</td>";
        echo '</tr>';
      }
      #
      echo '<tr>';
      echo '<td class="fieldlabel">Name Servers</td>';
      echo '<td></td>';
      echo '</tr>';
      #
      if (!empty($result['name_servers'])) {
        foreach ($result['name_servers'] as $ns) {
          echo '<tr>';
          echo "<td class='fieldlabel'>Host #{$ns['id']}:</td>";
          echo "<td class='fieldarea'>{$ns['host']}</td>";
          echo '</tr>';
          if (!empty($ns['ip'])) {
            echo '<tr>';
            echo "<td class='fieldlabel'>IP:</td>";
            echo "<td class='fieldarea'>{$ns['ip']}</td>";
            echo '</tr>';
            echo '<tr>';
          }
        }
      }
      echo '</table>';
    }
    else {
      echo '<p>This simple Addon provides quick Argentinian (.ar) domain lookups. Just search for a domain name using the form below.</p>';
    }
  }
  catch (\NicAr\NotFound $e) { 
    echo '<div class="successbox">';
    echo "<strong><span class='title'>This domain name is not registered yet.</span></strong><br>";
    echo "You can register <strong>{$domain}</strong> at <a href='http://www.nic.ar' target='_blank'>NIC.ar</a>.";
    echo '</div>';
  }
  catch (\NicAr\CaptchaError $e) { 
    echo '<div class="errorbox">';
    echo "<strong><span class='title'>You must resolve a CAPTCHA challenge first.</span></strong><br>";
    echo "You can retry at the <a href='https://nic.ar/buscarDominio.xhtml' target='_blank'>NIC.ar</a> site or consider getting a <a href='http://api.nicalert.me/pricing' target='_blank'>nic!alert API token</a>.";
    echo '</div>';
  }
  catch (\Exception $e) { 
    echo '<div class="errorbox">';
    echo "<strong><span class='title'>An error has occurred.</span></strong><br>";
    echo 'Please help improve the NIC.ar (unofficial) API reporting the following bug at <a href="http://api.nicalert.me/contact" target="_blank">this page</a>.';
    echo '<pre>'; var_dump($e); echo '</pre>';
    echo '</div>';
  }
  echo '<br>';
  echo "<form action='{$_SERVER['PHP_SELF']}' method='POST'>";  # consider $customadminpath
  echo '<input name="module" type="hidden" value="nic_ar">';
  echo '<input name="name" placeholder="domain-name">';
  echo '&nbsp;';
  echo '<select name="domain">';
  echo '<option>.com.ar</option>';
  echo '<option>.gob.ar</option>';
  echo '<option>.int.ar</option>';
  echo '<option>.mil.ar</option>';
  echo '<option>.net.ar</option>';
  echo '<option>.org.ar</option>';
  echo '<option>.tur.ar</option>';
  echo '</select>';
  echo '&nbsp;';
  echo '<input type="submit" value="Search">';
  echo '</form>';
}
