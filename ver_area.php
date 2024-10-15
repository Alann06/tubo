<?php
$conn = new mysqli("localhost", "root", "", "tubos_db");

if (isset($_GET['id'])) {
    $area_id = $_GET['id'];
} else {
    die("Error: No se ha pasado un ID de área.");
}

$area_result = $conn->query("SELECT nombre FROM areas WHERE id = $area_id");

if (!$area_result) {
    die("Error en la consulta: " . $conn->error);
}

$area = $area_result->fetch_assoc();

if (!$area) {
    die("Error: No se encontró un área con ese ID.");
}

$portatubos_result = $conn->query("SELECT id, espacios FROM portatubos WHERE area_id = $area_id");

if (!$portatubos_result) {
    die("Error en la consulta de porta tubos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área: <?php echo $area['nombre']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
   body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            color: #333;
        }
        header {
            background-color: #4A4A72;
            color: white;
        }
        header h1 {
            margin: 0;
        }
        .navbar-collapse{
            flex-grow: 1;
        }
        .navbar-nav .nav-link {
            color: #fff !important; /* Color blanco puro para los enlaces */
            font-weight: bold; /* Para hacer el texto más visible */
        }
        .navbar-nav .nav-link:hover {
            color: #e0e0ff !important; /* Color más claro al pasar el cursor */
        }
        footer {
            text-align: center;
            padding: 15px;
            background-color: #4A4A72;
            color: white;
            margin-top: 30px;
        }
        footer img {
            width: 50px;
        }
    </style>
</head>
<body>

<!-- Menú de navegación -->
<header class="fixed-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #4A4A72;">
            <a class="navbar-brand" href="#">Gestión de Tubos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li> 
                <li class="nav-item">
                        <a class="nav-link" href="agregar_area.php">Agregar Área</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="agregar_portatubo.php">Agregar Porta Tubo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agregar_tubo.php">Agregar Tubo</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" href="gestionar_stock.php">Stock</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="verificar_alertas.php">Alerta</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header><br><br> 

<!-- Contenido -->
<div class="container mt-5">
    <h1 class="text-center text-primary my-4">Área: <?php echo $area['nombre']; ?></h1>
    <h2 class="text-center text-secondary">Porta Tubos en esta área</h2>
    <ul class="list-group">
        <?php
        while ($row = $portatubos_result->fetch_assoc()) {
            echo "<li class='list-group-item'><a href='ver_portatubo.php?id={$row['id']}' class='text-decoration-none text-primary'> (Espacios: {$row['espacios']})</a></li>";
        }
        ?>
    </ul>
    <a class="btn btn-primary mt-3" href="index.php">Volver a las Áreas</a>
</div>

<!-- Footer -->
<footer>
    <p>&copy; E.E.S.T Nº 1 "Raúl Scalabrini Ortiz" 2024</p>
    <p>7°c Alan Najera Silva</p>
    <img src="logo.png" alt="logo">
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
$conn->close();
?>

</body>
</html>
