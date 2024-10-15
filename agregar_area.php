<?php
$conn = new mysqli("localhost", "root", "", "tubos_db");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_area = $_POST['nombre_area'];

    $sql_check = "SELECT * FROM areas WHERE nombre = '$nombre_area'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Error: Ya existe un área con el mismo nombre.');</script>";
    } else {
        $sql = "INSERT INTO areas (nombre) VALUES ('$nombre_area')";
        $conn->query($sql);
        echo "<script>alert('Área agregada exitosamente.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Área</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            padding-top: 80px;
        }
        header {
            background-color: #4A4A72;
            color: white;
        }
        header h1 {
            margin: 0;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: bold;
        }
        .navbar-nav .nav-link:hover {
            color: #e0e0ff !important;
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
        <nav class="navbar navbar-expand-lg navbar-dark">
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
</header>

<!-- Contenido -->
<div class="container">
    <h1 class="text-center text-primary my-4">Agregar Área</h1>
    <form method="post" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="nombre_area" class="form-label">Nombre del Área:</label>
            <input type="text" name="nombre_area" id="nombre_area" class="form-control" required>
        </div>
        <input type="submit" value="Agregar Área" class="btn btn-success">
    </form>
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
