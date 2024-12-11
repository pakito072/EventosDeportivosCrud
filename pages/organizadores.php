<?php
  include "../procesar.php";

  $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : null;
  unset($_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Organizadores</title>
  <!-- Vinculando Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <header class="bg-primary text-white text-center py-3">
    <h1>Organizadores</h1>
  </header>
  <main class="container mt-4">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="thead-dark">
          <th>Id</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Acciones</th>
        </thead>
        <tbody>
          <?php 
            $managers = get("organizadores");

            if (empty($managers)) {
              echo "<tr><td colspan='5'>No se ha encontrado ningún usuario</td></tr>";
            } else {
              foreach ($managers as $manager):
          ?>
            <tr>
              <td><?php echo $manager['id']; ?></td>
              <td><?php echo $manager['nombre']; ?></td>
              <td><?php echo $manager['email']; ?></td>
              <td><?php echo $manager['telefono']; ?></td>
              <td class="text-center">
                <form action='../procesar.php' method='POST' class='d-inline'>
                  <input type='hidden' name='accion' value='DELTorganizadores'>
                  <input type='hidden' name='id' value='<?php echo $manager['id']; ?>'>
                  <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm("¿Estás seguro de que deseas eliminar este organizador?")'>Eliminar</button>
                </form>
              </td>
            </tr>
          <?php 
              endforeach; 
            }
          ?>
        </tbody>
      </table>
    </div>

    <form action="../procesar.php" method="post" class="mt-4">
      
      <?php if ($errors == true): ?>
        <ul id="errorList" class="alert alert-danger">
          <?php 
            foreach ($errors as $error) {
              echo "<li>$error</li>";
            }
          ?>
        </ul>
      <?php endif; ?>
      
      <div>
        <h2>Crear organizador</h2>
        <input type="hidden" name="accion" value="POSTorganizadores">

        <div class="form-group">
          <label for="name">Nombre</label>
          <input type="text" id="name" name="name" class="form-control" placeholder="Nombre">
        </div>
        <div class="form-group">
          <label for="email">Correo</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Correo">
        </div>
        <div class="form-group">
          <label for="number">Teléfono</label>
          <input type="text" id="number" name="number" class="form-control" placeholder="Teléfono">
        </div>
      </div>

      <button id="btnForm" type="submit" class="btn btn-success">Crear</button>
      <div class="text-center mt-4"> <a href="../index.html" class="btn btn-primary">Volver a la Selección de Páginas</a> </div>
    </form>
  </main>

  <!-- Vinculando Bootstrap JS y dependencias -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
