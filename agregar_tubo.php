<?php
$conn = new mysqli("localhost", "root", "", "tubos_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
date_default_timezone_set('America/Argentina/Buenos_Aires'); 
$stockTubesResult = $conn->query("SELECT id, marca, tipo, temperatura, horas_vida, codigo, cantidad FROM stock_tubos");
$portaTubosResult = $conn->query("
    SELECT pt.id, pt.codigo, pt.espacios, a.nombre AS area_nombre
    FROM portatubos pt
    JOIN areas a ON pt.area_id = a.id
");

if (!$stockTubesResult || !$portaTubosResult) {
    die("Error en la consulta: " . $conn->error);
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $portaTuboId = $_POST['porta_tubo'];
    $tuboId = $_POST['tubo']; 
    $cantidad = $_POST['cantidad']; 
    $fechaColocacion = date('Y-m-d H:i:s'); 

    $spaceQuery = $conn->prepare("SELECT espacios FROM portatubos WHERE id = ?");
    if (!$spaceQuery) {
        $message = "Error al preparar la consulta: " . $conn->error;
    } else {
        $spaceQuery->bind_param("i", $portaTuboId);
        $spaceQuery->execute();
        $spaceQuery->bind_result($espaciosDisponibles);
        $spaceQuery->fetch();
        $spaceQuery->close();

        if ($espaciosDisponibles >= $cantidad) {
            $tubeQuery = $conn->prepare("SELECT cantidad, codigo FROM stock_tubos WHERE id = ?");
            if (!$tubeQuery) {
                $message = "Error al preparar la consulta: " . $conn->error;
            } else {
                $tubeQuery->bind_param("i", $tuboId);
                $tubeQuery->execute();
                $tubeQuery->bind_result($tuboCantidad, $tuboCodigo);
                $tubeQuery->fetch();
                $tubeQuery->close();

                if ($tuboCantidad >= $cantidad) {
                    $conn->begin_transaction();

                    try {
                        // Obtener el código más alto
                        $lastCodeResult = $conn->query("SELECT MAX(id) as last_id FROM tubos");
                        $lastCodeRow = $lastCodeResult->fetch_assoc();
                        $nuevoCodigoTubo = "Tubo " . ($lastCodeRow['last_id'] + 1);

                        $sql = "INSERT INTO tubos (codigo, portatubo_id, marca, tipo, temperatura, horas_vida, fecha_colocacion) 
                                SELECT ?, ?, marca, tipo, temperatura, horas_vida, ? 
                                FROM stock_tubos WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        if (!$stmt) {
                            throw new Exception("Error al preparar la consulta de inserción: " . $conn->error);
                        }

                        for ($i = 0; $i < $cantidad; $i++) {
                            $stmt->bind_param("sisi", $nuevoCodigoTubo, $portaTuboId, $fechaColocacion, $tuboId);
                            if (!$stmt->execute()) {
                                throw new Exception("Error al agregar el tubo: " . $stmt->error);
                            }
                        }

                        $updateSpaces = $conn->prepare("UPDATE portatubos SET espacios = espacios - ? WHERE id = ?");
                        if (!$updateSpaces) {
                            throw new Exception("Error al preparar la consulta de actualización: " . $conn->error);
                        }
                        $updateSpaces->bind_param("ii", $cantidad, $portaTuboId);
                        if (!$updateSpaces->execute()) {
                            throw new Exception("Error al actualizar el número de espacios disponibles: " . $updateSpaces->error);
                        }

                        $updateStock = $conn->prepare("UPDATE stock_tubos SET cantidad = cantidad - ? WHERE id = ?");
                        if (!$updateStock) {
                            throw new Exception("Error al preparar la consulta de actualización de stock: " . $conn->error);
                        }
                        $updateStock->bind_param("ii", $cantidad, $tuboId);
                        if (!$updateStock->execute()) {
                            throw new Exception("Error al actualizar la cantidad en stock: " . $updateStock->error);
                        }

                        $conn->commit();
                        $message = "Se han agregado $cantidad tubos exitosamente.";
                    } catch (Exception $e) {
                        $conn->rollback();
                        $message = "Error: " . $e->getMessage();
                    }

                    if (isset($stmt)) $stmt->close();
                    if (isset($updateSpaces)) $updateSpaces->close();
                    if (isset($updateStock)) $updateStock->close();
                } else {
                    $message = "No hay suficiente cantidad del tubo seleccionado en stock.";
                }
            }
        } else {
            $message = "No hay suficientes espacios disponibles en el porta tubo seleccionado.";
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tubo</title>
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
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>
<body onload="showAlert('<?php echo addslashes($message); ?>');">

<!-- Menú de navegación -->
<header>
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
</header>

<!-- Contenido -->
<div class="container mt-5">
    <h1 class="text-center text-primary my-4">Agregar Tubo</h1>
    <form action="agregar_tubo.php" method="POST">
        <div class="mb-3">
            <label for="porta_tubo" class="form-label">Selecciona Porta Tubo:</label>
            <select name="porta_tubo" id="porta_tubo" class="form-select" required>
                <option value="">Selecciona un porta tubo</option>
                <?php while ($portaTubos = $portaTubosResult->fetch_assoc()): ?>
                    <option value="<?= $portaTubos['id'] ?>"><?= $portaTubos['codigo'] ?> (Espacios disponibles: <?= $portaTubos['espacios'] ?>, Área: <?= $portaTubos['area_nombre'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="tubo" class="form-label">Selecciona Tubo:</label>
            <select name="tubo" id="tubo" class="form-select" required>
                <option value="">Selecciona un tubo</option>
                <?php while ($stockTube = $stockTubesResult->fetch_assoc()): ?>
                    <option value="<?= $stockTube['id'] ?>"><?= $stockTube['codigo'] ?> (Cantidad: <?= $stockTube['cantidad'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad a Agregar:</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Tubo</button>
    </form>
</div>

<!-- Pie de página -->
<footer>
    <p>&copy; E.E.S.T Nº 1 "Raúl Scalabrini Ortiz" 2024</p>
    <p>7°c Alan Najera Silva</p>
    <img src="logo.png" alt="logo">
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
