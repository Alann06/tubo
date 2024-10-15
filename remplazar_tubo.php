<?php
date_default_timezone_set('America/Argentina/Buenos_Aires'); 
$fechaColocacion = date('Y-m-d H:i:s');
$conn = new mysqli("localhost", "root", "", "tubos_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tuboId = $_POST['tubo_id'];

    $sql = "SELECT * FROM tubos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tuboId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tubo = $result->fetch_assoc();

    $sql = "SELECT * FROM stock_tubos WHERE cantidad > 0";
    $stockResult = $conn->query($sql);

    if (!$stockResult) {
        die("Error en la consulta: " . $conn->error);
    }

    if (isset($_POST['nuevo_tubo_id'])) {
        $nuevoTuboId = $_POST['nuevo_tubo_id'];
        $fechaColocacion = date('Y-m-d H:i:s'); 

        $conn->begin_transaction();

        try {
            $sql = "UPDATE tubos SET 
                        codigo = (SELECT CONCAT(codigo, '-', UUID()) FROM stock_tubos WHERE id = ?), 
                        fecha_colocacion = ?,
                        alerta_enviada = 0  
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $nuevoTuboId, $fechaColocacion, $tuboId);
            $stmt->execute();

            $sql = "UPDATE stock_tubos SET cantidad = cantidad - 1 WHERE id = ? AND cantidad > 0";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $nuevoTuboId);
            $stmt->execute();

            $conn->commit();

            echo "<div class='alert alert-success'>El tubo ha sido reemplazado correctamente y el stock ha sido actualizado.</div>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='alert alert-danger'>Error al reemplazar el tubo: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>No se seleccionó un nuevo tubo.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Método de solicitud no válido.</div>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reemplazar Tubo</title>
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
<div class="container mt-5">
    <h1 class="text-center text-primary my-4">Reemplazar Tubo</h1>
    <form action="remplazar_tubo.php" method="POST">
        <label for="nuevo_tubo_id">Selecciona un nuevo tubo del stock:</label>
        <select name="nuevo_tubo_id" id="nuevo_tubo_id" class="form-select" required>
            <option value="">Selecciona un tubo</option>
            <?php
            while ($stockRow = $stockResult->fetch_assoc()) {
                echo "<option value='{$stockRow['id']}'>{$stockRow['codigo']} - {$stockRow['marca']} - {$stockRow['tipo']} - {$stockRow['temperatura']} - {$stockRow['horas_vida']} horas vida</option>";
            }
            ?>
        </select><br>
        <input type="hidden" name="tubo_id" value="<?php echo htmlspecialchars($tuboId); ?>">
        <input type="submit" class="btn btn-primary" value="Reemplazar">
    </form>
</div>

<!-- Footer -->
<footer>
    <p>&copy; E.E.S.T Nº 1 "Raúl Scalabrini Ortiz" 2024</p>
    <p>7°c Alan Najera Silva</p>
    <img src="logo.png" alt="logo">
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
