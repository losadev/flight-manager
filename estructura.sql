CREATE DATABASE IF NOT EXISTS ryanair2;
USE ryanair2;

CREATE TABLE IF NOT EXISTS `aeropuertos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` TEXT NOT NULL,
  `pasaporte` TEXT NOT NULL,
  `edad` INT(11) NOT NULL,
  `puntos` INT(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `equipajes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_vuelo_cliente` INT(11) NOT NULL,
  `peso` DECIMAL(10,0) NOT NULL,
  `tamaño` DECIMAL(10,0) NOT NULL,
  `aeropuerto` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`aeropuerto`) REFERENCES `aeropuertos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tripulacion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` TEXT NOT NULL,
  `apellidos` TEXT NOT NULL,
  `rol` ENUM('Piloto','Copiloto','Asistente de vuelo','') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(65) NOT NULL,
  `pwd` VARCHAR(255) NOT NULL,
  `pwdCambiada` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `vuelos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `n_plazas` INT(11) NOT NULL,
  `disponibles` TINYINT(1) NOT NULL,
  `fecha` DATE DEFAULT NULL,
  `id_origen` INT(11) NOT NULL,
  `id_destino` INT(11) NOT NULL,
  `estado` ENUM('En Hora','Cancelado','Retrasado','Volando','Finalizado') NOT NULL,
  `hora_salida` TIME NOT NULL,
  `hora_llegada` TIME NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_origen`) REFERENCES `aeropuertos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_destino`) REFERENCES `aeropuertos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `vuelos_clientes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_vuelo` INT(11) NOT NULL,
  `id_cliente` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_vuelo` (`id_vuelo`,`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `vuelos_tripulacion` (
  `id_vuelo` INT(11) NOT NULL,
  `id_tripulante` INT(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
