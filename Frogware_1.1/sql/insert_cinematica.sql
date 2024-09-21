-- Active: 1723124682380@@127.0.0.1@3306@cinematica
-- Insertar empresas
INSERT INTO Empresa (nombreEmpresa, email, numeroTelefono, calle, numeroPuerta, localidad)
VALUES ('Cinematica', 'contacto@cinematica.com', '26960056', 'Calle 1', '123', 'Montevideo');

-- Insertar redes sociales
INSERT INTO redesSociales (nombreEmpresa, redSocial, urlRS, logo)
VALUES ('Cinematica', 'Facebook', 'https://facebook.com/cinematica', 'logo_facebook.png'),
       ('Cinematica', 'Twitter', 'https://twitter.com/cinematica', 'logo_twitter.png');

-- Insertar Usuarios (token: token1)
INSERT INTO Usuario (email, nombre, apellido, imagenPerfil, passwd, token, numeroCelular)
VALUES ('wchocho@gmail.com','Washington','Chocho','imagen1.webp','343121f36388c204d0249a8285ee3f00','78b1e6d775cec5260001af137a79dbd5','091234567'), -- passwd: wchocho123
       ('mtorres@gmail.com','Marcelo','Torres','imagen4.webp','2045ef6212256f3f9acf3f07ce136006','78b1e6d775cec5260001af137a79dbd5','098901234'), -- passwd: mtorres123
       ('cmenendez@gmail.com','Christian','Menendez','imagen3.webp','b6ecabcb73a2f334a9260e344af62e1d','78b1e6d775cec5260001af137a79dbd5','095678901'), -- passwd: cmenendez123
       ('dcauto@outlook.com','Delis','Cauto','imagen2.webp','a000ee16202ce758c681432b5508d649','78b1e6d775cec5260001af137a79dbd5','092345678'), -- passwd: dcauto123
       ('jartigas@gmail.com','José','Artigas','imagen6.webp','151b3593f0d334ae3e56deb7291de891','78b1e6d775cec5260001af137a79dbd5','092345678'); -- passwd: jartigas123

INSERT INTO Empleado (email, esAdmin, nombreEmpresa)
VALUES ('wchocho@gmail.com',FALSE,'Cinematica'),
       ('mtorres@gmail.com',TRUE,'Cinematica'),
       ('cmenendez@gmail.com',FALSE,'Cinematica');

INSERT INTO Cliente (email, numeroTarjeta)
VALUES ('dcauto@outlook.com','111122223333'),
       ('jartigas@gmail.com','111133332222');

-- Insertar productos (Películas y Artículos)
INSERT INTO Producto (idProducto)
VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9), (10), (11), (12), (13), (14), (15);

-- Insertar películas
INSERT INTO Pelicula (idProducto, actores, sinopsis, duracion, nombrePelicula, pegi, trailer, director, poster, cabecera)
VALUES (1, 'Leonardo DiCaprio, Joseph Gordon-Levitt, Ellen Page', 'Un ladrón que roba secretos corporativos mediante el uso de tecnología para compartir sueños recibe la tarea inversa de plantar una idea en la mente de un CEO.', '02:28:00', 'El Origen', 'PG-13', 'https://www.youtube.com/watch?v=YoHD9XEInc0', 'Christopher Nolan', 'el_origen_poster.webp', 'el_origen_cabecera.webp'),
       (3, 'Keanu Reeves, Laurence Fishburne, Carrie-Anne Moss', 'Un hacker informático aprende de rebeldes misteriosos sobre la verdadera naturaleza de su realidad y su papel en la guerra contra sus controladores.', '02:16:00', 'Matrix', 'PG-18', 'https://www.youtube.com/watch?v=vKQi3bBA1y8', 'Lana Wachowski, Lilly Wachowski', 'matrix_poster.webp', 'matrix_cabecera.webp'),
       (4, 'Robert Downey Jr., Chris Evans, Mark Ruffalo', 'Los héroes más poderosos de la Tierra deben unirse y aprender a luchar como equipo si quieren detener al travieso Loki y su ejército alienígena de esclavizar a la humanidad.', '02:23:00', 'Los Vengadores', 'PG-13', 'https://www.youtube.com/watch?v=eOrNdBpGMv8', 'Joss Whedon', 'los_vengadores_poster.webp', 'los_vengadores_cabecera.webp'),
       (6, 'Elijah Wood, Ian McKellen, Orlando Bloom', 'Un Hobbit tímido de la Comarca y ocho compañeros emprenden un viaje para destruir el poderoso Anillo Único y salvar la Tierra Media del Señor Oscuro Sauron.', '03:48:00', 'El Señor de los Anillos', 'PG-13', 'https://www.youtube.com/watch?v=V75dMMIW2B4', 'Peter Jackson', 'el_señor_de_los_anillos_poster.webp', 'el_señor_de_los_anillos_cabecera.webp'),
       (7, 'Tom Hanks, Robin Wright, Gary Sinise', 'La historia de un hombre con un coeficiente intelectual bajo que ha tenido una vida llena de eventos extraordinarios.', '02:22:00', 'Forrest Gump', 'PG-13', 'https://www.youtube.com/watch?v=bLvqoHBptjg', 'Robert Zemeckis', 'forrest_gump_poster.webp', 'forrest_gump_cabecera.webp'),
       (8, 'Marlon Brando, Al Pacino, James Caan', 'La crónica de la familia criminal Corleone bajo el patriarca Vito Corleone, centrándose en la transformación de su hijo menor, Michael, de un forastero reacio a un despiadado jefe de la mafia.', '02:55:00', 'El Padrino', 'PG-18', 'https://www.youtube.com/watch?v=sY1S34973zA', 'Francis Ford Coppola', 'el_padrino_poster.webp', 'el_padrino_cabecera.webp'),
       (11, 'Christian Bale, Heath Ledger, Aaron Eckhart', 'Cuando la amenaza conocida como el Joker emerge de su misterioso pasado, causa estragos y caos en la gente de Gotham. El Caballero Oscuro debe aceptar uno de los mayores desafíos psicológicos y físicos de su capacidad para luchar contra la injusticia.', '02:32:00', 'El Caballero Oscuro', 'PG-13', 'https://www.youtube.com/watch?v=EXeTwQWrcwY', 'Christopher Nolan', 'el_caballero_oscuro_poster.webp', 'el_caballero_oscuro_cabecera.webp'),
       (12, 'Gerard Butler, Lena Headey, David Wenham', 'Basada en la histórica Batalla de las Termópilas, el rey espartano Leónidas y 300 espartanos luchan hasta la muerte contra el vasto ejército persa del rey Jerjes.', '01:57:00', '300', 'PG-18', 'https://www.youtube.com/watch?v=UrIbxk7idYA', 'Zack Snyder', '300_poster.webp', '300_cabecera.webp');

-- Insertar artículos
INSERT INTO Articulo (idProducto, nombreArticulo, descripcion, precio, imagen)
VALUES (2, 'Pop Chico', '130g de Pop', 210, 'pop_chico.webp'),
       (5, 'Pop Grande', '250g de Pop', 370, 'pop_grande.webp'),
       (9, 'Bebida Chica', 'Bebida de 300ml', 140, 'bebida_chica.webp'),
       (10, 'Bebida Grande', 'Bebida de 500ml', 180, 'bebida_grande.webp'),
       (13, 'Combo 1', 'Combo Pop 130g + Bebida de 300ml', 315, 'combo_1.webp'),
       (14, 'Combo 2', 'Combo Pop 250g + Bebida de 500ml', 495, 'combo_2.webp'),
       (15, 'Combo Familiar', 'Combo Pop 250g + 2 Bebidas de 300ml', 590, 'combo_familiar.webp');

-- Insertar idiomas
INSERT INTO Idiomas (idioma)
VALUES ('Español'), ('Inglés'), ('Italiano');

-- Insertar categorías
INSERT INTO Categorias (nombreCategoria)
VALUES ('Acción'), ('Drama'), ('Ciencia Ficción'), ('Aventura'), ('Crimen'), ('Fantasía'), ('Romance'), ('Comedia'), ('Suspenso');

-- Insertar dimensiones
INSERT INTO Dimensiones (dimension, precio)
VALUES ('2D', 440), ('3D', 490);

-- Relacionar productos con idiomas
INSERT INTO TieneIdiomas (idioma, idProducto)
VALUES ('Español', 1), ('Inglés', 1), ('Español', 3), ('Inglés', 3), ('Español', 4), ('Inglés', 4), ('Español', 6), ('Español', 7), ('Inglés', 7), ('Español', 8), ('Italiano', 8), ('Español', 11), ('Inglés', 11), ('Español', 12);

-- Relacionar productos con categorías
INSERT INTO TieneCategorias (nombreCategoria, idProducto)
VALUES ('Ciencia Ficción', 1), -- El Origen
       ('Acción', 1),
       ('Ciencia Ficción', 3), -- Matrix
       ('Acción', 3),
       ('Acción', 4), -- Los Vengadores
       ('Aventura', 4), 
       ('Fantasía', 6), -- El Señor de los Anillos: La Comunidad del Anillo
       ('Aventura', 6), 
       ('Drama', 7), -- Forrest Gump
       ('Romance', 7), 
       ('Crimen', 8), -- El Padrino
       ('Drama', 8), 
       ('Acción', 11), -- El Caballero Oscuro
       ('Crimen', 11),
       ('Acción', 12), -- 300
       ('Drama', 12);

-- Relacionar productos con dimensiones
INSERT INTO TieneDimensiones (dimension, idProducto)
VALUES ('2D', 1), ('3D', 1), ('2D', 3), ('2D', 4), ('2D', 6), ('3D', 6), ('2D', 7), ('2D', 8), ('2D', 11), ('3D', 11), ('2D', 12);

-- Insertar cartelera
INSERT INTO Cartelera (idProducto)
VALUES (1), (3), (4), (6), (7), (8), (11), (12);

INSERT INTO Cine (nombreCine, calle, numeroPuerta, localidad, nombreEmpresa)
VALUES ('Costa Urbana', 'Av. Giannatassio', 'Km. 21', 'Ciudad de la Costa', 'Cinematica'),
       ('Tres Cruces', 'Bvar. Artigas', '1825', 'Montevideo', 'Cinematica'),
       ('Portones Shopping', 'Av. Italia', '5775', 'Montevideo', 'Cinematica'),
       ('Punta Carretas Shopping', 'José Ellauri', '350', 'Montevideo', 'Cinematica');

INSERT INTO Sala (nombreCine, numeroSala, capacidad)
VALUES ('Costa Urbana', 1, '17x13'), ('Costa Urbana', 2, '12x8'),
       ('Tres Cruces', 1, '17x13'), ('Tres Cruces', 2, '12x8'),
       ('Portones Shopping', 1, '17x13'), ('Portones Shopping', 2, '12x8'),
       ('Punta Carretas Shopping', 1, '17x13'), ('Punta Carretas Shopping', 2, '12x8');

INSERT INTO Funciones (idFuncion, idProducto, nombreCine, numeroSala, fechaPelicula, horaPelicula, dimension)
VALUES (1, 1, 'Costa Urbana', 1, '2024-09-29', '19:10:00', '3D'),
       (2, 6, 'Costa Urbana', 2, '2024-09-29', '19:50:00', '3D'),
       (3, 3, 'Tres Cruces', 2, '2024-09-29', '21:00:00', '2D'),
       (4, 8, 'Costa Urbana', 1, '2024-09-30', '18:30:00', '2D'),
       (5, 12, 'Costa Urbana', 2, '2024-09-30', '19:30:00', '2D'),
       (6, 7, 'Tres Cruces', 1, '2024-09-30', '19:30:00', '2D'),
       (7, 11, 'Tres Cruces', 2, '2024-09-30', '21:10:00', '3D'),
       (8, 1, 'Costa Urbana', 1, '2024-10-01', '18:00:00', '3D'),
       (9, 6, 'Costa Urbana', 2, '2024-10-01', '18:30:00', '3D'),
       (10, 3, 'Tres Cruces', 2, '2024-10-01', '17:50:00', '2D'),
       (11, 8, 'Costa Urbana', 1, '2024-10-01', '20:45:00', '2D'),
       (12, 12, 'Costa Urbana', 2, '2024-10-01', '21:40:00', '2D'),
       (13, 7, 'Tres Cruces', 1, '2024-10-01', '19:30:00', '2D'),
       (14, 11, 'Tres Cruces', 2, '2024-10-01', '20:30:00', '3D');