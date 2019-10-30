<?php
/*
 * Data Hub Utilities
 */

/*
 * Create foldername (format: <currentDate>-<currentTime>-<randNumber>)
 * <currentDate>: yyyymmdd
 * <currentTime>: HHMMSS
 * <randNumber>: 0..9
 * delimiter: '-'
 * 
 * @return foldername
 */
function generateFoldername() {
    $dtZone = new DateTimeZone('Europe/Berlin');

    $dt = new DateTime('now', $dtZone);
    $fn = $dt->format('Ymd-His');
    $fn = $fn . "-" . rand(0, 9);

    return $fn;
}

/*
 * Formats the foldername
 */
function prepareFoldername($name) {
    $dynNamePart = generateFoldername();
    
    if (strlen($name) <> 0) {
        $foldername = $name . "-" . $dynNamePart;
    } else {
        $foldername = $dynNamePart;
    }
    return $foldername;
}

/*
 * Returns the authentication information
 */
function getAuthenticationTuple() {
    return "nextcloudadmin:#NC2019Admin";
}

/*
 * Returns the path of the base storage location
 */
function getBaseStoragePath() {
    return "http://softcloud/remote.php/dav/files/nextcloudadmin/";
} 

/*
 * Creates a new NextCloudFolder with the given Parameters
 */
function createNextCloudFolder($basepath, $foldername, $authentication) {
    // prepare curl command for the folder creation
    $cmdCreateFolder = "curl -u " . $authentication . " '" . $basepath . $foldername . "' -X MKCOL";

    try {
        $cmdResult = shell_exec($cmdCreateFolder);
    } catch (Exception $e) {
        // output the exception
        print $e->getMessage();
    }
    return $foldername;
}


/*
 * Parse the XML response of the NextCloud API call
 * 
 * $xmlContent xml response
 * $index contains the xml attributes with their index in the values array
 * $values contains the values of the xml attributes
 */
function parseNCResponse($xmlContent, &$values, &$index) {
    // clear result arrays
    $values = NULL;
    $index = NULL;

    // do the parsing
    $xmlParser = xml_parser_create();
    xml_parse_into_struct($xmlParser, $xmlContent, $values, $index);
    xml_parser_free($xmlParser);
}
