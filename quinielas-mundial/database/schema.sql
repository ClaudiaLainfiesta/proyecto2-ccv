CREATE TABLE Fase (
	nombre_fase varchar(100),
	PRIMARY KEY (nombre_fase)
);

CREATE TABLE Grupo (
	codigo_grupo integer,
	PRIMARY KEY (codigo_grupo)
);

CREATE TABLE Equipo (
	pais varchar(100),

	codigo_grupo integer,

	bandera bytea,

	PRIMARY KEY (pais),
	FOREIGN KEY (codigo_grupo) REFERENCES Grupo(codigo_grupo)
);

CREATE TABLE Partido (
	codigo_partido integer,
	estadio varchar(100),
	fecha date,
	hora time,
	goles_local_oficial integer,
	goles_visitante_oficial integer,

	nombre_fase varchar(100),

	pais_local varchar(100), 
	pais_visitante varchar(100), 


	PRIMARY KEY (codigo_partido),
	FOREIGN KEY (nombre_fase) REFERENCES Fase(nombre_fase),

	FOREIGN KEY (pais_local) REFERENCES Equipo(pais),
	FOREIGN KEY (pais_visitante) REFERENCES Equipo(pais),
	CONSTRAINT chk_paises_distintos CHECK (pais_visitante <> pais_local)
);


CREATE TABLE Usuario (
	username varchar(100),
	nombre varchar(100),
	contrasena varchar(60),
	PRIMARY KEY (username)
);

CREATE TABLE Prediccion (
	codigo_partido integer,

	username varchar(100),

	goles_local_prediccion integer,
	goles_visitante_prediccion integer,
	puntos_prediccion integer,

	PRIMARY KEY (codigo_partido, username),
	FOREIGN KEY (codigo_partido) REFERENCES Partido(codigo_partido),
	FOREIGN KEY (username) REFERENCES Usuario(username)
);

INSERT INTO Fase (nombre_fase) VALUES ('Fase de Grupos');

INSERT INTO Grupo (codigo_grupo) VALUES 
(1),  -- Grupo A
(2),  -- Grupo B
(3),  -- Grupo C
(4),  -- Grupo D
(5),  -- Grupo E
(6),  -- Grupo F
(7),  -- Grupo G
(8),  -- Grupo H
(9),  -- Grupo I
(10), -- Grupo J
(11), -- Grupo K
(12); -- Grupo L

INSERT INTO Equipo (pais, codigo_grupo, bandera) VALUES
-- Grupo A (Código 1)
('México', 1, '\x00'),
('Sudáfrica', 1, '\x00'),
('República de Corea', 1, '\x00'),
('República Checa', 1, '\x00'),

-- Grupo B (Código 2)
('Canadá', 2, '\x00'),
('Bosnia y Herzegovina', 2, '\x00'),
('Catar', 2, '\x00'),
('Suiza', 2, '\x00'),

-- Grupo C (Código 3)
('Brasil', 3, '\x00'),
('Marruecos', 3, '\x00'),
('Haití', 3, '\x00'),
('Escocia', 3, '\x00'),

-- Grupo D (Código 4)
('Estados Unidos', 4, '\x00'),
('Paraguay', 4, '\x00'),
('Australia', 4, '\x00'),
('Turquía', 4, '\x00'),

-- Grupo E (Código 5)
('Alemania', 5, '\x00'),
('Curazao', 5, '\x00'),
('Costa de Marfil', 5, '\x00'),
('Ecuador', 5, '\x00'),

-- Grupo F (Código 6)
('Países Bajos', 6, '\x00'),
('Japón', 6, '\x00'),
('Suecia', 6, '\x00'),
('Túnez', 6, '\x00'),

-- Grupo G (Código 7)
('Bélgica', 7, '\x00'),
('Egipto', 7, '\x00'),
('Irán', 7, '\x00'),
('Nueva Zelanda', 7, '\x00'),

-- Grupo H (Código 8)
('España', 8, '\x00'),
('Cabo Verde', 8, '\x00'),
('Arabia Saudí', 8, '\x00'),
('Uruguay', 8, '\x00'),

-- Grupo I (Código 9)
('Francia', 9, '\x00'),
('Senegal', 9, '\x00'),
('Irak', 9, '\x00'),
('Noruega', 9, '\x00'),

-- Grupo J (Código 10)
('Argentina', 10, '\x00'),
('Argelia', 10, '\x00'),
('Austria', 10, '\x00'),
('Jordania', 10, '\x00'),

-- Grupo K (Código 11)
('Portugal', 11, '\x00'),
('RD Congo', 11, '\x00'),
('Uzbekistán', 11, '\x00'),
('Colombia', 11, '\x00'),

-- Grupo L (Código 12)
('Inglaterra', 12, '\x00'),
('Croacia', 12, '\x00'),
('Ghana', 12, '\x00'),
('Panamá', 12, '\x00');

INSERT INTO Partido (codigo_partido, estadio, fecha, hora, goles_local_oficial, goles_visitante_oficial, nombre_fase, pais_local, pais_visitante) VALUES
-- Jueves, 11 de junio 2026
(1, 'Estadio Ciudad de México', '2026-06-11', '15:00:00', NULL, NULL, 'Fase de Grupos', 'México', 'Sudáfrica'),
(2, 'Estadio Guadalajara', '2026-06-11', '22:00:00', NULL, NULL, 'Fase de Grupos', 'República de Corea', 'República Checa'),

-- Viernes, 12 de junio 2026
(3, 'Estadio Toronto', '2026-06-12', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Canadá', 'Bosnia y Herzegovina'),
(4, 'Estadio Los Ángeles', '2026-06-12', '21:00:00', NULL, NULL, 'Fase de Grupos', 'Estados Unidos', 'Paraguay'),

-- Sábado, 13 de junio 2026
(5, 'Estadio Bahía de San Francisco', '2026-06-13', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Catar', 'Suiza'),
(6, 'Estadio Nueva York Nueva Jersey', '2026-06-13', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Brasil', 'Marruecos'),
(7, 'Estadio Boston', '2026-06-13', '21:00:00', NULL, NULL, 'Fase de Grupos', 'Haití', 'Escocia'),
(8, 'Estadio BC Place Vancouver', '2026-06-13', '00:00:00', NULL, NULL, 'Fase de Grupos', 'Australia', 'Turquía'),

-- Domingo, 14 de junio 2026
(9, 'Estadio Houston', '2026-06-14', '13:00:00', NULL, NULL, 'Fase de Grupos', 'Alemania', 'Curazao'),
(10, 'Estadio Dallas', '2026-06-14', '16:00:00', NULL, NULL, 'Fase de Grupos', 'Países Bajos', 'Japón'),
(11, 'Estadio Filadelfia', '2026-06-14', '19:00:00', NULL, NULL, 'Fase de Grupos', 'Costa de Marfil', 'Ecuador'),
(12, 'Estadio Monterrey', '2026-06-14', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Suecia', 'Túnez'),

-- Lunes, 15 de junio 2026
(13, 'Estadio Atlanta', '2026-06-15', '12:00:00', NULL, NULL, 'Fase de Grupos', 'España', 'Cabo Verde'),
(14, 'Estadio Seattle', '2026-06-15', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Bélgica', 'Egipto'),
(15, 'Estadio Miami', '2026-06-15', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Arabia Saudí', 'Uruguay'),
(16, 'Estadio Los Ángeles', '2026-06-15', '21:00:00', NULL, NULL, 'Fase de Grupos', 'Irán', 'Nueva Zelanda'),

-- Martes, 16 de junio 2026
(17, 'Estadio Nueva York Nueva Jersey', '2026-06-16', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Francia', 'Senegal'),
(18, 'Estadio Boston', '2026-06-16', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Irak', 'Noruega'),
(19, 'Estadio Kansas City', '2026-06-16', '21:00:00', NULL, NULL, 'Fase de Grupos', 'Argentina', 'Argelia'),
(20, 'Estadio Bahía de San Francisco', '2026-06-16', '00:00:00', NULL, NULL, 'Fase de Grupos', 'Austria', 'Jordania'),

-- Miércoles, 17 de junio 2026
(21, 'Estadio Houston', '2026-06-17', '13:00:00', NULL, NULL, 'Fase de Grupos', 'Portugal', 'RD Congo'),
(22, 'Estadio Dallas', '2026-06-17', '16:00:00', NULL, NULL, 'Fase de Grupos', 'Inglaterra', 'Croacia'),
(23, 'Estadio Toronto', '2026-06-17', '19:00:00', NULL, NULL, 'Fase de Grupos', 'Ghana', 'Panamá'),
(24, 'Estadio Ciudad de México', '2026-06-17', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Uzbekistán', 'Colombia'),

-- Jueves, 18 de junio 2026
(25, 'Estadio Atlanta', '2026-06-18', '12:00:00', NULL, NULL, 'Fase de Grupos', 'República Checa', 'Sudáfrica'),
(26, 'Estadio Los Ángeles', '2026-06-18', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Suiza', 'Bosnia y Herzegovina'),
(27, 'Estadio BC Place Vancouver', '2026-06-18', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Canadá', 'Catar'),
(28, 'Estadio Guadalajara', '2026-06-18', '21:00:00', NULL, NULL, 'Fase de Grupos', 'México', 'República de Corea'),

-- Viernes, 19 de junio 2026
(29, 'Estadio Seattle', '2026-06-19', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Estados Unidos', 'Australia'),
(30, 'Estadio Boston', '2026-06-19', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Escocia', 'Marruecos'),
(31, 'Estadio Filadelfia', '2026-06-19', '21:00:00', NULL, NULL, 'Fase de Grupos', 'Brasil', 'Haití'),
(32, 'Estadio Bahía de San Francisco', '2026-06-19', '00:00:00', NULL, NULL, 'Fase de Grupos', 'Turquía', 'Paraguay'),

-- Sábado, 20 de junio 2026
(33, 'Estadio Houston', '2026-06-20', '13:00:00', NULL, NULL, 'Fase de Grupos', 'Países Bajos', 'Suecia'),
(34, 'Estadio Toronto', '2026-06-20', '16:00:00', NULL, NULL, 'Fase de Grupos', 'Alemania', 'Costa de Marfil'),
(35, 'Estadio Kansas City', '2026-06-20', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Ecuador', 'Curazao'),
(36, 'Estadio Monterrey', '2026-06-20', '00:00:00', NULL, NULL, 'Fase de Grupos', 'Túnez', 'Japón'),

-- Domingo, 21 de junio 2026
(37, 'Estadio Atlanta', '2026-06-21', '12:00:00', NULL, NULL, 'Fase de Grupos', 'España', 'Arabia Saudí'),
(38, 'Estadio Los Ángeles', '2026-06-21', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Bélgica', 'Irán'),
(39, 'Estadio Miami', '2026-06-21', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Uruguay', 'Cabo Verde'),
(40, 'Estadio BC Place Vancouver', '2026-06-21', '21:00:00', NULL, NULL, 'Fase de Grupos', 'Nueva Zelanda', 'Egipto'),

-- Lunes, 22 de junio 2026
(41, 'Estadio Dallas', '2026-06-22', '13:00:00', NULL, NULL, 'Fase de Grupos', 'Argentina', 'Austria'),
(42, 'Estadio Filadelfia', '2026-06-22', '17:00:00', NULL, NULL, 'Fase de Grupos', 'Francia', 'Irak'),
(43, 'Estadio Nueva York Nueva Jersey', '2026-06-22', '20:00:00', NULL, NULL, 'Fase de Grupos', 'Noruega', 'Senegal'),
(44, 'Estadio Bahía de San Francisco', '2026-06-22', '23:00:00', NULL, NULL, 'Fase de Grupos', 'Jordania', 'Argelia'),

-- Martes, 23 de junio 2026
(45, 'Estadio Houston', '2026-06-23', '13:00:00', NULL, NULL, 'Fase de Grupos', 'Portugal', 'Uzbekistán'),
(46, 'Estadio Boston', '2026-06-23', '16:00:00', NULL, NULL, 'Fase de Grupos', 'Inglaterra', 'Ghana'),
(47, 'Estadio Toronto', '2026-06-23', '19:00:00', NULL, NULL, 'Fase de Grupos', 'Panamá', 'Croacia'),
(48, 'Estadio Guadalajara', '2026-06-23', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Colombia', 'RD Congo'),

-- Miércoles, 24 de junio 2026
(49, 'Estadio BC Place Vancouver', '2026-06-24', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Suiza', 'Canadá'),
(50, 'Estadio Seattle', '2026-06-24', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Bosnia y Herzegovina', 'Catar'),
(51, 'Estadio Miami', '2026-06-24', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Escocia', 'Brasil'),
(52, 'Estadio Atlanta', '2026-06-24', '18:00:00', NULL, NULL, 'Fase de Grupos', 'Marruecos', 'Haití'),
(53, 'Estadio Ciudad de México', '2026-06-24', '21:00:00', NULL, NULL, 'Fase de Grupos', 'República Checa', 'México'),
(54, 'Estadio Monterrey', '2026-06-24', '21:00:00', NULL, NULL, 'Fase de Grupos', 'Sudáfrica', 'República de Corea'),

-- Jueves, 25 de junio 2026
(55, 'Estadio Filadelfia', '2026-06-25', '16:00:00', NULL, NULL, 'Fase de Grupos', 'Curazao', 'Costa de Marfil'),
(56, 'Estadio Nueva York Nueva Jersey', '2026-06-25', '16:00:00', NULL, NULL, 'Fase de Grupos', 'Ecuador', 'Alemania'),
(57, 'Estadio Dallas', '2026-06-25', '19:00:00', NULL, NULL, 'Fase de Grupos', 'Japón', 'Suecia'),
(58, 'Estadio Kansas City', '2026-06-25', '19:00:00', NULL, NULL, 'Fase de Grupos', 'Túnez', 'Países Bajos'),
(59, 'Estadio Los Ángeles', '2026-06-25', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Turquía', 'Estados Unidos'),
(60, 'Estadio Bahía de San Francisco', '2026-06-25', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Paraguay', 'Australia'),

-- Viernes, 26 de junio 2026
(61, 'Estadio Boston', '2026-06-26', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Noruega', 'Francia'),
(62, 'Estadio Toronto', '2026-06-26', '15:00:00', NULL, NULL, 'Fase de Grupos', 'Senegal', 'Irak'),
(63, 'Estadio Houston', '2026-06-26', '20:00:00', NULL, NULL, 'Fase de Grupos', 'Cabo Verde', 'Arabia Saudí'),
(64, 'Estadio Guadalajara', '2026-06-26', '20:00:00', NULL, NULL, 'Fase de Grupos', 'Uruguay', 'España'),
(65, 'Estadio Seattle', '2026-06-26', '23:00:00', NULL, NULL, 'Fase de Grupos', 'Egipto', 'Irán'),
(66, 'Estadio BC Place Vancouver', '2026-06-26', '23:00:00', NULL, NULL, 'Fase de Grupos', 'Nueva Zelanda', 'Bélgica'),

-- Sábado, 27 de junio 2026
(67, 'Estadio Nueva York Nueva Jersey', '2026-06-27', '17:00:00', NULL, NULL, 'Fase de Grupos', 'Panamá', 'Inglaterra'),
(68, 'Estadio Filadelfia', '2026-06-27', '17:00:00', NULL, NULL, 'Fase de Grupos', 'Croacia', 'Ghana'),
(69, 'Estadio Miami', '2026-06-27', '19:30:00', NULL, NULL, 'Fase de Grupos', 'Colombia', 'Portugal'),
(70, 'Estadio Atlanta', '2026-06-27', '19:30:00', NULL, NULL, 'Fase de Grupos', 'RD Congo', 'Uzbekistán'),
(71, 'Estadio Kansas City', '2026-06-27', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Argelia', 'Austria'),
(72, 'Estadio Dallas', '2026-06-27', '22:00:00', NULL, NULL, 'Fase de Grupos', 'Jordania', 'Argentina');
