<!doctype html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./style/datahub.css"/>
	<link rel="shortcut icon" type="image/x-icon" href="./style/favicon.ico">
    <title>Data Hub</title>
  </head>
  
  <body>
    <img src="./style/logo-tg.svg" width="120px" align="right" /><br />
    <img src="./style/background-tg.png" width="550px" align="center" />
	
    <p id="header">Online Data Hub</p>
	 
    <form id="box" action="./datahubrequesthandler.php" method="post" autocomplete="off">
        <label for="inTargetfolder">Verzeichnisname</label>
        <input id="inTargetfolder" name="foldername" type="text" size="33" maxlength="100"/>
        <input type="submit" value="Erstellen" />
    </form>
	
  </body>
</html>
