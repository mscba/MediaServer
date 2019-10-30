<?php

/*
 * Implementation of several NextCloud Client API calls
 * 
 * @author R.Wildhaber (afiwir)
 */

//@fixme afiwir, 2019-08-22: install http client (guzzle) not in the user home directory.
//@fixme afiwir, 2019-08-22: modify include path
require('/home/rwildhab/vendor/autoload.php');

include('nc_ocs_conf.php');

/*
 * functions
 */

/*
 * Create a new share for the file/folder given by $filename
 * 
 * @http_client guzzle http client
 * @filename name of file or folder which is goint to be shared
 * @permission (int) 1 = read; 2 = update; 4 = create; 8 = delete; 16 = share; 31 = all
 * @shareType (int) 0 = user; 1 = group; 3 = public link; 6 = federated cloud share
 * 
 * @return returns the response as XML (incl. shareId and status code)
 * Status Codes:
 *   100 - successful
 *   400 - unknown share type
 *   403 - public upload was disabled by the admin
 *   404 - file couldn’t be shared
 */
function createNewShare(&$http_client, $filename, $permission, $shareType = 3) {

    $publicUpload = 'false';
    if ($permission > 1) {
        $publicUpload = 'true';
    }

    try {
    /* Method: POST
     * POST Arguments: path - (string) path to the file/folder which should be shared
     * POST Arguments: shareType - (int) 0 = user; 1 = group; 3 = public link; 6 = federated cloud share
     * POST Arguments: shareWith - (string) user / group id with which the file should be shared
     * POST Arguments: publicUpload - (string) allow public upload to a public shared folder (true/false)
     * POST Arguments: password - (string) password to protect public link Share with
     * POST Arguments: permissions - (int) 1 = read; 2 = update; 4 = create; 8 = delete; 16 = share; 31 = all (default: 31, for public shares: 1)
     * 
     * Mandatory fields: shareType, path and shareWith for shareType 0 or 1.
     */

    $res = $http_client->request('POST', 'shares', [
        'debug' => false,
        'form_params' => [
            'path' => $filename,
            'shareType' => $shareType,
            'permissions' => $permission,
            'publicUpload' => $publicUpload
        ]
    ]);

    } catch (GuzzleHttp\Exception\ClientException $e) {
        print("Exception: " . $e->getMessage() . "<br />\n");
    }
    return $res;
}

/*
 * Instantiate a new HTTP client
 */
function getHttpClient() {
    global $baseURI;
    global $ncUser;
    global $ncUserPW;

    $client = new GuzzleHttp\Client([
    'base_uri' => $baseURI,
    'headers' => ['OCS-APIRequest' => 'true'],
    'auth' => [$ncUser, $ncUserPW],]);
        
    return $client;
}

/*
 * Create a share with read-only rights
 */
function createPublicShare($filename) {
    $client = getHttpClient();
    return createNewShare($client, $filename, 1, 3);
}

/*
 * Create a share with all access rights (admin)
 */
function createAdminShare($filename) {
    $client = getHttpClient();
    return createNewShare($client, $filename, 31, 3);
}

/*
 * Get index of the $attributename within the values array
 */
function getValueArrayAttributeIndex($arrIndex, $attributename) {
    $idx = NULL;
    foreach($arrIndex as $key => $value) {
        if($key == $attributename) {
            // the index of attribute within the value array is stored at pos 0 in the index array
            $idx = $arrIndex[$key][0];
        }
    }
    return $idx;
}

/*
 * Get the value of the given $index from the value array
 */
function getArrayValue($arrValues, $index) {
    $ret = NULL;
    foreach($arrValues[$index] as $key => $value) {
        if($key == 'value') {
            $ret = $value;
        }
    }
    return $ret;
}

/*
 * Get the URL of the generated share
 */
function getShareURL($arrValues, $arrIndex) {
    $idx = getValueArrayAttributeIndex($arrIndex, 'URL');
    return getArrayValue($arrValues, $idx);
}

/*
 * Get the Id of the generated share
 */
function getShareID($arrValues, $arrIndex) {
    $idx = getValueArrayAttributeIndex($arrIndex, 'ID');
    return getArrayValue($arrValues, $idx);
}