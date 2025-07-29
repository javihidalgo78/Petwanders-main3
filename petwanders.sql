-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-07-2025 a las 13:43:00
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
  `categoria` varchar(60) CHARACTER SET utf32 COLLATE utf32_spanish2_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `disponible` tinyint(1) NOT NULL,
  `foto` varchar(60) NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `caracteristicas` text COLLATE utf8mb4_spanish2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `categoria`, `precio`, `disponible`, `foto`) VALUES
(1, 'Collar Ajustable Premium', 'Accesorios', 15.99, 1, 'collar_premium.jpg'),
(2, 'Correa Retráctil 5m', 'Accesorios', 24.50, 1, 'correa_retractil.jpg'),
(3, 'Cama Ortopédica Grande', 'Camas y Descanso', 89.99, 1, 'cama_ortopedica.jpg'),
(4, 'Comedero Automático', 'Alimentación', 45.75, 1, 'comedero_automatico.jpg'),
(5, 'Juguete Kong Clásico', 'Juguetes', 12.99, 1, 'kong_clasico.jpg'),
(6, 'Arena para Gatos 10kg', 'Higiene', 18.50, 1, 'arena_gatos.jpg'),
(7, 'Champú Antipulgas', 'Higiene', 9.99, 1, 'champu_antipulgas.jpg'),
(8, 'Transportín Mediano', 'Transporte', 35.00, 1, 'transportin_mediano.jpg'),
(9, 'Pelota de Tenis Pack 3', 'Juguetes', 7.50, 1, 'pelotas_tenis.jpg'),
(10, 'Rascador Torre 120cm', 'Accesorios', 65.99, 1, 'rascador_torre.jpg'),
(11, 'Pienso Premium Adulto 15kg', 'Alimentación', 52.99, 1, 'pienso_adulto.jpg'),
(12, 'Bebedero Fuente Automática', 'Alimentación', 28.75, 1, 'bebedero_fuente.jpg'),
(13, 'Arnés Acolchado', 'Accesorios', 19.99, 1, 'arnes_acolchado.jpg'),
(14, 'Manta Térmica', 'Camas y Descanso', 22.50, 1, 'manta_termica.jpg'),
(15, 'Cepillo Deslanador', 'Higiene', 14.99, 1, 'cepillo_deslanador.jpg'),
(16, 'Hueso de Cuero Natural', 'Juguetes', 8.25, 1, 'hueso_cuero.jpg'),
(17, 'Arenero Cerrado con Filtro', 'Higiene', 42.00, 1, 'arenero_cerrado.jpg'),
(18, 'Correa de Entrenamiento', 'Accesorios', 16.50, 1, 'correa_entrenamiento.jpg'),
(19, 'Snacks Dentales Pack 28', 'Alimentación', 11.99, 1, 'snacks_dentales.jpg'),
(20, 'Caseta Exterior Impermeable', 'Camas y Descanso', 125.00, 1, 'caseta_exterior.jpg'),
(21, 'Juguete Interactivo Puzzle', 'Juguetes', 21.99, 1, 'puzzle_interactivo.jpg'),
(22, 'Cortaúñas Profesional', 'Higiene', 13.50, 1, 'cortaunas_profesional.jpg'),
(23, 'Chaleco Reflectante', 'Accesorios', 17.25, 1, 'chaleco_reflectante.jpg'),
(24, 'Pienso Cachorros 12kg', 'Alimentación', 48.99, 1, 'pienso_cachorros.jpg'),
(25, 'Ratón de Juguete con Catnip', 'Juguetes', 5.99, 1, 'raton_catnip.jpg');

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
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token_password` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `direccion`, `password`, `fecha_alta`, `token_password`) VALUES
(1, 'María', 'García López', 'maria.garcia@gmail.com', 'Calle Mayor 15, 3º A, 14001 Córdoba', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890abcdef', '2025-01-15 09:30:00', NULL),
(2, 'Juan Carlos', 'Martínez Ruiz', 'jc.martinez@hotmail.com', 'Avenida de la Libertad 45, 2º B, 41002 Sevilla', '$2y$10$fedcba9876543210zyxwvutsrqponmlkjihgfedcba', '2025-02-03 13:20:00', NULL),
(3, 'Ana Isabel', 'Fernández Moreno', 'ana.fernandez@outlook.com', 'Plaza de España 8, 1º C, 29001 Málaga', '$2y$10$123456789abcdefghijklmnopqrstuvwxyz0987654321', '2025-02-18 08:45:00', NULL),
(4, 'Roberto', 'Sánchez Jiménez', 'roberto.sanchez@yahoo.es', 'Calle del Sol 22, 4º D, 18001 Granada', '$2y$10$qwertyuiop1234567890asdfghjklzxcvbnm', '2025-03-05 15:10:00', NULL),
(5, 'Carmen', 'Rodríguez Vázquez', 'carmen.rodriguez@gmail.com', 'Paseo de la Constitución 33, Bajo A, 21001 Huelva', '$2y$10$mnbvcxzasdfghjklqwertyuiop0987654321', '2025-03-12 10:55:00', NULL),
(6, 'Miguel Ángel', 'González Pérez', 'miguel.gonzalez@hotmail.es', 'Calle Nueva 67, 2º A, 23001 Jaén', '$2y$10$poiuytrewq0987654321lkjhgfdsamnbvcxz', '2025-03-28 12:40:00', NULL),
(7, 'Lucía', 'Herrera Castillo', 'lucia.herrera@outlook.es', 'Avenida del Parque 12, 1º B, 04001 Almería', '$2y$10$zxcvbnmasdfghjklqwertyuiop1234567890', '2025-04-10 06:25:00', NULL),
(8, 'Francisco', 'Morales Delgado', 'francisco.morales@gmail.com', 'Calle Real 88, 3º C, 11001 Cádiz', '$2y$10$1234567890qwertyuiopasdfghjklzxcvbnm', '2025-04-22 13:15:00', NULL),
(9, 'Isabel', 'Jiménez Navarro', 'isabel.jimenez@yahoo.com', 'Plaza Mayor 5, 2º D, 14002 Córdoba', '$2y$10$asdfghjklzxcvbnm1234567890qwertyuiop', '2025-05-08 10:30:00', NULL),
(10, 'David', 'Ramírez Torres', 'david.ramirez@hotmail.com', 'Calle Larga 99, 4º A, 41003 Sevilla', '$2y$10$qwertyuiop1234567890zxcvbnmasdfghjkl', '2025-05-20 15:05:00', NULL);

--
-- Índices para tablas volcadas
--

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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
