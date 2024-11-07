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

  $columns = "";
  $values = [];
  $types = "";

  if ($table === "organizadores") {
    $columns = "(nombre, email, telefono)";
    $values = [$data['name'], $data['email'], $data['number']];
    $types = "sss"; 
  } else if ($table === "eventos") {
  $fechaHora = $_POST['fecha'];
    list($fecha, $hora) = explode('T', $fechaHora);

    $columns = "(nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador)";
    $values = [
      htmlspecialchars(trim($data['nombre_evento'])),
      htmlspecialchars(trim($data['deporte'])),
      htmlspecialchars($fecha),
      htmlspecialchars($hora),
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

  $stmt->close();
  $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["table"])) {
  
  get($_GET["table"]);

}else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET["table"])) {

  $data = json_decode(file_get_contents("php://input"), true);
  
  post($_GET['table'], $data);
}
