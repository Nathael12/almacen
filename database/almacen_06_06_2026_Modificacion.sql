-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 06, 2026 at 11:15 PM
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
  `fecha_salida` date DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `cantidad` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lotes`
--

INSERT INTO `lotes` (`id_lote`, `producto_id`, `proveedor_id`, `fecha_entrada`, `fecha_caducidad`, `fecha_salida`, `estado`, `cantidad`) VALUES
(1, 1, 1, '2026-02-01', '2026-02-28', '2026-06-06', 0, 0),
(2, 1, 1, '2026-02-01', '2026-02-28', '2026-06-06', 0, 0),
(3, 1, 1, '2026-02-01', '2026-03-05', '2026-06-06', 0, 0),
(4, 1, 1, '2026-06-06', '2026-07-02', '2026-06-06', 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id_producto` int NOT NULL,
  `nombre_comercial` varchar(100) NOT NULL,
  `nombre_comun` varchar(100) DEFAULT NULL,
  `unidad_id` int DEFAULT NULL,
  `categoria_producto` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_comercial`, `nombre_comun`, `unidad_id`, `categoria_producto`, `estado`) VALUES
(1, 'Cocacola', 'coca', 3, 'liquido', 1),
(2, 'oreo', 'oreo', 2, 'galleta', 0);

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
  `lote_id` int NOT NULL,
  `cantidad_usada` int NOT NULL,
  `fecha_salida` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `salidas`
--

INSERT INTO `salidas` (`id_salida`, `producto_id`, `lote_id`, `cantidad_usada`, `fecha_salida`) VALUES
(1, 1, 4, 1, '2026-06-06');

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
(3, 'Mililitros', 'ML', 1);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `name_user` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lotes`
--
ALTER TABLE `lotes`
  MODIFY `id_lote` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salidas`
--
ALTER TABLE `salidas`
  MODIFY `id_salida` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id_unidad` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT;

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
