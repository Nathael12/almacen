-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 07, 2026 at 04:20 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `almacen`
--

-- --------------------------------------------------------

--
-- Table structure for table `lotes`
--

CREATE TABLE `lotes` (
  `id_lote` int NOT NULL,
  `producto_id` int DEFAULT NULL,
  `proveedor_id` int DEFAULT NULL,
  `fecha_entrada` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `fecha_salida` date DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lotes`
--

INSERT INTO `lotes` (`id_lote`, `producto_id`, `proveedor_id`, `fecha_entrada`, `fecha_caducidad`, `cantidad`, `fecha_salida`, `estado`) VALUES
(1, 1, 1, '2026-08-04', '2026-08-21', 1, '2026-08-05', 0),
(2, 1, 1, '2026-08-04', '2026-08-30', 1, '2026-08-05', 0),
(3, 1, 1, '2026-08-04', '2026-08-31', 1, '2026-08-05', 0),
(4, 2, 1, '2026-08-04', '2026-08-25', 0, '2026-09-05', 0),
(5, 3, 1, '2026-08-04', '2026-09-10', 0, '2026-09-05', 0),
(6, 2, 1, '2026-08-04', '2026-09-17', 1, '2026-08-04', 0),
(7, 2, 1, '2026-09-05', '2026-09-25', 952, '2026-09-05', 0),
(8, 2, 1, '2026-09-05', '2026-09-27', 0, '2026-09-05', 0),
(9, 4, 1, '2026-09-05', '2026-11-26', 0, '2026-09-05', 0),
(10, 5, 1, '2026-09-05', '2027-02-05', 20, NULL, 1),
(11, 4, 1, '2026-09-05', '2026-09-29', 6, NULL, 1),
(12, 1, 1, '2026-09-23', '2028-06-06', 6, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id_producto` int NOT NULL,
  `nombre_comercial` varchar(100) NOT NULL,
  `nombre_comun` varchar(100) DEFAULT NULL,
  `presentacion` decimal(10,2) DEFAULT NULL,
  `unidad_id` int DEFAULT NULL,
  `categoria_producto` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_comercial`, `nombre_comun`, `presentacion`, `unidad_id`, `categoria_producto`, `estado`) VALUES
(1, 'Leche Alpura Entera 1L', 'Leche', NULL, 1, 'Líquidos', 0),
(2, 'Frijol Negro Verde Valle 1kg', 'Frijol', '1.00', 2, 'Granos', 1),
(3, 'Detergente Ariel Doble Poder', 'Jabón de ropa', '500.00', 5, 'Polvos', 1),
(4, 'Pepsi Cola', 'Pepsi', NULL, 3, 'Refresco', 1),
(5, 'Cocacola', 'coca', '1.00', 1, 'Refresco', 1),
(6, 'Pepsi Cola', 'Pepsi', NULL, 3, 'Refresco', 0),
(7, 'Sanissimo', 'Galletas Orneadas', '450.00', 5, 'galleta', 1),
(8, 'Pepsi Cola', 'Pepsi', '1.00', 1, 'Refresco', 1);

-- --------------------------------------------------------

--
-- Table structure for table `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre`, `estado`) VALUES
(1, 'Diconsa', 1);

-- --------------------------------------------------------

--
-- Table structure for table `salidas`
--

CREATE TABLE `salidas` (
  `id_salida` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad_usada` int NOT NULL,
  `fecha_salida` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `salidas`
--

INSERT INTO `salidas` (`id_salida`, `producto_id`, `cantidad_usada`, `fecha_salida`) VALUES
(1, 1, 1, '2026-08-05'),
(2, 1, 2, '2026-08-05'),
(3, 2, 49, '2026-09-05'),
(4, 2, 1, '2026-09-05'),
(5, 2, 2, '2026-09-05'),
(6, 2, 2, '2026-09-05'),
(7, 4, 10, '2026-09-05'),
(8, 4, 10, '2026-09-05'),
(9, 3, 1, '2026-09-05'),
(10, 4, 10, '2026-09-05');

-- --------------------------------------------------------

--
-- Table structure for table `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id_unidad` int NOT NULL,
  `nombre_unidad` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `abreviatura` varchar(20) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `unidad_medida`
--

INSERT INTO `unidad_medida` (`id_unidad`, `nombre_unidad`, `abreviatura`, `estado`) VALUES
(1, 'Litro', 'L', 1),
(2, 'Kilogramo', 'kg', 1),
(3, 'Mililitros', 'ML', 1),
(5, 'Gramos', 'gr', 1);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `nombre`) VALUES
(4, 'coordinador', '$2y$10$kUvAcGrlXRPVMiVNAIC2bes57EpiqgF/YJ88bCHnhWWYz9.fPQRI2', 'Administrador');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lotes`
--
ALTER TABLE `lotes`
  ADD PRIMARY KEY (`id_lote`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `fk_productos_unidad` (`unidad_id`);

--
-- Indexes for table `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indexes for table `salidas`
--
ALTER TABLE `salidas`
  ADD PRIMARY KEY (`id_salida`);

--
-- Indexes for table `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id_unidad`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lotes`
--
ALTER TABLE `lotes`
  MODIFY `id_lote` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salidas`
--
ALTER TABLE `salidas`
  MODIFY `id_salida` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id_unidad` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lotes`
--
ALTER TABLE `lotes`
  ADD CONSTRAINT `lotes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `lotes_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Constraints for table `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_unidad` FOREIGN KEY (`unidad_id`) REFERENCES `unidad_medida` (`id_unidad`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_unidad_producto` FOREIGN KEY (`unidad_id`) REFERENCES `unidad_medida` (`id_unidad`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
