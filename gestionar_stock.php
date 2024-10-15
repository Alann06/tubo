<?php
$conn = new mysqli("localhost", "root", "", "tubos_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca = $_POST['marca'];
    $tipo = $_POST['tipo'];
    $temperatura = $_POST['temperatura'];
    $horas_vida = $_POST['horas_vida'];
    $codigo = $_POST['codigo'];
    $cantidad = $_POST['cantidad'];

    $sql_check = "SELECT * FROM stock_tubos WHERE codigo = '$codigo' OR (marca = '$marca' AND tipo = '$tipo')";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo "<p class='error'>Error: Ya existe un tubo con el mismo código o combinación de marca y tipo.</p>";
    } else {
        $sql = "INSERT INTO stock_tubos (marca, tipo, temperatura, horas_vida, codigo, cantidad) 
                VALUES ('$marca', '$tipo', '$temperatura', '$horas_vida', '$codigo', '$cantidad')";
        $conn->query($sql);
        echo "<p class='success'>Stock de tubos agregado exitosamente.</p>";
    }
}

$sql_all = "SELECT * FROM stock_tubos";
$result_all = $conn->query($sql_all);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tubos al Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            color: #333;
            padding-top: 80px; /* Asegura que el contenido no quede debajo del menú */
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
    <div class="container-fluid">
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
    <h1 class="text-center text-primary my-4">Agregar Tubos al Stock</h1>
    <form method="post" class="mb-4">
        <div class="mb-3">
            <label for="marca" class="form-label">Marca:</label>
            <input type="text" name="marca" id="marca" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de Tubo:</label>
            <select name="tipo" id="tipo" class="form-select" required>
                <option value="">Selecciona un tipo</option>
                <option value="a">a</option>
                <option value="b">b</option>
                <option value="c">c</option>
                <option value="a+b">a+b</option>
                <option value="t8">t8</option>
                <option value="t5">t5</option>
                <option value="t12">t12</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="temperatura" class="form-label">Temperatura:</label>
            <select name="temperatura" id="temperatura" class="form-select" required>
                <option value="Cálido">Cálido</option>
                <option value="Frío">Frío</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="horas_vida" class="form-label">Horas de Vida:</label>
            <input type="number" name="horas_vida" id="horas_vida" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="codigo" class="form-label">Código:</label>
            <input type="text" name="codigo" id="codigo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad:</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" required>
        </div>
        <input type="submit" value="Agregar Tubos al Stock" class="btn btn-primary">
    </form>

    <h2 class="text-center text-primary">Registro de Tubos en Stock</h2>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Marca</th>
                <th>Tipo</th>
                <th>Temperatura</th>
                <th>Horas de Vida</th>
                <th>Código</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_all->num_rows > 0): ?>
                <?php while ($row = $result_all->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['marca']; ?></td>
                        <td><?php echo $row['tipo']; ?></td>
                        <td><?php echo $row['temperatura']; ?></td>
                        <td><?php echo $row['horas_vida']; ?></td>
                        <td><?php echo $row['codigo']; ?></td>
                        <td><?php echo $row['cantidad']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay registros disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
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
