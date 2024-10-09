-- Active: 1723124682380@@127.0.0.1@3306@cinematica
DROP DATABASE IF EXISTS Cinematica;
CREATE DATABASE Cinematica;
USE Cinematica;

DROP TABLE IF EXISTS Empresa;
CREATE TABLE Empresa (
    nombreEmpresa varchar(50),
    email varchar(50),
    numeroTelefono varchar(9),
    calle varchar(50),
    numeroPuerta varchar(10),
    localidad varchar(50),
    PRIMARY KEY (nombreEmpresa)
);

DROP TABLE IF EXISTS redesSociales;
CREATE TABLE redesSociales (
    nombreEmpresa varchar(50),
    redSocial varchar(20),
    urlRS varchar(250),
    logo varchar(1000),
    FOREIGN KEY (nombreEmpresa) REFERENCES Empresa(nombreEmpresa),
    PRIMARY KEY (nombreEmpresa, redSocial)
);

DROP TABLE IF EXISTS Cine;
CREATE TABLE Cine (
    nombreCine varchar(50),
    calle varchar(50),
    numeroPuerta varchar(10),
    localidad varchar(50),
    nombreEmpresa varchar(50),
    FOREIGN KEY (nombreEmpresa) REFERENCES Empresa(nombreEmpresa),
    PRIMARY KEY (nombreCine)
);

DROP TABLE IF EXISTS Sala;
CREATE TABLE Sala (
    nombreCine varchar(50),
    numeroSala int,
    largo int,
    ancho int,
    FOREIGN KEY (nombreCine) REFERENCES Cine(nombreCine),
    PRIMARY KEY (nombreCine, numeroSala)
);

DROP TABLE IF EXISTS Usuario;
CREATE TABLE Usuario (
    email varchar(50),
    nombre varchar(20),
    apellido varchar(20),
    imagenPerfil varchar(50),
    passwd varchar(50),
    token varchar(50),
    numeroCelular varchar(9),
    PRIMARY KEY (email)
);

DROP TABLE IF EXISTS Empleado;
CREATE TABLE Empleado (
    email varchar(50),
    esAdmin boolean,
    nombreEmpresa varchar(50),
    FOREIGN KEY (email) REFERENCES Usuario(email),
    FOREIGN KEY (nombreEmpresa) REFERENCES Empresa(nombreEmpresa),
    PRIMARY KEY (email)
);

DROP TABLE IF EXISTS Cliente;
CREATE TABLE Cliente (
    email varchar(50),
    numeroTarjeta int,
    FOREIGN KEY (email) REFERENCES Usuario(email),
    PRIMARY KEY (email)
);

DROP TABLE IF EXISTS Producto;
CREATE TABLE Producto (
    idProducto int,
    PRIMARY KEY (idProducto)
);

DROP TABLE IF EXISTS Articulo;
CREATE TABLE Articulo (
    idProducto int,
    nombreArticulo varchar(50),
    descripcion varchar(250),
    precio int,
    imagen varchar(60),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    PRIMARY KEY (idProducto)
);

DROP TABLE IF EXISTS Pelicula;
CREATE TABLE Pelicula (
    idProducto int,
    actores varchar(250),
    sinopsis varchar(750),
    duracion time,
    nombrePelicula varchar(50),
    pegi varchar(10),
    trailer varchar(250),
    director varchar(50),
    poster varchar(60),
    cabecera varchar(60),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    PRIMARY KEY (idProducto)
);

DROP TABLE IF EXISTS Cartelera;
CREATE TABLE Cartelera (
    idProducto int,
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    PRIMARY KEY (idProducto)
);

DROP TABLE IF EXISTS Idiomas;
CREATE TABLE Idiomas (
    idioma varchar(20),
    PRIMARY KEY (idioma)
);

DROP TABLE IF EXISTS Categorias;
CREATE TABLE Categorias (
    nombreCategoria varchar(20),
    PRIMARY KEY (nombreCategoria)
);

DROP TABLE IF EXISTS Dimensiones;
CREATE TABLE Dimensiones (
    dimension varchar(2),
    precio int,
    PRIMARY KEY (dimension)
);

DROP TABLE IF EXISTS TieneIdiomas;
CREATE TABLE TieneIdiomas (
    idioma varchar(20),
    idProducto int,
    FOREIGN KEY (idioma) REFERENCES Idiomas(idioma),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    PRIMARY KEY (idioma, idProducto)
);

DROP TABLE IF EXISTS TieneCategorias;
CREATE TABLE TieneCategorias (
    nombreCategoria varchar(20),
    idProducto int,
    FOREIGN KEY (nombreCategoria) REFERENCES Categorias(nombreCategoria),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    PRIMARY KEY (nombreCategoria, idProducto)
);

DROP TABLE IF EXISTS TieneDimensiones;
CREATE TABLE TieneDimensiones (
    dimension varchar(2),
    idProducto int,
    FOREIGN KEY (dimension) REFERENCES Dimensiones(dimension),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    PRIMARY KEY (dimension, idProducto)
);

DROP TABLE IF EXISTS Funciones;
CREATE TABLE Funciones (
    idFuncion int,
    idProducto int,
    nombreCine varchar(50),
    numeroSala int,
    fechaPelicula date,
    horaPelicula time,
    dimension varchar(2),
    asientosOcupados varchar(250),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    FOREIGN KEY (nombreCine, numeroSala) REFERENCES Sala(nombreCine, numeroSala),
    FOREIGN KEY (dimension) REFERENCES Dimensiones(dimension),
    PRIMARY KEY (idFuncion)
);

DROP TABLE IF EXISTS Asientos;
CREATE TABLE Asientos (
    idFuncion int,
    fila int,
    columna int,
    FOREIGN KEY (idFuncion) REFERENCES Funciones(idFuncion)
);

DROP TABLE IF EXISTS Carrito;
CREATE TABLE Carrito (
    email varchar(50),
    idFuncion int,
    asientos varchar(50),
    FOREIGN KEY (email) REFERENCES Usuario(email),
    FOREIGN KEY (idFuncion) REFERENCES Funciones(idFuncion),
    PRIMARY KEY (email)
);

DROP TABLE IF EXISTS CarritoArticulo;
CREATE TABLE CarritoArticulo (
    email varchar(50),
    idProducto int,
    cantidad int,
    FOREIGN KEY (email) REFERENCES Usuario(email),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto)
);

DROP TABLE IF EXISTS Compra;
CREATE TABLE Compra (
    idCompra int,
    email varchar(50),
    idFuncion int,
    fechaCompra date,
    asientos varchar(50),
    precio int,
    FOREIGN KEY (email) REFERENCES Usuario(email),
    FOREIGN KEY (idFuncion) REFERENCES Funciones(idFuncion),
    PRIMARY KEY (idCompra)
);

DROP TABLE IF EXISTS CompraArticulo;
CREATE TABLE CompraArticulo (
    idCompra int,
    idProducto int,
    cantidad int,
    FOREIGN KEY (idCompra) REFERENCES Compra(idCompra),
    FOREIGN KEY (idProducto) REFERENCES Producto(idProducto),
    PRIMARY KEY (idCompra, idProducto)
);