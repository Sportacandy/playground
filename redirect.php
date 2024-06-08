<?php

# This script is intended to be invoked with a URL rewritten by Apache RewriteEngine.
#
# When accessing a shortened URL, the rewrite engine is invoked with the .htaccess configuration
# at the /playground/m/, so that Apache HTTP server invoke 'redirect.php' (this script) with the shortened URL.

require('config.php');

$id = $_GET['id'];

##############################
try {
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ];
  $db = new PDO('sqlite:' . $dbfile, null, null, $options);
  $db->exec('CREATE TABLE IF NOT EXISTS urls(longurl TEXT PRIMARY KEY, id TEXT UNIQUE)');

  ##############################
  $selSql = 'SELECT longurl FROM urls where id = :id';
  $selStmt = $db->prepare($selSql);
  if ($selStmt) {
    $selStmt->bindValue(':id', $id, PDO::PARAM_STR);
    $selStmt->execute();
    $selStmt->bindColumn('longurl', $url);
    if ($selStmt->fetch(PDO::FETCH_BOUND)) {
      header('Location:' . $url);
      die();
    }
  }
  
  http_response_code(404);
  die();
}
catch (Exception $e) {
  http_response_code(500); # Internal error.
  die();
}

?>
