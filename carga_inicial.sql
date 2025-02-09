USE ryanair2;

INSERT INTO `aeropuertos` (`nombre`) VALUES
('Alvedro'), ('Barajas'), ('El Prat'), ('Málaga'), ('Mallorca'), ('Sevilla'), ('Bilbao');


INSERT INTO `clientes` (`nombre`, `pasaporte`, `edad`, `puntos`) VALUES
('José Pérez', '58747574XDG', 45, 60),
('Ana Gómez', '23456789ABC', 32, 40),
('Carlos López', '98765432DEF', 28, 10),
('María Fernández', '11223344GHI', 38, 75),
('Luis Ramírez', '55667788JKL', 50, 90),
('Elena Torres', '99887766MNO', 29, 20),
('Ricardo Sánchez', '44556677PQR', 41, 55),
('Patricia Navarro', '22334455STU', 35, 33),
('Fernando Gutiérrez', '66778899VWX', 27, 15);


INSERT INTO `equipajes` (`id_vuelo_cliente`, `peso`, `tamaño`, `aeropuerto`) VALUES
(1, 45, 34, 1),
(2, 55, 44, 2);

--
INSERT INTO `tripulacion` (`id`, `nombre`, `apellidos`, `rol`) VALUES
(1, 'Joé', 'Smith', 'Copiloto'),
(2, 'Carlos', 'Johnson', 'Copiloto'),
(3, 'Ana', 'Brown', 'Asistente de vuelo'),
(4, 'Lucía', 'Taylor', 'Asistente de vuelo'),
(5, 'Pedro', 'Anderson', 'Piloto'),
(6, 'Marta', 'Thomas', 'Asistente de vuelo'),
(7, 'Raúl', 'Jackson', 'Piloto'),
(8, 'Elena', 'White', 'Asistente de vuelo'),
(9, 'Laura', 'Harris', 'Asistente de vuelo'),
(10, 'Luis', 'Martin', 'Copiloto');

-- PRUEBA CON 123 o 12345678, PARA LOGUEARTE 
INSERT INTO `usuarios` (`nombre`, `pwd`, `pwdCambiada`) VALUES
('anto24', '$2y$10$KbDU2J1GIktF1ZrGWBPA7eJH6ABsUKLNelaiy8sEyng6K.PF7OIOi', 0),
('pepe2', '$2y$10$SECY10.6Srm7h/fKb5Wl4.SQhhX8YHGil5yiPTCWinCTyZ9S47l1G', 0);

INSERT INTO `vuelos` (`n_plazas`, `disponibles`, `fecha`, `id_origen`, `id_destino`, `estado`, `hora_salida`, `hora_llegada`) VALUES
(118, 0, '2024-12-25', 1, 2, 'En Hora', '10:00:00', '12:00:00');
