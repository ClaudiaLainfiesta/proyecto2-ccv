CREATE DATABASE proyecto_final_CCV;
USE proyecto_final_CCV;

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
	CHECK (pais_visitante <> pais_local)
);


CREATE TABLE Usuario (
	username varchar(100),
	nombre varchar(100),
	contrasena varchar(100),
	PRIMARY KEY (username)
);


CREATE TABLE Prediccion (
	codigo_partido integer,

	username varchar(100),

	goles_local_prediccion integer,
	goles_visitante_prediccion integer,
	puntos_prediccion integer,

	PRIMARY KEY (codigo_partido, username),
	FOREIGN KEY (codigo_partido) REFERENCES Partido(codigo_partido) ON DELETE CASCADE,
	FOREIGN KEY (username) REFERENCES Usuario(username) ON DELETE CASCADE
);