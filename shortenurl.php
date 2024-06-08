<?php

require('config.php');
require('SHA3Shake.php'); # shake_256 library. https://github.com/danielburger1337/sha3-shake-php
use danielburger1337\SHA3Shake\SHA3Shake;

header('Content-Type: application/json; charset=utf-8');

$testOnly = $_GET['test'];
$longUrl = $_GET['url'];
#$longUrl = 'https://www.youtube.com/watch?v=QBHDS8JUTNI';
#$longUrl = 'https://www.linkedin.com/jobs/search/?alertAction=viewjobs&currentJobId=3911517952&origin=JOBS_HOME_JOB_ALERTS&savedSearchId=1739664437';

##############################
$code = 200;
$shortUrl = '';
$message = '';
$exists = false;
try {
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ];
  $db = new PDO('sqlite:' . $dbfile, null, null, $options);
  $db->exec('CREATE TABLE IF NOT EXISTS urls(longurl TEXT PRIMARY KEY, id TEXT UNIQUE)');

  ##############################
  $selSql = 'SELECT id FROM urls where longurl = :longurl';
  $selStmt = $db->prepare($selSql);
  if ($selStmt) {
    $selStmt->bindValue(':longurl', $longUrl, PDO::PARAM_STR);
    $selStmt->execute();
    $selStmt->bindColumn('id', $id);
    if ($selStmt->fetch(PDO::FETCH_BOUND)) {
      $shortUrl = $baseUrl . $id;
      $message = 'Specified URL is already shortened.';
      $exists = true;
    }
  }
  
  if (!$exists and !$testOnly) { # Register the shortened URL to the URL database.
    $insSql = 'INSERT INTO urls(longurl, id) VALUES (:longurl, :id)';
    $insStmt = $db->prepare($insSql);
    
    if ($insStmt) {
      # Create a hash value with the specified byte length, starting with six.
      # If the hash value to insert conflicts with an already registered one, create
      # a longer hash by one byte and try inserting to the database.
      # Repeat this try until the hash value is successfully inserted into the database.
      for ($hlen = 6; true; $hlen++) {
        $id = SHA3Shake::shake256($longUrl, $hlen);
        $insStmt->bindParam(':longurl', $longUrl, PDO::PARAM_STR);
        $insStmt->bindParam(':id', $id, PDO::PARAM_STR);
        if ($insStmt->execute()) {
          break;
        }
      }
    }

    $shortUrl = $baseUrl . $id;
  }
}
catch (Exception $e) {
  $code = 500; # Internal error.
  $message = $e->getMessage();
}
$resp = [ 'code' => $code, 'shortUrl' => $shortUrl, 'message' => $message, 'exists' => $exists ];
echo json_encode($resp);

?>
