<?php
#
# NIC.ar Addon WHMCS Module v0.1
#
# Provides quick Argentinian domain lookups on a convenient Admin page.
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
      echo '<div class="infobox">';  # also .successbox
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
  catch (\Exception $e) { 
    echo '<div class="errorbox">';
    echo "<strong><span class='title'>An error has occurred.</span></strong>";
    echo 'Please help improve the NIC.ar (unofficial) API reporting the following bug at <a href="http://api.nicalert.me/contact">this page</a>.';
    echo '<pre>'; var_dump($e); echo '</pre>';
    echo '</div>';
  }
  echo '<br>';
  echo '<form action="/admin/addonmodules.php" method="POST">';
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
