<?php

// request handler

require('nc_ocs_calls.php');
include('datahubutils.php');

// prepare foldername (user input + dynamic part)
$foldername = prepareFoldername($_POST[foldername]);

// create folder on NextCloud
$ncFoldername = createNextCloudFolder(getBaseStoragePath(), $foldername, getAuthenticationTuple());

// create a public share on the generated folder
$response = createPublicShare($foldername);
//print("Status Code: " . $response->getStatusCode() . "<br />\n");

// result arrays for the xml parsing
$values = NULL;
$index = NULL;

// XML Parsing
parseNCResponse($response->getBody(), $values, $index);

// get the URL and Id of the generated share
$publicShareURL = getShareURL($values, $index);
$publicShareId = getShareID($values, $index);

$hrefPublic = "<a href=\"" . $publicShareURL . "\">" . $publicShareURL . "</a>";

// create admin share on the generated folder
$response = createAdminShare($foldername);
//print("Status Code: " . $response->getStatusCode() . "<br />\n");

// XML Parsing
parseNCResponse($response->getBody(), $values, $index);

// get the URL and Id of the generated share
$adminShareURL = getShareURL($values, $index);
$adminShareId = getShareID($values, $index);

$hrefAdmin = "<a href=\"" . $adminShareURL . "\">" . $adminShareURL . "</a>";

/*
 * rendering response page 
*/
echo "<!doctype html>\n";
echo "<html lang=\"de\">\n";
echo "  <head>\n";
echo "    <meta charset=\"utf-8\">\n";
echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
echo "	<link rel=\"stylesheet\" href=\"./style/datahub.css\"/>\n";
echo "	<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"./style/favicon.ico\">\n";
echo "    <title>Data Hub</title>\n";
echo "  </head>\n";
  
echo "  <body>\n";
echo "    <img src=\"./style/logo-tg.svg\" width=\"120px\" align=\"right\" /><br />\n";
echo "    <img src=\"./style/background-tg.png\" width=\"550px\" align=\"center\" />\n";
	
echo "    <p id=\"header\">Online Data Hub</p>\n";

echo "    <p id=\"box\"><b>Erzeugtes NextCloud Verzeichnis: </b><br />\n";
echo " " . $ncFoldername . "</p>\n";
	
echo "	  <p id=\"box\">\n";
echo "      <b>Generierte Zugriffslinks</b><br />\n";
echo "      Share Link URL (public) (id = " . $publicShareId. "):<br />\n";
echo " " . $hrefPublic . "<br /><br />\n";
echo "      Share Link URL (admin) (id = " . $adminShareId . "):<br />\n";
echo " " . $hrefAdmin . "\n";
echo "	  </p>\n";

echo "    <p align=\"right\"><a href=\"./datahub.php\"><< zurück</a></p>\n";

echo "</body>\n";
echo "</html>\n";
