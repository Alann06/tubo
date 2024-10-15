<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $area_id = $_POST['area_id'];
    $espacios = $_POST['espacios'];

    $conn = new mysqli("localhost", "root", "", "tubos_db");

    $sql = "INSERT INTO portatubos (area_id, espacios) VALUES ('$area_id', '$espacios')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Porta tubo agregado exitosamente.');</script>";
    } else {
        echo "<script>alert('Error al agregar el porta tubo: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Porta Tubo</title>
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
    <h1 class="text-center text-primary my-4">Agregar Porta Tubo</h1>
    <form method="post">
        <div class="mb-3">
            <label for="area_id" class="form-label">Área:</label>
            <select name="area_id" id="area_id" class="form-select" required>
                <?php
                $conn = new mysqli("localhost", "root", "", "tubos_db");
                $result = $conn->query("SELECT id, nombre FROM areas");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="espacios" class="form-label">Espacios:</label>
            <input type="number" name="espacios" id="espacios" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Agregar Porta Tubo</button>
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
