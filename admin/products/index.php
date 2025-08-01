<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'errores.log');
error_reporting(E_ALL);

session_start();

// Verificar si el usuario está logueado y es un administrador
if (!isset($_SESSION['usuario_nombre']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// require_once "../../js/main.js";
require_once '../../config/database.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener categorías
$categorias = [];
$categoria_result = $conn->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
while ($cat = $categoria_result->fetch_assoc()) {
    $categorias[] = $cat;
}

// Capturar filtros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// Armar cláusula WHERE
$where_clauses = [];

if (!empty($search)) {
    $where_clauses[] = "productos.nombre LIKE '%" . $conn->real_escape_string($search) . "%";
}

if ($categoria_id > 0) {
    $where_clauses[] = "productos.categoria_id = " . $categoria_id;
}

$search_query = '';
if (count($where_clauses) > 0) {
    $search_query = ' WHERE ' . implode(' AND ', $where_clauses);
}

// Función para mostrar imagen
function displayProductImage($foto, $productName) {
    if (!empty($foto)) {
        if (strpos($foto, 'Images/') === 0) {
            $imagePath = '../../' . $foto;
            $imageUrl = '../../' . $foto;
        } else {
            $imagePath = '../../Images/' . $foto;
            $imageUrl = '../../Images/' . $foto;
        }

        echo "<!-- Debug: Campo foto DB: " . $foto . " | Ruta completa: " . $imagePath . " -->";

        if (file_exists($imagePath)) {
            return '<img src="' . htmlspecialchars($imageUrl) . '" alt="' . htmlspecialchars($productName) . '" class="product-image">';
        } else {
            return '<div class="image-placeholder">
                        <div>
                            <div>Sin imagen</div>
                            <div class="image-error">Archivo no encontrado</div>
                            <div style="font-size: 10px; color: red;">
                                Campo DB: ' . htmlspecialchars($foto) . '<br>
                                Ruta buscada: ' . htmlspecialchars($imagePath) . '
                            </div>
                        </div>
                    </div>';
        }
    } else {
        return '<div class="image-placeholder">Sin imagen</div>';
    }
}

// Paginación
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total de productos
$total_products_query = $conn->query("SELECT COUNT(*) AS total FROM productos " . $search_query);
$total_products = $total_products_query->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Consulta principal con JOIN a categorías
$sql = "SELECT productos.*, categorias.nombre AS categoria_nombre 
        FROM productos 
        LEFT JOIN categorias ON productos.categoria = categorias.id
        $search_query 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Productos</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .container > form {
            margin-bottom: 20px;
        }

        .container > .btn.btn-primary {
            margin-bottom: 20px;
            display: inline-block;
        }

        .container form button[type="submit"] {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            margin-top: 10px;
        }

        .container form button[type="submit"]:hover {
            background: linear-gradient(135deg, #45a049, #3e8e41);
        }

        .product-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .image-placeholder {
            width: 100px;
            height: 100px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 12px;
            text-align: center;
        }

        .image-error {
            color: #dc3545;
            font-size: 12px;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .tablaLibros {
                font-size: 12px;
            }
            .product-image {
                max-width: 60px;
                max-height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Panel de Control de la Tienda</h1>
            <div>
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </header>

        <form action="index.php" method="get">
            <input type="text" name="search" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($search); ?>">

            <select name="categoria">
                <option value="0">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $categoria_id) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Buscar</button>
            <a href="index.php" class="btn btn-secondary" style="margin-left: 10px;">Limpiar filtros</a>
        </form>

        <a href="create.php" class="btn btn-primary">Añadir Producto</a>

        <div class="pagination" style="margin-bottom: 20px; text-align: center;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $categoria_id > 0 ? '&categoria=' . $categoria_id : ''; ?>"
                   class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-secondary'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>

        <table class="tablaLibros">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Características</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo number_format($row['precio'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($row['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                        <td><?php echo htmlspecialchars($row['caracteristicas']); ?></td>
                        <td>
                            <?php echo displayProductImage($row['foto'], $row['nombre']); ?>
                            <?php if (!empty($row['foto'])):
                                echo '<br><small style="color: #666; font-size: 10px;">' . htmlspecialchars($row['foto']) . '</small>';
                            endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn">Editar</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($result->num_rows == 0): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <h3>No se encontraron productos</h3>
                <?php if (!empty($search) || $categoria_id > 0): ?>
                    <p>No hay productos que coincidan con los filtros aplicados.</p>
                    <a href="index.php" class="btn">Ver todos los productos</a>
                <?php else: ?>
                    <p>Aún no hay productos en la base de datos</p>
                    <a href="create.php" class="btn btn-primary">Añadir el primer producto</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$result->free();
$conn->close();
?>
