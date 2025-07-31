<?php

// session_start();

// if (!isset($_SESSION['usuario_nombre'])) {
//     header('Location: admin/login.php');
//     exit;
// }


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
require_once '../../config/database.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Función para mostrar la imagen correctamente
function displayProductImage($foto, $productName) {
    if (!empty($foto)) {
        // Determinar la ruta correcta
        // Si $foto ya contiene "Images/", usar la ruta completa
        // Si no, añadir "Images/"
        if (strpos($foto, 'Images/') === 0) {
            // El campo ya contiene "Images/archivo.jpg"
            $imagePath = '../../' . $foto;
            $imageUrl = '../../' . $foto;
        } else {
            // El campo solo contiene "archivo.jpg"
            $imagePath = '../../Images/' . $foto;
            $imageUrl = '../../Images/' . $foto;
        }
        
        // Debug: mostrar la ruta que se está intentando usar
        echo "<!-- Debug: Campo foto DB: " . $foto . " | Ruta completa: " . $imagePath . " -->";
        
        // Verificar si el archivo existe
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

// Pagination variables
$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if (!empty($search)) {
    $search_query = " WHERE nombre LIKE '%%" . $conn->real_escape_string($search) . "%%'";
}

// Get total number of products for pagination
$total_products_query = $conn->query("SELECT COUNT(*) AS total FROM productos" . $search_query);
$total_products = $total_products_query->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Fetch products with search and pagination
$sql = "SELECT * FROM productos" . $search_query . " LIMIT " . $limit . " OFFSET " . $offset;
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        /* Separación para la barra de búsqueda y el botón de añadir */
        .container > form {
            margin-bottom: 20px;
        }
        .container > .btn.btn-primary {
            margin-bottom: 20px;
            display: inline-block; /* Asegura que el margen se aplique correctamente */
        }

        .container form button[type="submit"] {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            margin-top: 10px;
        }

        .container form button[type="submit"]:hover {
            background: linear-gradient(135deg, #45a049, #3e8e41);
        }

        /* Estilos para las imágenes */
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

        /* Responsive table */
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
            <button type="submit">Buscar</button>
        </form>
        <a href="create.php" class="btn btn-primary">Añadir Producto</a>

        <div class="pagination" style="margin-bottom: 20px; text-align: center;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>

        <table class="tablaLibros">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
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
                        <td><?php echo htmlspecialchars($row['caracteristicas']); ?></td>
                        <td>
                            <?php echo displayProductImage($row['foto'], $row['nombre']); ?>
                            <?php if (!empty($row['foto'])): ?>
                                <br><small style="color: #666; font-size: 10px;"><?php echo htmlspecialchars($row['foto']); ?></small>
                            <?php endif; ?>
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
                <?php if (!empty($search)): ?>
                    <p>No hay productos que coincidan con la búsqueda: "<?php echo htmlspecialchars($search); ?>"</p>
                    <a href="index.php" class="btn">Ver todos los productos</a>
                <?php else: ?>
                    <p>Aún no hay productos en la base de datos</p>
                    <a href="create.php" class="btn btn-primary">Añadir el primer producto</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Información de debug (opcional - puedes eliminar esto en producción) -->
        <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
            <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                <h4>Información de Debug:</h4>
                <p><strong>Total productos:</strong> <?php echo $total_products; ?></p>
                <p><strong>Página actual:</strong> <?php echo $page; ?></p>
                <p><strong>Total páginas:</strong> <?php echo $total_pages; ?></p>
                <p><strong>Búsqueda:</strong> <?php echo !empty($search) ? htmlspecialchars($search) : 'Ninguna'; ?></p>
                <p><strong>SQL:</strong> <?php echo htmlspecialchars($sql); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>