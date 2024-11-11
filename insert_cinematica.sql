-- Active: 1723124682380@@127.0.0.1@3306@cinematica
-- Insertar empresas
INSERT INTO Empresa (nombreEmpresa, email, passwd, numeroTelefono, calle, numeroPuerta, localidad)
VALUES ('Cinematica', 'cinematicaescine@gmail.com', 'qrfw svuy mvbt vuoi', '26960056', 'Calle 1', '123', 'Montevideo');

-- Insertar redes sociales de la empresa
INSERT INTO RedesSociales (nombreEmpresa, redSocial, urlRS, logo)
VALUES ('Cinematica', 'Gmail', 'https://mail.google.com/mail/?view=cm&to=cinemaricaescine@gmail.com', 'bx-gmail'),
       ('Cinematica', 'Instagram', 'https://www.instagram.com/', 'bx-instagram'),
       ('Cinematica', 'Facebook', 'https://www.facebook.com/', 'bx-facebook'),
       ('Cinematica', 'Linkedin', 'https://www.linkedin.com/', 'bx-linkedin');

-- Insertar cines
INSERT INTO Cine (nombreCine, calle, numeroPuerta, localidad, nombreEmpresa)
VALUES ('Costa Urbana', 'Av. Giannatassio', 'Km. 21', 'Ciudad de la Costa', 'Cinematica'),
       ('Tres Cruces', 'Bvar. Artigas', '1825', 'Montevideo', 'Cinematica'),
       ('Portones Shopping', 'Av. Italia', '5775', 'Montevideo', 'Cinematica'),
       ('Punta Carretas Shopping', 'José Ellauri', '350', 'Montevideo', 'Cinematica'),
       ('Cine Chaplin', 'Luis A. De Herrera', '639', 'Melo', 'Cinematica'),
       ('Cine Miramar', 'Rbla. de los Argentinos', '1124', 'Piriápolis', 'Cinematica');

-- Insertar salas de cada cine
INSERT INTO Sala (nombreCine, numeroSala, ancho, largo)
VALUES ('Costa Urbana', 1, 17, 13), ('Costa Urbana', 2, 14, 10),
       ('Tres Cruces', 1, 17, 13), ('Tres Cruces', 2, 14, 10),
       ('Portones Shopping', 1, 17, 13), ('Portones Shopping', 2, 17, 13),
       ('Punta Carretas Shopping', 1, 17, 13), ('Punta Carretas Shopping', 2, 17, 13),
       ('Cine Chaplin', 1, 14, 10), ('Cine Chaplin', 2, 12, 8),
       ('Cine Miramar', 1, 12, 8), ('Cine Miramar', 2, 12, 8);
       
INSERT INTO ImagenPerfil (imagenPerfil) 
VALUES ('venom.webp'), ('iron_man.webp'), ('morgan.webp'), ('robin.webp'), ('T-800.webp'),
       ('xenomorfo.webp'), ('rambo.webp'), ('will.webp'), ('doc_emmeth_brown.webp'), ('imagen1.webp'), ('imagen8.webp'), ('default.webp');

INSERT INTO ImagenSlider (imagenSlider)
VALUES ('300-slider.webp'), ('ForrestGump.webp'), ('GT-slider.webp'), ('movieTime.webp');

INSERT INTO Usuario (email, nombre, apellido, imagenPerfil, passwd, numeroCelular)
VALUES ('wchocho@gmail.com','Washington','Chocho','default.webp','343121f36388c204d0249a8285ee3f00','091234567'), -- passwd: wchocho123
       ('mtorres@gmail.com','Marcelo','Torres','default.webp','2045ef6212256f3f9acf3f07ce136006','098901234'), -- passwd: mtorres123
       ('cmenendez@gmail.com','Christian','Menendez','default.webp','b6ecabcb73a2f334a9260e344af62e1d','095678901'), -- passwd: cmenendez123
       ('dcauto@outlook.com','Delis','Cauto','default.webp','a000ee16202ce758c681432b5508d649','092345678'), -- passwd: dcauto123
       ('nfernandez@gmail.com','Nicolás','Fernandez','default.webp','10c9c2cd128bb4da723f0f5fb3399f42','091234568'), -- passwd: nfernande123
       ('gmadruga@gmail.com','Giselle','Madruga','default.webp','0e544a574a3b4389d8ba473be7be86f1','095245659'), -- passwd: gmadruga123
       ('mbritos@outlook.com','Maria','Britos','default.webp','b2d51d1818747410708c5b68e7e2f4af','096202358'), -- passwd: mbritos123
       ('kgancio@gmail.com','Karen','Gancio','default.webp','677b695fdb95b9f2b8cf2e2f7e3abfb8','094593909'), -- passwd: kgancio123
       ('mdacunha@outlook.com','Maria','Da Cunha','default.webp','cbad96a3206f6749f243617a952ae5bc','097441403'), -- passwd: mdacunha123
       ('ssoutullo@gmail.com','Sussi','Soutullo','default.webp','6c96c7480ea8a8aab3daa34b2e35b450','092102203'); -- passwd: ssoutullo123

-- Insertar empleados
INSERT INTO Empleado (email, esAdmin, nombreEmpresa)
VALUES ('wchocho@gmail.com',FALSE,'Cinematica'),
       ('mtorres@gmail.com',TRUE,'Cinematica'),
       ('ssoutullo@gmail.com', TRUE, 'Cinematica'),
       ('cmenendez@gmail.com',FALSE,'Cinematica');

-- Insertar clientes
INSERT INTO Cliente (email)
VALUES ('dcauto@outlook.com'),
       ('mdacunha@outlook.com'),
       ('kgancio@gmail.com');

-- Insertar clientes con CC incorporadas
INSERT INTO Cliente (email, numeroTarjeta, banco)
VALUES ('mbritos@outlook.com', '00283892333400001', 'BROU'),
       ('nfernandez@gmail.com', '2792336621880000', 'Santander'),
       ('gmadruga@gmail.com', '1224559000010000', 'Itaú');;

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
INSERT INTO Pelicula (idProducto, actores, sinopsis, duracion, nombrePelicula, pegi, trailer, director, poster, cabecera)
VALUES (100000001, 'Gerard Butler, Lena Headey, David Wenham', 'Rey Leonidas de Esparta y un ejército de 300 hombres luchan contra el Imperio persa.', '01:57:00', '300', 'R', 'https://www.youtube.com/watch?v=UrIbxk7idYA', 'Zack Snyder', '300_poster.webp', '300_cabecera.webp'), 
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
VALUES ('Acción'), ('Historia'), ('Ciencia Ficción'), ('Aventura'), ('Comedia'), ('Animación'), ('Deportes'), ('Drama'), ('Crimen'), ('Thriller'), ('Fantasía'), ('Romance');

-- Insertar dimensiones
INSERT INTO Dimensiones (dimension, precio)
VALUES ('2D', 440), ('3D', 490);

-- Relacionar productos con idiomas
INSERT INTO TieneIdiomas (idioma, idProducto) 
VALUES ('Inglés', 100000001), ('Español', 100000001),
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
INSERT INTO TieneCategorias (nombreCategoria, idProducto) 
VALUES ('Acción', 100000001), ('Historia', 100000001), -- 300
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
INSERT INTO TieneDimensiones (dimension, idProducto) 
VALUES ('2D', 100000001), ('3D', 100000001),
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

INSERT INTO Funciones (idFuncion, idProducto, nombreCine, numeroSala, fechaPelicula, horaPelicula, dimension, disp)
VALUES (900000001, 100000001, 'Costa Urbana', 2, '2024-11-22', '17:50:00', '2D', 140),
       (900000002, 100000002, 'Costa Urbana', 1, '2024-11-22', '19:10:00', '3D', 221),
       (900000003, 100000003, 'Costa Urbana', 2, '2024-11-22', '21:00:00', '2D', 140),
       (900000004, 100000004, 'Costa Urbana', 1, '2024-11-22', '21:50:00', '3D', 221),
       (900000005, 100000005, 'Costa Urbana', 1, '2024-11-23', '17:00:00', '2D', 221),
       (900000006, 100000006, 'Costa Urbana', 2, '2024-11-23', '18:20:00', '2D', 140),
       (900000007, 100000007, 'Costa Urbana', 2, '2024-11-23', '20:55:00', '2D', 140),
       (900000008, 100000008, 'Costa Urbana', 1, '2024-11-23', '22:10:00', '2D', 221),
       (900000009, 100000001, 'Costa Urbana', 2, '2024-11-24', '17:10:00', '3D', 140),
       (900000010, 100000002, 'Costa Urbana', 1, '2024-11-24', '18:30:00', '2D', 221),
       (900000011, 100000003, 'Costa Urbana', 2, '2024-11-24', '20:05:00', '3D', 140),
       (900000012, 100000004, 'Costa Urbana', 1, '2024-11-24', '21:10:00', '2D', 221),
       (900000013, 100000003, 'Tres Cruces', 2, '2024-11-22', '17:50:00', '2D', 140),
       (900000014, 100000004, 'Tres Cruces', 1, '2024-11-22', '19:10:00', '3D', 221),
       (900000015, 100000005, 'Tres Cruces', 2, '2024-11-22', '21:00:00', '2D', 140),
       (900000016, 100000006, 'Tres Cruces', 1, '2024-11-22', '21:50:00', '3D', 221),
       (900000017, 100000007, 'Tres Cruces', 1, '2024-11-23', '17:00:00', '2D', 221),
       (900000018, 100000008, 'Tres Cruces', 2, '2024-11-23', '18:20:00', '3D', 140),
       (900000019, 100000001, 'Tres Cruces', 2, '2024-11-23', '20:55:00', '2D', 140),
       (900000020, 100000002, 'Tres Cruces', 1, '2024-11-23', '22:10:00', '2D', 221),
       (900000021, 100000003, 'Tres Cruces', 2, '2024-11-24', '17:10:00', '3D', 140),
       (900000022, 100000004, 'Tres Cruces', 1, '2024-11-24', '18:30:00', '2D', 221),
       (900000023, 100000005, 'Tres Cruces', 2, '2024-11-24', '20:05:00', '2D', 140),
       (900000024, 100000006, 'Tres Cruces', 1, '2024-11-24', '21:10:00', '2D', 221),
       (900000025, 100000005, 'Portones Shopping', 2, '2024-11-22', '17:50:00', '2D', 221),
       (900000026, 100000006, 'Portones Shopping', 1, '2024-11-22', '19:10:00', '3D', 320),
       (900000027, 100000007, 'Portones Shopping', 2, '2024-11-22', '21:00:00', '2D', 221),
       (900000028, 100000008, 'Portones Shopping', 1, '2024-11-22', '21:50:00', '3D', 320),
       (900000029, 100000001, 'Portones Shopping', 1, '2024-11-23', '17:00:00', '2D', 320),
       (900000030, 100000002, 'Portones Shopping', 2, '2024-11-23', '18:20:00', '3D', 221),
       (900000031, 100000003, 'Portones Shopping', 2, '2024-11-23', '20:55:00', '3D', 221),
       (900000032, 100000004, 'Portones Shopping', 1, '2024-11-23', '22:10:00', '2D', 320),
       (900000033, 100000005, 'Portones Shopping', 2, '2024-11-24', '17:10:00', '2D', 221),
       (900000034, 100000006, 'Portones Shopping', 1, '2024-11-24', '18:30:00', '3D', 320),
       (900000035, 100000007, 'Portones Shopping', 2, '2024-11-24', '20:05:00', '2D', 221),
       (900000036, 100000008, 'Portones Shopping', 1, '2024-11-24', '21:10:00', '2D', 320),
       (900000037, 100000007, 'Punta Carretas Shopping', 2, '2024-11-22', '17:50:00', '2D', 221),
       (900000038, 100000008, 'Punta Carretas Shopping', 1, '2024-11-22', '19:10:00', '2D', 221),
       (900000039, 100000001, 'Punta Carretas Shopping', 2, '2024-11-22', '21:00:00', '3D', 221),
       (900000040, 100000002, 'Punta Carretas Shopping', 1, '2024-11-22', '21:50:00', '2D', 221),
       (900000041, 100000003, 'Punta Carretas Shopping', 1, '2024-11-23', '17:00:00', '2D', 221),
       (900000042, 100000004, 'Punta Carretas Shopping', 2, '2024-11-23', '18:20:00', '3D', 221),
       (900000043, 100000005, 'Punta Carretas Shopping', 2, '2024-11-23', '20:55:00', '2D', 221),
       (900000044, 100000006, 'Punta Carretas Shopping', 1, '2024-11-23', '22:10:00', '2D', 221),
       (900000045, 100000007, 'Punta Carretas Shopping', 2, '2024-11-24', '17:10:00', '2D', 221),
       (900000046, 100000008, 'Punta Carretas Shopping', 1, '2024-11-24', '18:30:00', '3D', 221),
       (900000047, 100000001, 'Punta Carretas Shopping', 2, '2024-11-24', '20:05:00', '2D', 221),
       (900000048, 100000002, 'Punta Carretas Shopping', 1, '2024-11-24', '21:10:00', '3D', 221),
       (900000049, 100000001, 'Cine Chaplin', 2, '2024-11-22', '17:50:00', '2D', 48),
       (900000050, 100000002, 'Cine Chaplin', 1, '2024-11-22', '19:10:00', '2D', 48),
       (900000051, 100000003, 'Cine Chaplin', 2, '2024-11-22', '21:00:00', '2D', 48),
       (900000052, 100000004, 'Cine Chaplin', 1, '2024-11-22', '21:50:00', '2D', 48),
       (900000053, 100000005, 'Cine Chaplin', 1, '2024-11-23', '17:00:00', '2D', 48),
       (900000054, 100000006, 'Cine Chaplin', 2, '2024-11-23', '18:20:00', '2D', 48),
       (900000055, 100000007, 'Cine Chaplin', 2, '2024-11-23', '20:55:00', '2D', 48),
       (900000056, 100000008, 'Cine Chaplin', 1, '2024-11-23', '22:10:00', '2D', 48),
       (900000057, 100000001, 'Cine Chaplin', 2, '2024-11-24', '17:10:00', '2D', 48),
       (900000058, 100000002, 'Cine Chaplin', 1, '2024-11-24', '18:30:00', '2D', 48),
       (900000059, 100000003, 'Cine Chaplin', 2, '2024-11-24', '20:05:00', '2D', 48),
       (900000060, 100000004, 'Cine Chaplin', 1, '2024-11-24', '21:10:00', '2D', 48),
       (900000061, 100000005, 'Cine Miramar', 2, '2024-11-22', '17:50:00', '2D', 48),
       (900000062, 100000006, 'Cine Miramar', 1, '2024-11-22', '19:10:00', '2D', 48),
       (900000063, 100000007, 'Cine Miramar', 2, '2024-11-22', '21:00:00', '2D', 48),
       (900000064, 100000008, 'Cine Miramar', 1, '2024-11-22', '21:50:00', '2D', 48),
       (900000065, 100000001, 'Cine Miramar', 1, '2024-11-23', '17:00:00', '2D', 48),
       (900000066, 100000002, 'Cine Miramar', 2, '2024-11-23', '18:20:00', '2D', 48),
       (900000067, 100000003, 'Cine Miramar', 2, '2024-11-23', '20:55:00', '2D', 48),
       (900000068, 100000004, 'Cine Miramar', 1, '2024-11-23', '22:10:00', '2D', 48),
       (900000069, 100000005, 'Cine Miramar', 2, '2024-11-24', '17:10:00', '2D', 48),
       (900000070, 100000006, 'Cine Miramar', 1, '2024-11-24', '18:30:00', '2D', 48),
       (900000071, 100000007, 'Cine Miramar', 2, '2024-11-24', '20:05:00', '2D', 48),
       (900000072, 100000008, 'Cine Miramar', 1, '2024-11-24', '21:10:00', '2D', 48);

INSERT INTO Mail (asunto, cabecera, cuerpo)
VALUES ('Registro', 'Bienvenido a Cinemática', 
"Hola @nombre,

Tu registro se ha realizado con éxito.

Nos complace darte la bienvenida a nuestra comunidad. A partir de ahora, tendrás acceso a tu cuenta de Cinemática, donde podrás comprar entradas desde donde sea que estes.

Si tienes alguna pregunta o necesitas ayuda con tu cuenta, no dudes en contactarnos.

¡Esperamos que disfrutes de todo lo que Cinemática tiene para ofrecer!

Saludos, 
El equipo de Cinemática,
Todas las estrellas en un solo lugar."),
       ('Token', 'Restablecimiento de contraseña solicitado', 
"Hola @nombre,

Recibimos una solicitud para restablecer tu contraseña. Si no realizaste esta solicitud, puedes ignorar este mensaje.

Para cambiar tu contraseña, ingresa el siguiente token de verificación: @token.

Este token expirará en 60 minutos.
Si tienes alguna pregunta o necesitas ayuda adicional, no dudes en contactarnos.

Saludos, 
El equipo de Cinemática,
Todas las estrellas en un solo lugar."),
       ('Compra', 'Confirmación de compra y envio de factura',
"Estimado/a @cliente,

Estamos complacidos de anunciarle que se ha realizado su compra en Cinemática.

Adjunto encontrarás la factura correspondiente a tu pedido realizado el @fechaCompra, con el número de identificador @idCompra.
Este documento incluye todos los detalles de tu transacción, así como la información de facturación y los productos adquiridos.

Si tienes alguna pregunta sobre tu compra o necesitas asistencia, no dudes en contactarnos.

Atentamente,
El equipo de Cinemática,
Todas las estrellas en un solo lugar."),
       ('CV Leido', 'Su curriculum es tenido en cuenta', 
"Estimado/a @nombre:

Le agradecemos por habernos enviado su currículum. Queremos informarle que ya hemos revisado su aplicación y valoramos su interés en unirse a nuestro equipo.

Nos pondremos en contacto con usted en brevedad para informarle sobre los siguientes pasos en el proceso de selección.

Agradecemos su paciencia y le deseamos mucho éxito.

Atentamente,
El equipo de Cinemática,
Todas las estrellas en un solo lugar."),
       ('CV Descartado', 'Agradecimiento por su postulación',
"Estimado/a @nombre:

Gracias por haberse tomado el tiempo de enviar su currículum vitae y postularse para formar parte de nuestro equipo.

Después de una cuidadosa revisión, lamentamos informarle que hemos decidido continuar con otros candidatos cuyas experiencias se alinean mejor con los requisitos de esta empresa.

Apreciamos su interés y le deseamos mucho éxito en sus futuros proyectos profesionales. Le invitamos a seguir atento/a a nuevas oportunidades de Cinemática, donde esperamos tener el placer de recibir nuevamente su postulación.

Atentamente,
El equipo de Cinemática,
Todas las estrellas en un solo lugar.");