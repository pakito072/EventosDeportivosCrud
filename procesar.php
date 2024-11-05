<?php

function conectarDB() {

  $host = "localhost"; 
  $user = "root";
  $password = "";
  $base_datos = "eventos_deportivos";

  $connection = new mysqli($host, $user, $password, $base_datos);

  if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
  }

  return $connection;
}
  

function get($table){
  $conn = conectarDB();

  $sql = "SELECT * FROM " . $table;
  $result = $conn->query($sql);
  
  $events = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $events[] = $row;
    }
  }

  header("Content-Type: application/json");
  echo json_encode($events);

  $conn->close();

}



if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["table"])) {
  
  get($_GET["table"]);

}