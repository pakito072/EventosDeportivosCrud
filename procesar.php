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


function post($table, $data) {
  $conn = conectarDB();

  $name = htmlspecialchars(trim($data['name']));
  $email = htmlspecialchars(trim($data['email']));
  $number = htmlspecialchars(trim($data['number']));

  $columns = "";
  if ($table === "organizadores") {
    $columns = "(nombre, email, telefono)";
    
  }else if ($table === "eventos") {
    $columns = "(nombre_evento, tipo_deporte, fecha, hora, ubicacion)";
  }
  
  $stmt = $conn->prepare("INSERT INTO $table $columns VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $number);

  if ($stmt->execute()) {
      echo json_encode(["success" => true, "message" => "Registro insertado correctamente."]);
  } else {
      echo json_encode(["success" => false, "message" => "Error al insertar el registro: " . $stmt->error]);
  }

  $stmt->close();
  $conn->close();
}



if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["table"])) {
  
  get($_GET["table"]);

}else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET["table"])) {
  
  post($_GET['table'], $_POST);
}

