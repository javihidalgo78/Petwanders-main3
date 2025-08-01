-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-08-2025 a las 09:15:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `petwanders`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `talla` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id`, `id_usuario`, `id_producto`, `cantidad`, `talla`) VALUES
(16, 11, 2, 1, 'N/A'),
(17, 11, 9, 1, 'N/A'),
(18, 11, 19, 1, 'N/A'),
(19, 11, 26, 1, 'N/A'),
(20, 11, 33, 1, 'N/A'),
(21, 11, 32, 1, 'N/A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Juguetes'),
(2, 'Alimentacion'),
(3, 'Accesorios'),
(4, 'Higiene'),
(5, 'Transporte'),
(6, 'Camas y descanso'),
(7, 'Disponible'),
(8, 'Disponible en Amazon');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `fecha_compra` int(11) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `categoria` varchar(60) CHARACTER SET utf32 COLLATE utf32_spanish2_ci NOT NULL,
  `talla` varchar(11) NOT NULL DEFAULT 'm',
  `precio` int(10) NOT NULL,
  `stock_total` int(10) NOT NULL DEFAULT 0,
  `tiene_variantes` tinyint(1) NOT NULL DEFAULT 0,
  `disponible` tinyint(1) NOT NULL,
  `stock` int(200) NOT NULL,
  `foto` varchar(60) NOT NULL,
  `amazonUrl` varchar(200) NOT NULL,
  `caracteristicas` text NOT NULL,
  `tallas` text DEFAULT NULL,
  `amazon_url` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `categoria`, `talla`, `precio`, `stock_total`, `tiene_variantes`, `disponible`, `stock`, `foto`, `amazonUrl`, `caracteristicas`, `tallas`, `amazon_url`, `categoria_id`) VALUES
(1, 'Collar Ajustable Premium', 'Guay)))))', 'Accesorios', 'm', 16, 0, 1, 1, 5, 'collar_premium_1.jpg', '', 'Ajustable para diferentes tamaños, Material resistente al agua, Hebilla de seguridad reforzada', NULL, NULL, NULL),
(2, 'Correa Retráctil 5m', 'Guay', 'Accesorios', 'm', 24, 0, 1, 1, 0, 'correa_retractil_2.webp', '', 'Longitud extensible hasta 5 metros, Sistema de frenado automático, Mango ergonómico antideslizante', NULL, NULL, NULL),
(3, 'Cama Ortopédica Grande', '', 'Camas y Descanso', 'm', 90, 0, 1, 1, 0, 'cama_ortopedica.webp', '', 'Espuma ortopédica de alta densidad, Funda lavable y extraíble, Ideal para perros con problemas articulares', NULL, NULL, NULL),
(4, 'Comedero Automático', '', 'Alimentación', 'm', 46, 0, 1, 1, 0, 'comedero_automatico.webp', '', 'Programable para múltiples comidas, Dispensador de hasta 2kg de alimento, Funcionamiento silencioso', NULL, NULL, NULL),
(5, 'Juguete Kong Clásico', 'guay', 'Juguetes', 'm', 13, 0, 1, 1, 4, 'juguete_kong.jpg', '', 'Material ultra resistente Kong, Estimula el juego interactivo, Apto para rellenar con premios', NULL, NULL, NULL),
(6, 'Arena para Gatos 10kg', '', 'Higiene', 'm', 19, 0, 1, 1, 0, 'arena_gatos.jpg', '', 'Arena aglomerante de alta calidad, Control superior de olores, Baja generación de polvo', NULL, NULL, NULL),
(7, 'Champú Antipulgas', '', 'Higiene', 'm', 10, 0, 1, 1, 0, 'champu_antipulgas.webp', '', 'Fórmula natural antipulgas, pH balanceado para piel sensible, Aroma fresco y duradero', NULL, NULL, NULL),
(8, 'Transportín Mediano', 'Awesome!!!!!', 'Transporte', 'm', 36, 0, 1, 1, 0, 'transportin_mediano_1.webp', '', 'Ventilación óptima en todos los lados, Puerta de seguridad con doble cierre, Asa resistente para transporte cómodo', NULL, NULL, NULL),
(9, 'Pelota de Tenis Pack 3', '', 'Juguetes', 'm', 8, 0, 1, 1, 0, 'pelotas.webp', '', 'Pack de 3 pelotas profesionales, Material no tóxico y duradero, Tamaño perfecto para perros medianos', NULL, NULL, NULL),
(11, 'Pienso Premium Adulto 15kg', '', 'Alimentación', 'm', 53, 0, 1, 1, 0, 'pienso_premium.jpg', '', 'Ingredientes naturales premium, Rico en proteínas y vitaminas, Sin colorantes artificiales', NULL, NULL, NULL),
(14, 'Manta Térmica', 'hhhhhhhh', 'Camas y Descanso', 'm', 23, 0, 1, 1, 0, 'manta.webp', '', 'Material térmico autorregulable, Lavable en lavadora, Tamaño compacto y portable', NULL, NULL, NULL),
(15, 'Cepillo Deslanador', '', 'Higiene', 'm', 15, 0, 1, 1, 0, 'cepillo.webp', '', 'Cerdas de doble densidad, Reduce la caída del pelo hasta 90%, Mango ergonómico antideslizante', NULL, NULL, NULL),
(16, 'Hueso de Cuero Natural', '', 'Juguetes', 'm', 8, 0, 1, 1, 0, 'hueso.webp', '', '100% cuero natural, Ayuda a mantener dientes limpios, Larga duración para masticadores intensos', NULL, NULL, NULL),
(18, 'Correa de Entrenamiento', '', 'Accesorios', 'm', 17, 0, 1, 1, 0, 'correa.jpg', '', 'Resistente a la intemperie, Múltiples opciones de longitud, Mosquetón giratorio de 360°', NULL, NULL, NULL),
(19, 'Snacks Dentales Pack 28', '', 'Alimentación', 'm', 12, 0, 1, 1, 0, 'snack.webp', '', 'Ayuda a limpiar dientes y encías, Sabor irresistible para perros, Fórmula libre de azúcares añadidos', NULL, NULL, NULL),
(20, 'Caseta Exterior Impermeable', '', 'Camas y Descanso', 'm', 125, 0, 1, 1, 0, 'caseta.jpg', '', 'Resistente a lluvia y viento, Montaje fácil sin herramientas, Base elevada para mejor drenaje', NULL, NULL, NULL),
(21, 'Juguete Interactivo Puzzle', '', 'Juguetes', 'm', 22, 0, 1, 1, 0, 'puzzle.webp', '', 'Estimula la inteligencia canina, Múltiples niveles de dificultad, Materiales seguros y no tóxicos', NULL, NULL, NULL),
(22, 'Cortaúñas Profesional', '', 'Higiene', 'm', 14, 0, 1, 1, 0, 'cortaunas.jpg', '', 'Cuchillas de acero inoxidable, Mango antideslizante profesional, Incluye lima de uñas', NULL, NULL, NULL),
(23, 'Chaleco Reflectante', '', 'Accesorios', 'm', 17, 0, 1, 1, 0, 'chaleco.jpg', '', 'Material altamente reflectante, Ajustable para diferentes tallas, Cierre de velcro resistente', NULL, NULL, NULL),
(25, 'Ratón de Juguete con Catnip', '', 'Juguetes', 'm', 6, 0, 1, 1, 0, 'juguetegato.webp', '', 'Relleno de catnip premium, Tamaño ideal para gatos, Estimula el instinto de caza natural', NULL, NULL, NULL),
(26, 'Mokka', '', 'Accesorios', 'm', 30, 0, 1, 1, 0, 'Harness.JPEG', 'https://www.amazon.com/arnéspetshop/id12345', 'Diseño moderno con colores atractivos, Correa ajustable incluida, Material transpirable y cómodo', NULL, 'https://www.amazon.com/arnéspetshop/id12345', NULL),
(27, 'Chocolate', '', 'Accesorios', 'm', 33, 0, 1, 1, 0, 'Harness2.JPEG', 'https://www.amazon.com/arnéspetshop/id12345', 'Color chocolate elegante, Acolchado extra para comodidad, Fácil colocación con clips rápidos', NULL, 'https://www.amazon.com/arnéspetshop/id12345', NULL),
(28, 'Electric', '', 'Accesorios', 'm', 35, 0, 1, 1, 0, 'Harness4.JPEG', 'https://www.amazon.com/arnéspetshop/id12345', 'Diseño eléctrico vibrante, Material reflectante para seguridad, Resistente a tirones fuertes', NULL, 'https://www.amazon.com/arnéspetshop/id12345', NULL),
(29, 'White Fountain', '', 'Alimentación', 'm', 43, 0, 1, 1, 0, 'Fuenteblanca.JPEG', 'https://amzn.eu/d/ffLMFYJ', 'Fuente de agua blanca elegante, Sistema de filtración integrado, Capacidad de 2.5 litros', NULL, 'https://amzn.eu/d/ffLMFYJ', NULL),
(30, 'Green Fountain', '', 'Alimentación', 'm', 43, 0, 1, 1, 0, 'Fuenteverde.JPEG', 'https://amzn.eu/d/ffLMFYJ', 'Color verde atractivo, Bomba silenciosa de larga duración, Fácil limpieza y mantenimiento', NULL, 'https://amzn.eu/d/ffLMFYJ', NULL),
(31, 'Automatic Feeder', '', 'Alimentación', 'm', 60, 0, 1, 1, 0, 'Petfeeder.JPEG', 'https://amzn.eu/d/g9I4pBq', 'Dispensador automático programable, Capacidad para 6kg de alimento, Grabación de voz personalizada', NULL, 'https://amzn.eu/d/g9I4pBq', NULL),
(32, 'Correas', '', 'Accesorios', 'm', 13, 0, 1, 1, 0, 'Leashes.JPEG', 'https://amzn.eu/d/g9I4pBq', 'Disponible en varios colores, Material duradero y lavable, Longitud perfecta para paseos', NULL, 'https://amzn.eu/d/g9I4pBq', NULL),
(33, 'Arenero Autolimpiable', '', 'Higiene', 'm', 250, 0, 1, 1, 0, 'Catlitterbox.JPEG', 'https://amzn.eu/d/1Tm9dYD', 'Sistema de auto-limpieza innovador, Capacidad para múltiples gatos, Reduce olores automáticamente', NULL, 'https://amzn.eu/d/1Tm9dYD', NULL),
(34, 'Fuente de agua y Comedero', '', 'Alimentación', 'm', 80, 0, 1, 1, 0, 'Waterfeeder2in1.JPEG', 'https://amzn.eu/d/g9I4pBq', 'Doble función agua y comida, Diseño compacto y moderno, Materiales libres de BPA', NULL, 'https://amzn.eu/d/g9I4pBq', NULL),
(35, 'Comedero Bebedero de viaje', '', 'Alimentación', 'm', 20, 0, 1, 1, 0, 'dogbowl.jpg', 'https://amzn.eu/d/1BeI0St', 'Plegable para fácil transporte, Materiales resistentes y ligeros, Ideal para viajes y excursiones', NULL, 'https://amzn.eu/d/1BeI0St', NULL),
(38, 'Arnés Ajustable Premium', 'El más cool del parque', '', 'm', 28, 0, 0, 0, 0, 'Harness3_1.jpeg', '', '', NULL, NULL, NULL),
(40, 'Bebedero', 'Con refrigeración', '', 'm', 200, 0, 0, 0, 0, 'Waterfeeder_1.jpeg', '', '', NULL, NULL, NULL);

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `before_update_producto` BEFORE UPDATE ON `productos` FOR EACH ROW BEGIN
    IF NOT(NEW.talla IN ('s', 'm', 'l')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La talla debe ser "s", "m" o "l"';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_variantes`
--

CREATE TABLE `producto_variantes` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `talla` enum('s','m','l') NOT NULL,
  `stock` int(10) NOT NULL DEFAULT 0,
  `precio_adicional` decimal(10,2) DEFAULT 0.00,
  `sku` varchar(50) DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `producto_variantes`
--
DELIMITER $$
CREATE TRIGGER `tr_variante_delete_stock` AFTER DELETE ON `producto_variantes` FOR EACH ROW BEGIN
    UPDATE `productos` 
    SET `stock_total` = (
        SELECT COALESCE(SUM(stock), 0) 
        FROM `producto_variantes` 
        WHERE `producto_id` = OLD.producto_id
    )
    WHERE `id` = OLD.producto_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_variante_insert_stock` AFTER INSERT ON `producto_variantes` FOR EACH ROW BEGIN
    UPDATE `productos` 
    SET `stock_total` = (
        SELECT COALESCE(SUM(stock), 0) 
        FROM `producto_variantes` 
        WHERE `producto_id` = NEW.producto_id
    )
    WHERE `id` = NEW.producto_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_variante_update_stock` AFTER UPDATE ON `producto_variantes` FOR EACH ROW BEGIN
    UPDATE `productos` 
    SET `stock_total` = (
        SELECT COALESCE(SUM(stock), 0) 
        FROM `producto_variantes` 
        WHERE `producto_id` = NEW.producto_id
    )
    WHERE `id` = NEW.producto_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `apellidos` varchar(60) CHARACTER SET utf16 COLLATE utf16_spanish2_ci NOT NULL,
  `email` varchar(60) NOT NULL,
  `direccion` varchar(200) CHARACTER SET utf16 COLLATE utf16_spanish2_ci NOT NULL,
  `password` varchar(60) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'cliente',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token_password` varchar(200) DEFAULT NULL,
  `tipo_usuario` varchar(50) NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `direccion`, `password`, `rol`, `fecha_alta`, `token_password`, `tipo_usuario`) VALUES
(1, 'María', 'García López', 'maria.garcia@gmail.com', 'Calle Mayor 15, 3º A, 14001 Córdoba', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890abcdef', 'admin', '2025-07-25 12:23:22', NULL, 'cliente'),
(2, 'Juan Carlos', 'Martínez Ruiz', 'jc.martinez@hotmail.com', 'Avenida de la Libertad 45, 2º B, 41002 Sevilla', '$2y$10$fedcba9876543210zyxwvutsrqponmlkjihgfedcba', 'cliente', '2025-02-03 13:20:00', NULL, 'cliente'),
(3, 'Ana Isabel', 'Fernández Moreno', 'ana.fernandez@outlook.com', 'Plaza de España 8, 1º C, 29001 Málaga', '$2y$10$123456789abcdefghijklmnopqrstuvwxyz0987654321', 'cliente', '2025-02-18 08:45:00', NULL, 'cliente'),
(4, 'Roberto', 'Sánchez Jiménez', 'roberto.sanchez@yahoo.es', 'Calle del Sol 22, 4º D, 18001 Granada', '$2y$10$qwertyuiop1234567890asdfghjklzxcvbnm', 'cliente', '2025-03-05 15:10:00', NULL, 'cliente'),
(5, 'Carmen', 'Rodríguez Vázquez', 'carmen.rodriguez@gmail.com', 'Paseo de la Constitución 33, Bajo A, 21001 Huelva', '$2y$10$mnbvcxzasdfghjklqwertyuiop0987654321', 'cliente', '2025-03-12 10:55:00', NULL, 'cliente'),
(6, 'Miguel Ángel', 'González Pérez', 'miguel.gonzalez@hotmail.es', 'Calle Nueva 67, 2º A, 23001 Jaén', '$2y$10$poiuytrewq0987654321lkjhgfdsamnbvcxz', 'cliente', '2025-03-28 12:40:00', NULL, 'cliente'),
(7, 'Lucía', 'Herrera Castillo', 'lucia.herrera@outlook.es', 'Avenida del Parque 12, 1º B, 04001 Almería', '$2y$10$zxcvbnmasdfghjklqwertyuiop1234567890', 'cliente', '2025-04-10 06:25:00', NULL, 'cliente'),
(8, 'Francisco', 'Morales Delgado', 'francisco.morales@gmail.com', 'Calle Real 88, 3º C, 11001 Cádiz', '$2y$10$1234567890qwertyuiopasdfghjklzxcvbnm', 'cliente', '2025-04-22 13:15:00', NULL, 'cliente'),
(9, 'Isabel', 'Jiménez Navarro', 'isabel.jimenez@yahoo.com', 'Plaza Mayor 5, 2º D, 14002 Córdoba', '$2y$10$asdfghjklzxcvbnm1234567890qwertyuiop', 'cliente', '2025-05-08 10:30:00', NULL, 'cliente'),
(10, 'David', 'Ramírez Torres', 'david.ramirez@hotmail.com', 'Calle Larga 99, 4º A, 41003 Sevilla', '$2y$10$qwertyuiop1234567890zxcvbnmasdfghjkl', 'cliente', '2025-05-20 15:05:00', NULL, 'cliente'),
(11, 'Javier', 'Hidalgo Lopez', 'javihidalgo78@gmail.com', 'Avenida del Sol 33', '$2y$10$q9t7nZ/ki5XQ8y3BNFqfMu3nj7ZWoAVQBvcj/RIAqengnuM99Ueva', 'cliente', '2025-07-18 12:06:00', NULL, 'cliente'),
(12, 'Javier', 'Hidalgo Lopez', 'jhidlop267@g.educaand.es', 'Avenida del Sol 33', '$2y$10$RPO7rohcmNm8QxPxqqEXfOfNN4qxAq8cU2.zY7htLr79i4tkpNJSK', 'cliente', '2025-07-22 08:44:15', NULL, 'cliente'),
(13, 'Javier', 'Hidalgo Lopez', 'javihidalgoamz78@gmail.com', 'Avenida del Sol 33', '$2y$10$jxvEmM27BwZ2uXE3gPsIBOuG0PFSTIQqu.CgAglow2EgEDSZ5HT..', 'cliente', '2025-07-23 11:40:27', NULL, 'cliente'),
(14, 'Jaime', 'Urrutia', 'correo@gmail.pt', 'Avenida del Sol 33', '$2y$10$eCaIv.l3IrbsEcECCxTsye3RWNGoNLAvYNwdqwoXWPQaH0/WQXzEK', 'cliente', '2025-07-28 07:01:52', NULL, 'cliente'),
(15, 'Javi', 'Hidalgo', 'admin@example.com', '', '$2y$10$PYYQ5QnePHXDQNC5fLjO8OW5LInp7McfjTArmH4.DSMqsivzz.CdO', 'admin', '2025-07-28 07:56:59', NULL, 'cliente'),
(16, 'Javier', 'Hidalgo Lopez', 'correo@gmail.ir', 'Avenida del Sol 33', '$2y$10$A4oaAEChCy6WfU0CyxHMOOOk4ymz0gnT6M/q0irlc7A0CdqaG2Dbm', 'cliente', '2025-07-29 09:15:53', NULL, 'cliente'),
(17, 'Jaime', 'Hidalgo Lopez', 'correo@gmail.rs', 'Avenida del Sol 33', '$2y$10$9/KOr1xCxQ.PkGBvIl7on.2JedR7UYfPXX1aiK1fmT2dgL9qMZCFi', 'cliente', '2025-07-29 11:48:55', NULL, 'cliente'),
(18, 'Jaime', 'Urrutia', 'correo@gmail.is', 'Avenida del Sol 33', '$2y$10$I.EnWqmMvHCzKakayazcL.fZ2Y2sWhRwMU.mzokNaHgZnYzvGFFpu', 'cliente', '2025-07-31 10:35:06', NULL, 'cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria` (`categoria_id`);

--
-- Indices de la tabla `producto_variantes`
--
ALTER TABLE `producto_variantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_talla_unique` (`producto_id`,`talla`),
  ADD KEY `idx_producto_id` (`producto_id`),
  ADD KEY `idx_talla` (`talla`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `producto_variantes`
--
ALTER TABLE `producto_variantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto_variantes`
--
ALTER TABLE `producto_variantes`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_variante_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
