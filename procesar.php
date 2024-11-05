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

function delete ($table, $id){
  
  $conn = conectarDB();

  $stmt = $conn->prepare("DELETE FROM " . $table . " WHERE id = ?");
  $stmt->bind_param("i",$id);

  if ($stmt->execute()) {
    http_response_code(200);
  }else{
    http_response_code(500);
  }

  $stmt->close();
  $conn->close();

}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["table"])) {
  
  get($_GET["table"]);

}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

  parse_str(file_get_contents("php://input"),$data);
  $table = isset
  
}