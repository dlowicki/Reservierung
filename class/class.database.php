<?php
/**
 * Connect Database
 */
class Database
{
  private $sn = "localhost";
  private $un = "shop";
  private $pw = "123456";
  private $db = "reservierung";


  public function getConnection(){
    $conn = new mysqli($this->sn, $this->un, $this->pw, $this->db); // Create connection
    if ($conn->connect_error) {   // Check connection
        die("Connection failed: " . $conn->connect_error);
        return false;
    }
    return $conn;
  }

}

?>
