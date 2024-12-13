<?php
include "../procesar.php";

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : null;
unset($_SESSION['errors']);

$isEdit = isset($_GET['id']);
$eventData = null;

$orderBy = isset($_GET["orderBy"]) ? $_GET["orderBy"] : "id";
$direction = isset($_GET["direction"]) ? $_GET["direction"] : "ASC";

$limit = 10;
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

if ($isEdit) {
	$eventId = $_GET['id'];
	$events = get("eventos");
	foreach ($events as $event) {
		if ($event['id'] == $eventId) {
			$eventData = $event;
			break;
		}
	}
}

$search = isset($_GET["search"]) ? $_GET["search"] : "";
$events = get("eventos", $search, $orderBy, $direction, $limit, $offset);

$totalEvents = countEvents($search);
$totalPages = ceil($totalEvents / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Eventos</title>
	<!-- Vinculando Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
	<header class="bg-primary text-white text-center py-3">
		<h1>Eventos</h1>
	</header>

	<main class="container mt-4">
		<div>
			<form action="eventos.php" method="GET" class="form-inline mb-4">
				<label for="search" class="mr-2">Buscar por nombre</label>
				<input type="text" name="search" id="search" class="form-control mr-2" placeholder="Nombre del evento">
				<button type="submit" class="btn btn-primary">Buscar</button>

				<?php if (!empty($search)): ?>
					<a href="eventos.php" class="btn btn-secondary ml-2">Eliminar Busqueda</a>
				<?php endif; ?>
			</form>
			<table class="table table-bordered">
				<thead class="thead-dark">
					<th><a
							href="?search=<?php echo urlencode($search); ?>&orderBy=id&direction=<?php echo $direction === "ASC" ? "DESC" : "ASC"; ?>">Id<?php echo $orderBy === "id" ? ($direction === "ASC" ? "⬆️" : "⬇️") : ""; ?></a>
					</th>
					<th><a
							href="?search=<?php echo urlencode($search); ?>&orderBy=nombre_evento&direction=<?php echo $direction === "ASC" ? "DESC" : "ASC"; ?>">Nombre
							Evento<?php echo $orderBy === "nombre_evento" ? ($direction === "ASC" ? "⬆️" : "⬇️") : ""; ?></a></th>
					<th><a
							href="?search=<?php echo urlencode($search); ?>&orderBy=tipo_deporte&direction=<?php echo $direction === "ASC" ? "DESC" : "ASC"; ?>">Tipo
							Deporte<?php echo $orderBy === "tipo_deporte" ? ($direction === "ASC" ? "⬆️" : "⬇️") : ""; ?></a></th>
					<th><a
							href="?search=<?php echo urlencode($search); ?>&orderBy=fecha&direction=<?php echo $direction === "ASC" ? "DESC" : "ASC"; ?>">Fecha<?php echo $orderBy === "fecha" ? ($direction === "ASC" ? "⬆️" : "⬇️") : ""; ?></a>
					</th>
					<th><a
							href="?search=<?php echo urlencode($search); ?>&orderBy=hora&direction=<?php echo $direction === "ASC" ? "DESC" : "ASC"; ?>">Hora<?php echo $orderBy === "hora" ? ($direction === "ASC" ? "⬆️" : "⬇️") : ""; ?></a>
					</th>
					<th><a
							href="?search=<?php echo urlencode($search); ?>&orderBy=ubicacion&direction=<?php echo $direction === "ASC" ? "DESC" : "ASC"; ?>">Ubicación<?php echo $orderBy === "ubicacion" ? ($direction === "ASC" ? "⬆️" : "⬇️") : ""; ?></a>
					</th>
					<th><a
							href="?search=<?php echo urlencode($search); ?>&orderBy=nombre_organizador&direction=<?php echo $direction === "ASC" ? "DESC" : "ASC"; ?>">Organizador<?php echo $orderBy === "nombre_organizador" ? ($direction === "ASC" ? "⬆️" : "⬇️") : ""; ?></a>
					</th>
					<th>Acciones</th>
				</thead>

				<tbody>
					<?php
					if (empty($events)) {
						echo "<tr><td colspan='8'>No se ha encontrado ningún evento</td></tr>";
					} else {
						foreach ($events as $event):
							?>
							<tr>
								<td><?php echo $event['id']; ?></td>
								<td><?php echo $event['nombre_evento']; ?></td>
								<td><?php echo $event['tipo_deporte']; ?></td>
								<td><?php echo $event['fecha']; ?></td>
								<td><?php echo $event['hora']; ?></td>
								<td><?php echo $event['ubicacion']; ?></td>
								<td><?php echo $event['nombre_organizador']; ?></td>
								<td class="text-center">
									<div class="btn-group-vertical" role="group">
										<a href='eventos.php?id=<?php echo $event['id']; ?>' class='btn btn-warning btn-sm'>Editar</a>

										<form action='../procesar.php' method='POST' class='d-inline'>
											<input type='hidden' name='accion' value='DELTeventos'>
											<input type='hidden' name='id' value='<?php echo $event['id']; ?>'>
											<button type='submit' class='btn btn-danger btn-sm'
												onclick='return confirm("¿Estás seguro de que deseas eliminar este evento?")'>Eliminar</button>
										</form>
									</div>
								</td>
							</tr>
							<?php
						endforeach;
					}
					?>
				</tbody>
			</table>
			<div class="pagination">
				<ul class="pagination">
					<li class="page-item <?php if ($page <= 1)
						echo 'disabled'; ?>">
						<a class="page-link"
							href="?search=<?php echo urlencode($search); ?>&orderBy=<?php echo $orderBy; ?>&direction=<?php echo $direction; ?>&page=<?php echo $page - 1; ?>">Anterior</a>
					</li>

					<?php for ($i = 1; $i <= $totalPages; $i++): ?>
						<li class="page-item <?php if ($i == $page)
							echo 'active'; ?>">
							<a class="page-link"
								href="?search=<?php echo urlencode($search); ?>&orderBy=<?php echo $orderBy; ?>&direction=<?php echo $direction; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
						</li>
					<?php endfor; ?>

					<li class="page-item <?php if ($page >= $totalPages)
						echo 'disabled'; ?>">
						<a class="page-link"
							href="?search=<?php echo urlencode($search); ?>&orderBy=<?php echo $orderBy; ?>&direction=<?php echo $direction; ?>&page=<?php echo $page + 1; ?>">Siguiente</a>
					</li>
				</ul>
			</div>
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
				<h2><span id="action"><?php echo $isEdit ? "Editar" : "Crear"; ?></span> Evento</h2>

				<input type="hidden" name="accion" value="<?php echo $isEdit ? 'UPDAeventos' : 'POSTeventos'; ?>">

				<?php echo $isEdit ? '<input type="hidden" name="id" value="' . $eventData['id'] . '">' : ''; ?>

				<div class="form-group">
					<label for="nombre_evento">Nombre del Evento</label>
					<input type="text" name="nombre_evento" id="nombre_evento" class="form-control"
						placeholder="Nombre del Evento" value="<?php echo $isEdit ? $eventData['nombre_evento'] : ''; ?>">
				</div>
				<div class="form-group">
					<label for="deporte">Deporte</label>
					<input type="text" name="deporte" id="deporte" class="form-control" placeholder="Deporte"
						value="<?php echo $isEdit ? $eventData['tipo_deporte'] : ''; ?>">
				</div>
				<div class="form-group">
					<label for="fecha">Fecha y Hora</label>
					<input type="datetime-local" name="fecha" id="fecha" class="form-control"
						value="<?php echo $isEdit ? $eventData['fecha'] . 'T' . $eventData['hora'] : ''; ?>">
				</div>
				<div class="form-group">
					<label for="idOrganizador">Organizador</label>
					<select name="idOrganizador" id="idOrganizador" class="form-control">
						<option selected disabled>Selecciona un Organizador</option>
						<?php
						$managers = get("organizadores");
						foreach ($managers as $manager) {
							$selected = ($isEdit && $eventData['id_organizador'] == $manager['id']) ? 'selected' : '';
							echo "<option value='{$manager['id']}' $selected>{$manager['nombre']}</option>";
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label for="ubicacion">Ubicacion</label>
					<input type="text" name="ubicacion" id="ubicacion" class="form-control" placeholder="Ubicacion"
						value="<?php echo $isEdit ? $eventData['ubicacion'] : ''; ?>">
				</div>
			</div>

			<button type="submit" class="btn btn-success"><?php echo $isEdit ? "Actualizar" : "Crear"; ?></button>
			<div class="text-center mt-4"> <a href="../index.html" class="btn btn-primary">Volver a la Selección de
					Páginas</a> </div>
		</form>
	</main>

	<!-- Vinculando Bootstrap JS y dependencias -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>