<?php

function conectarDB(){

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

  if($table === "organizadores"){
    $sql = "SELECT * FROM organizadores";
  }else{
    $sql = "SELECT eventos.*, organizadores.nombre AS nombre_organizador
            FROM eventos
            JOIN organizadores ON eventos.id_organizador = organizadores.id;";
  }
  
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

function post($table, $data){
  $conn = conectarDB();

  $columns = "";
  $values = [];
  $types = "";

  if ($table === "organizadores") {

    $regexEmail = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    $regexNumber = '/^[0-9]{9}$/';

    $inputName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $inputEmail = isset($_POST['email']) ? trim($_POST['email']) : '';
    $inputNumber = isset($_POST['number']) ? trim($_POST['number']) : '';

    if (strlen($inputName) < 2) {
      http_response_code(401);
      $conn->close();   
      return;
    }

    if (!preg_match($regexEmail, $inputEmail)) {
      http_response_code(401);
      $conn->close();
      return;
    }

    if (strlen($inputNumber) !== 9 || !preg_match($regexNumber, $inputNumber)) {
      http_response_code(401);
      $conn->close();   
      return; 
    }




    $columns = "(nombre, email, telefono)";
    $values = [
      htmlspecialchars(trim($data['name'])),
      htmlspecialchars(trim($data['email'])),
      htmlspecialchars(trim($data['number'])),
    ];
    $types = "sss";

  } else if ($table === "eventos") {

    $datetime = $data['fecha'];
    list($date, $time) = explode('T', $datetime);

    $columns = "(nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador)";

    $values = [
      htmlspecialchars(trim($data['nombre_evento'])),
      htmlspecialchars(trim($data['deporte'])),
      htmlspecialchars($date),
      htmlspecialchars($time),
      htmlspecialchars(trim($data['ubicacion'])),
      $data['idOrganizador']
    ];
    $types = "ssssss";
  }

  $placeholders = implode(", ", array_fill(0, count($values), "?"));
  $stmt = $conn->prepare("INSERT INTO $table $columns VALUES ($placeholders)");
  $stmt->bind_param($types, ...$values);

  if ($stmt->execute()) {
    http_response_code(201);
  } else {
    http_response_code(500);
  }
}

function getById($id) {
  $conn = conectarDB();

  $stmt = $conn->prepare("SELECT * FROM eventos WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  

  if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();
    header("Content-Type: application/json");
    echo json_encode($row);
    
  } else {
    http_response_code(404);
  }

  $stmt->close();
  $conn->close();
}

function update($id, $data){
  $conn = conectarDB();

  $datetime = htmlspecialchars($data['fecha']);

  list($date, $time) = explode('T', $datetime);

  $eventName = htmlspecialchars(trim($data['nombre_evento']));
  $sport = htmlspecialchars(trim($data['deporte']));
  $location = htmlspecialchars(trim($data['ubicacion']));
  $idOrganizador = (int) $data['idOrganizador'];
  $id = (int) $id;
  
  $stmt = $conn->prepare("UPDATE eventos SET nombre_evento = ?, tipo_deporte = ?, fecha = ?, hora = ?, ubicacion = ?, id_organizador = ? WHERE id = ?");
  $stmt->bind_param("ssssssi", $eventName, $sport, $date, $time, $location, $idOrganizador, $id);

  if ($stmt->execute()) {
    http_response_code(200);

  } else {
    http_response_code(500);
    error_log("Error al actualizar evento: " . $stmt->error);
  }

  $stmt->close();
  $conn->close();
}

function delete($table, $id){
  $conn = conectarDB();


  if ($table === "organizadores") {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM eventos WHERE id_organizador = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row["COUNT(*)"] > 0) {
      http_response_code(403);
      $stmt->close();
      $conn->close();
      return;
    }
  }

  $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    http_response_code(200);
  } else {
    http_response_code(500);
  }

  $stmt->close();
  $conn->close();

}


