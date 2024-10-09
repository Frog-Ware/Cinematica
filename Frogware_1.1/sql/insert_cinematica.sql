-- Active: 1723124682380@@127.0.0.1@3306@cinematica
-- Insertar empresas
INSERT INTO Empresa (nombreEmpresa, email, numeroTelefono, calle, numeroPuerta, localidad)
VALUES ('Cinematica', 'contacto@cinematica.com', '26960056', 'Calle 1', '123', 'Montevideo');

-- Insertar redes sociales de la empresa
INSERT INTO redesSociales (nombreEmpresa, redSocial, urlRS, logo)
VALUES ('Cinematica', 'Facebook', 'https://facebook.com/cinematica', 'logo_facebook.png'),
       ('Cinematica', 'Twitter', 'https://twitter.com/cinematica', 'logo_twitter.png');

-- Insertar cines
INSERT INTO Cine (nombreCine, calle, numeroPuerta, localidad, nombreEmpresa)
VALUES ('Costa Urbana', 'Av. Giannatassio', 'Km. 21', 'Ciudad de la Costa', 'Cinematica'),
       ('Tres Cruces', 'Bvar. Artigas', '1825', 'Montevideo', 'Cinematica'),
       ('Portones Shopping', 'Av. Italia', '5775', 'Montevideo', 'Cinematica'),
       ('Punta Carretas Shopping', 'José Ellauri', '350', 'Montevideo', 'Cinematica');

-- Insertar salas de cada cine
INSERT INTO Sala (nombreCine, numeroSala, largo, ancho)
VALUES ('Costa Urbana', 1, 17, 13), ('Costa Urbana', 2, 12, 8),
       ('Tres Cruces', 1, 17, 13), ('Tres Cruces', 2, 12, 8),
       ('Portones Shopping', 1, 20, 16), ('Portones Shopping', 2, 17, 13),
       ('Punta Carretas Shopping', 1, 17, 13), ('Punta Carretas Shopping', 2, 17, 13);

-- Insertar usuarios (token: token1)
INSERT INTO Usuario (email, nombre, apellido, imagenPerfil, passwd, token, numeroCelular)
VALUES ('wchocho@gmail.com','Washington','Chocho','imagen1.webp','343121f36388c204d0249a8285ee3f00','78b1e6d775cec5260001af137a79dbd5','091234567'), -- passwd: wchocho123
       ('mtorres@gmail.com','Marcelo','Torres','imagen4.webp','2045ef6212256f3f9acf3f07ce136006','78b1e6d775cec5260001af137a79dbd5','098901234'), -- passwd: mtorres123
       ('cmenendez@gmail.com','Christian','Menendez','imagen3.webp','b6ecabcb73a2f334a9260e344af62e1d','78b1e6d775cec5260001af137a79dbd5','095678901'), -- passwd: cmenendez123
       ('dcauto@outlook.com','Delis','Cauto','imagen2.webp','a000ee16202ce758c681432b5508d649','78b1e6d775cec5260001af137a79dbd5','092345678'), -- passwd: dcauto123
       ('jartigas@gmail.com','José','Artigas','imagen6.webp','151b3593f0d334ae3e56deb7291de891','78b1e6d775cec5260001af137a79dbd5','092345678'); -- passwd: jartigas123

-- Insertar empleados
INSERT INTO Empleado (email, esAdmin, nombreEmpresa)
VALUES ('wchocho@gmail.com',FALSE,'Cinematica'),
       ('mtorres@gmail.com',TRUE,'Cinematica'),
       ('cmenendez@gmail.com',FALSE,'Cinematica');

-- Insertar clientes
INSERT INTO Cliente (email, numeroTarjeta)
VALUES ('dcauto@outlook.com','111122223333'),
       ('jartigas@gmail.com','111133332222');

-- Insertar productos (películas y artículos)
INSERT INTO Producto (idProducto)
VALUES (100000001), (100000002), (100000003), (100000004), (100000005), (100000006), (100000007), (100000008), (100000009), (100000010), (100000011), (200000001), (200000002), (200000003), (200000004), (200000005), (200000006), (200000007), (200000008), (200000009), (200000010), (200000011), (200000012), (200000013), (200000014), (200000015), (200000016), (200000017), (200000018);

-- Insertar artículos
INSERT INTO Articulo (idProducto, nombreArticulo, descripcion, precio, imagen)
VALUES (200000001, 'Agua', '500ml', 110, 'agua.webp'),
       (200000002, 'CocaCola Mediana', '500ml', 170, 'cocacola_mediana.webp'),
       (200000003, 'Combo 1', 'Combo de Pop y Bebida', 350, 'combo_1.webp'),
       (200000004, 'Combo 2', 'Combo de Pop y Bebida', 450, 'combo_2.webp'),
       (200000005, 'Combo 3', 'Combo de Pop y Bebida', 550, 'combo_3.webp'),
       (200000006, 'Doritos', 'Doritos de 120g', 190, 'doritos.webp'),
       (200000007, 'Doritos Chicos', 'Doritos de 45g', 90, 'doritos_chicos.webp'),
       (200000008, 'CocaCola en lata', '450ml', 155, 'cocacola_en_lata.webp'),
       (200000009, 'Sprite en lata', '450ml', 155, 'sprite_en_lata.webp'),
       (200000010, 'Monster', '600ml', 190, 'monster.webp'),
       (200000011, 'Monster Ultra', '600ml', 190, 'monster_ultra.webp'),
       (200000012, 'Papas Chips', 'Papas Chips de 95g', 140, 'papas_chips.webp'),
       (200000013, 'Papas Lays', 'Papas Lays de 95g', 140, 'papas_lays.webp'),
       (200000014, 'Pop Mini', 'Pop de 200g', 190, 'pop_mini.webp'),
       (200000015, 'Pop Mediano', 'Pop de 300g', 270, 'pop_mediano.webp'),
       (200000016, 'Pop Grande', 'Pop de 400g', 340, 'pop_grande.webp'),
       (200000017, 'Pop XL', 'Pop de 500g', 420, 'pop_xl.webp'),
       (200000018, 'Pop Giga', 'Pop de 750g', 590, 'pop_giga.webp');

-- Insertar películas
INSERT INTO Pelicula (idProducto, actores, sinopsis, duracion, nombrePelicula, pegi, trailer, director, poster, cabecera) VALUES
(100000001, 'Gerard Butler, Lena Headey, David Wenham', 'Rey Leonidas de Esparta y un ejército de 300 hombres luchan contra el Imperio persa.', '01:57:00', '300', 'R', 'https://www.youtube.com/watch?v=UrIbxk7idYA', 'Zack Snyder', '300_poster.webp', '300_cabecera.webp'),
(100000002, 'Cate Blanchett, Kevin Hart, Jamie Lee Curtis', 'Cazadores de bóvedas enfrentan criaturas salvajes y un villano tiránico en un planeta alienígena.', '02:00:00', 'Borderlands', 'PG-13', 'https://www.youtube.com/watch?v=link-borderlands', 'Eli Roth', 'borderlands_poster.webp', 'borderlands_cabecera.webp'),
(100000003, 'Ryan Reynolds, Hugh Jackman, Morena Baccarin', 'Deadpool y Wolverine unen fuerzas para enfrentarse a nuevas amenazas en esta aventura llena de acción y humor irreverente.', '02:15:00', 'Deadpool y Wolverine', 'R', 'https://www.youtube.com/watch?v=link-deadpool-wolverine', 'Shawn Levy', 'deadpool_y_wolverine_poster.webp', 'deadpool_y_wolverine_cabecera.webp'),
(100000004, 'Colin Farrell, Cristin Milioti, Michael Zegen', 'Serie sobre el ascenso del infame villano de Gotham, el Pingüino.', '00:45:00', 'El Pingüino', 'TV-MA', 'https://www.youtube.com/watch?v=link-pinguino', 'Craig Zobel', 'el_pinguino_poster.webp', 'el_pinguino_cabecera.webp'),
(100000005, 'Andy Serkis, Woody Harrelson, Steve Zahn', 'Humanos y simios están en guerra por la supervivencia de sus especies.', '02:20:00', 'El Planeta de los Simios', 'PG-13', 'https://www.youtube.com/watch?v=qxjPjPzQ1iU', 'Matt Reeves', 'el_planeta_de_los_simios_poster.webp', 'el_planeta_de_los_simios_cabecera.webp'),
(100000006, 'Bill Murray, Chris Pratt, Samuel L. Jackson', 'Garfield, el gato sarcástico, se enfrenta a nuevas aventuras.', '01:20:00', 'Garfield', 'PG', 'https://www.youtube.com/watch?v=link-garfield', 'Peter Hewitt', 'garfield_poster.webp', 'garfield_cabecera.webp'),
(100000007, 'David Harbour, Orlando Bloom, Archie Madekwe', 'Un joven piloto de autos de carrera tiene la oportunidad de competir profesionalmente.', '02:15:00', 'Gran Turismo', 'PG-13', 'https://www.youtube.com/watch?v=link-granturismo', 'Neill Blomkamp', 'gran_turismo_poster.webp', 'gran_turismo_cabecera.webp'),
(100000008, 'Joaquin Phoenix, Robert De Niro, Zazie Beetz', 'En Gotham, un comediante con problemas mentales se convierte en el icónico Joker.', '02:02:00', 'Joker', 'R', 'https://www.youtube.com/watch?v=zAGVQLHvwOY', 'Todd Phillips', 'joker_poster.webp', 'joker_cabecera.webp'),
(100000009, 'Shia LaBeouf, Megan Fox, Josh Duhamel', 'Robots alienígenas se enfrentan en la Tierra para protegerla o destruirla.', '02:24:00', 'Transformers', 'PG-13', 'https://www.youtube.com/watch?v=link-transformers', 'Michael Bay', 'transformers_poster.webp', 'transformers_cabecera.webp'),
(100000010, 'Daisy Edgar-Jones, Glen Powell, David Strathairn', 'Un grupo de cazadores de tormentas se enfrenta a tornados devastadores.', '01:55:00', 'Twisters', 'PG-13', 'https://www.youtube.com/watch?v=link-twisters', 'Lee Isaac Chung', 'twisters_poster.webp', 'twisters_cabecera.webp'),
(100000011, 'Joey King, Liza Koshy, Chase Stokes', 'En un mundo distópico, la sociedad está dividida en bellos y feos.', '01:38:00', 'Uglies', 'PG-13', 'https://www.youtube.com/watch?v=link-uglies', 'McG', 'uglies_poster.webp', 'uglies_cabecera.webp');

-- Insertar cartelera
INSERT INTO Cartelera (idProducto)
VALUES (100000001), (100000002), (100000003), (100000004), (100000005), (100000006), (100000007), (100000008);

-- Insertar idiomas
INSERT INTO Idiomas (idioma)
VALUES ('Español'), ('Inglés'), ('Italiano');

-- Insertar categorías
INSERT INTO Categorias (nombreCategoria)
VALUES ('Acción'), ('Historia'), ('Ciencia Ficción'), ('Aventura'), ('Comedia'), ('Animación'), ('Deportes'), ('Drama'), ('Crimen'), ('Thriller');

-- Insertar dimensiones
INSERT INTO Dimensiones (dimension, precio)
VALUES ('2D', 440), ('3D', 490);

-- Relacionar productos con idiomas
INSERT INTO TieneIdiomas (idioma, idProducto) VALUES
('Inglés', 100000001), ('Español', 100000001),
('Inglés', 100000002), ('Español', 100000002),
('Inglés', 100000003), ('Español', 100000003),
('Inglés', 100000004), ('Español', 100000004),
('Inglés', 100000005), ('Español', 100000005),
('Inglés', 100000006), ('Español', 100000006),
('Inglés', 100000007), ('Español', 100000007),
('Inglés', 100000008), ('Español', 100000008),
('Inglés', 100000009), ('Español', 100000009),
('Inglés', 100000010), ('Español', 100000010),
('Inglés', 100000011), ('Español', 100000011);

-- Relacionar productos con categorías
INSERT INTO TieneCategorias (nombreCategoria, idProducto) VALUES
('Acción', 100000001), ('Historia', 100000001), -- 300
('Aventura', 100000002), ('Ciencia ficción', 100000002), -- Borderlands
('Acción', 100000003), ('Comedia', 100000003), -- Deadpool y Wolverine
('Acción', 100000004), ('Ciencia ficción', 100000004), -- El Pingüino
('Ciencia ficción', 100000005), ('Aventura', 100000005), -- El Planeta de los Simios
('Comedia', 100000006), ('Animación', 100000006), -- Garfield
('Acción', 100000007), ('Deportes', 100000007), -- Gran Turismo
('Drama', 100000008), ('Crimen', 100000008), -- Joker
('Acción', 100000009), ('Ciencia ficción', 100000009), -- Transformers
('Acción', 100000010), ('Thriller', 100000010), -- Twisters
('Ciencia ficción', 100000011), ('Drama', 100000011); -- Uglies

-- Relacionar productos con dimensiones
INSERT INTO TieneDimensiones (dimension, idProducto) VALUES
('2D', 100000001), ('3D', 100000001),
('2D', 100000002), ('3D', 100000002),
('2D', 100000003), ('3D', 100000003),
('2D', 100000004), ('3D', 100000004),
('2D', 100000005),
('2D', 100000006), ('3D', 100000006),
('2D', 100000007),
('2D', 100000008), ('3D', 100000008),
('2D', 100000009),
('2D', 100000010), ('3D', 100000010),
('2D', 100000011);

INSERT INTO Funciones (idFuncion, idProducto, nombreCine, numeroSala, fechaPelicula, horaPelicula, dimension)
VALUES (900000001, 100000001, 'Costa Urbana', 1, '2024-11-22', '19:10:00', '3D'),
       (900000002, 100000006, 'Costa Urbana', 2, '2024-11-22', '18:00:00', '3D'),
       (900000003, 100000003, 'Tres Cruces', 2, '2024-11-22', '17:50:00', '2D'),
       (900000004, 100000008, 'Costa Urbana', 1, '2024-11-22', '21:45:00', '3D'),
       (900000005, 100000005, 'Costa Urbana', 2, '2024-11-22', '20:40:00', '2D'),
       (900000006, 100000004, 'Tres Cruces', 1, '2024-11-22', '19:40:00', '3D'),
       (900000007, 100000002, 'Tres Cruces', 2, '2024-11-22', '20:30:00', '2D'),
       (900000008, 100000007, 'Costa Urbana', 1, '2024-11-23', '18:00:00', '2D'),
       (900000009, 100000003, 'Costa Urbana', 2, '2024-11-23', '18:30:00', '3D'),
       (900000010, 100000008, 'Tres Cruces', 2, '2024-11-23', '17:50:00', '2D'),
       (900000011, 100000002, 'Costa Urbana', 1, '2024-11-23', '20:45:00', '3D'),
       (900000012, 100000004, 'Costa Urbana', 2, '2024-11-23', '21:40:00', '2D'),
       (900000013, 100000005, 'Tres Cruces', 1, '2024-11-23', '19:30:00', '2D'),
       (900000014, 100000006, 'Tres Cruces', 2, '2024-11-23', '20:30:00', '3D');