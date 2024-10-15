<?php
$conn = new mysqli("localhost", "root", "", "tubos_db");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $portatubo_id = (int)$_GET['id'];  
} else {
    die("Error: No se ha pasado un ID válido.");
}

$portatubo_result = $conn->query("SELECT codigo, area_id FROM portatubos WHERE id = $portatubo_id");

if (!$portatubo_result) {
    die("Error en la consulta: " . $conn->error);
}

$portatubo = $portatubo_result->fetch_assoc();

if (!$portatubo) {
    die("Error: No se encontró un porta tubos con ese ID.");
}

$area_id = $portatubo['area_id'];  

$tubos_result = $conn->query("SELECT codigo, marca, tipo, temperatura, horas_vida FROM tubos WHERE portatubo_id = $portatubo_id");

if (!$tubos_result) {
    die("Error en la consulta de tubos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porta Tubo: <?php echo htmlspecialchars($portatubo['codigo']); ?></title>
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
<div class="container my-5">
    <h1 class="text-center text-primary my-4">Porta Tubo: <?php echo htmlspecialchars($portatubo['codigo']); ?></h1>
    <h2 class="text-center text-secondary mb-4">Tubos en este Porta Tubo</h2>
    <ul class="list-group">
        <?php
        while ($row = $tubos_result->fetch_assoc()) {
            echo "<li class='list-group-item'>Código: {$row['codigo']}, Marca: {$row['marca']}, Tipo: {$row['tipo']}, Temperatura: {$row['temperatura']}, Horas de Vida: {$row['horas_vida']}</li>";
        }
        ?>
    </ul>
    <a  class="btn btn-primary d-block mt-4" href="ver_area.php?id=<?php echo $area_id; ?>">Volver a las Áreas</a>
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
