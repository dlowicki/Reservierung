<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <link href="css/change.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="jquery.min.js" charset="utf-8"></script>
    <script src="reserve.js" charset="utf-8"></script>
    <script src="fetch.js" charset="utf-8"></script>
  </head>
  <body>
    <?php
    if(isset($_GET['r'])){
      $change = $_GET['r'];
      if(strlen($change) >= 1 && strlen($change) <= 15){
        echo "<div id='viewChangeReserve' class='$change'> </div>";
        return;
      }
    }
    ?>
  </body>
</html>
