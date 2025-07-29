ALTER TABLE `productos`
ADD `tallas` TEXT NULL DEFAULT NULL AFTER `caracteristicas`,
ADD `capacidades` TEXT NULL DEFAULT NULL AFTER `tallas`,
ADD `colores` TEXT NULL DEFAULT NULL AFTER `capacidades`;
