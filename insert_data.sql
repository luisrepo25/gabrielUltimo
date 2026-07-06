INSERT IGNORE INTO marca (id, nombre) VALUES 
(1, 'Bosch'), (2, 'Stanley'), (3, 'Tramontina'), (4, 'Truper'), (5, '3M'), (6, 'Sika'), (7, 'Dewalt');
INSERT IGNORE INTO marca (id, nombre) values (8,'Monopol');

INSERT IGNORE INTO color (id, nombre) VALUES 
(1, 'Rojo'), (2, 'Azul'), (3, 'Amarillo'), (4, 'Gris Metálico'), (5, 'Naranja');

INSERT IGNORE INTO medida (id, longitud, ancho, alto) VALUES (1, '5m', '19mm', 'n/a'),
(10, '1 pulgada', 'n/a', 'n/a'), (11, '3 pulgadas', 'n/a', 'n/a'), (12, '6 metros', '4 pulg', 'n/a'), (13, '50 metros', 'n/a', 'n/a');

INSERT IGNORE INTO volumen (id, peso, volumen_m3) VALUES (1, '1kg', 'n/a'),
(10, '1 Litro', '0.001'), (11, '18 Litros', '0.018'), (12, '50 kg', '0.035'), (13, '1 kg', '0.001');

INSERT IGNORE INTO metodoPago (id, nombre) VALUES 
(1, 'Efectivo'), (2, 'Tarjeta de Débito');

INSERT IGNORE INTO categoria (idcategoria, nombre, descripcion, id_categoria_padre) VALUES 
(1, 'Construcción', 'Materiales pesados y obra gruesa', NULL),
(2, 'Carpintería', 'Herramientas y accesorios para madera', NULL),
(3, 'Pintura', 'Recubrimientos y acabados', NULL),
(4, 'Plomería', 'Tuberías y conexiones de agua/gas', NULL),
(5, 'Electricidad', 'Cables, térmicos e iluminación', NULL),
(6, 'Herramientas Eléctricas', 'Maquinaria con motor para obra', NULL),
(101, 'Cementos y Mezclas', 'Bolsas de cemento, cola y yeso', 1),
(102, 'Fierro y Acero', 'Barras corrugadas y alambres', 1),
(103, 'Ladrillos y Tejas', 'Material cerámico para muros', 1),
(201, 'Herramientas de Corte', 'Serruchos, formones y cepillos', 2),
(202, 'Adhesivos y Colas', 'Pegamentos especiales para madera', 2),
(203, 'Herrajes', 'Bisagras, correderas y tiradores', 2),
(301, 'Látex e Interiores', 'Pinturas al agua para paredes', 3),
(302, 'Esmaltes y Barnices', 'Acabados para metal y madera', 3),
(303, 'Impermeabilizantes', 'Protección contra humedad', 3),
(401, 'Tubería PVC', 'Tubos para desagüe y agua potable', 4),
(402, 'Grifería', 'Grifos, duchas y llaves de paso', 4),
(403, 'Accesorios de Unión', 'Codos, tees y coplas', 4);

INSERT IGNORE INTO producto (idproducto, nombre, descripcion, precio, cantidad, fechaCaducidad, id_categoria, id_marca, id_color, id_medida, id_volumen) VALUES 
(101, 'Cemento IP-30 Viacha', 'Bolsa de 50kg para obra gruesa', 55.00, 500, '2026-10-15', 101, 6, 5, NULL, 12),
(102, 'Fierro Corrugado 12mm', 'Barra de acero estructural de 12m', 82.50, 200, NULL, 102, 7, NULL, 12, NULL),
(103, 'Sika-1 Impermeabilizante', 'Aditivo líquido para hormigón', 35.00, 40, '2027-02-20', 303, 6, NULL, NULL, 13),
(104, 'Alambre de Amarre', 'Alambre recocido para estribos', 15.00, 100, NULL, 102, 4, NULL, NULL, 13),
(201, 'Látex Americano Blanco', 'Pintura de alto cubrimiento interior', 320.00, 25, '2028-12-01', 301, 3, 1, NULL, 11),
(202, 'Esmalte Sintético Rojo', 'Pintura para metal y madera', 48.00, 50, '2027-06-30', 302, 5, 1, NULL, 10),
(203, 'Barniz Marino', 'Protección para maderas exteriores', 55.00, 30, '2027-05-15', 302, 2, NULL, NULL, 10),
(301, 'Tornillo Drywall 1"', 'Caja de 100 unidades punta aguja', 18.00, 1000, NULL, 203, 4, 4, 10, NULL),
(302, 'Clavo de Acero 3"', 'Clavo con cabeza para madera 1kg', 22.00, 800, NULL, 102, 4, 4, 11, 13),
(303, 'Bisagra de Cangrejo', 'Par de bisagras para muebles', 12.50, 150, NULL, 203, 3, 4, NULL, NULL),
(401, 'Tubo PVC Desagüe 4"', 'Tubo de 6 metros para drenaje', 95.00, 100, NULL, 401, 6, NULL, 12, NULL),
(402, 'Cinta Teflón 12mm', 'Sellador de roscas para agua', 5.00, 300, NULL, 403, 3, 1, 1, NULL),
(403, 'Grifo flexible Cocina', 'Grifería de acero inoxidable', 145.00, 15, NULL, 402, 3, 4, NULL, NULL),
(501, 'Cable AWG #12 Azul', 'Rollo de cable de cobre aislado', 380.00, 20, NULL, 5, 5, 2, 13, NULL),
(502, 'Cinta Aislante 3M', 'Cinta de PVC para empalmes', 12.00, 100, '2029-01-01', 5, 5, 2, 1, NULL),
(601, 'Taladro Percutor 600W', 'Taladro profesional uso pesado', 450.00, 12, NULL, 6, 1, NULL, NULL, NULL),
(602, 'Amoladora Angular 4.5"', 'Herramienta de corte 115mm', 390.00, 10, NULL, 6, 7, NULL, NULL, NULL);

INSERT IGNORE INTO proveedor (ci, nombre, descripcion, telefono, correo, direccion) VALUES 
(9001, 'Mainter S.A.', 'Distribuidor de maquinaria Bosch y Dewalt', 3354455, 'ventas@mainter.com', 'Parque Industrial PI-22'),
(9002, 'Importadora Campero', 'Especialistas en acero y fierro corrugado', 3367788, 'info@campero.com', 'Av. Cristobal de Mendoza #450'),
(9003, 'Pinturas Coral Bolivia', 'Fábrica de pinturas y recubrimientos', 3312233, 'pedidos@coral.bo', 'KM 10 Doble Vía a La Guardia'),
(9004, 'Plásticos Tigre', 'Tuberías y accesorios de PVC', 3348899, 'atencion@tigre.bo', 'Carretera al Norte KM 7'),
(9005, 'Ferretería El Tornillo', 'Distribuidor mayorista de pernos y herramientas', 3385544, 'ventas@eltornillo.com', 'Av. Grigotá esq. 3er Anillo');

INSERT IGNORE INTO usuario (ci, nombre, apellido, telefono, sexo, email, domicilio, tipoPersona) VALUES 
(1001, 'Denilson', 'Cruz', 70011223, 'M', 'denilson.c@ferre.bo', 'Av. Bush 2do Anillo', 'E'),
(1002, 'Antony', 'Luizaga', 75022334, 'M', 'antony.l@ferre.bo', 'Plan 3000 Calle 5', 'E'),
(1003, 'Alexander', 'Osinaga', 77033445, 'M', 'alexander.o@ferre.bo', 'Av. Banzer 4to Anillo', 'E'),
(1004, 'Miguel', 'Aguayo', 3345566, 'M', 'miguel.a@mail.com', 'Barrio Sirari', 'C'),
(1005, 'Gabriel', 'Aguilar', 71044556, 'M', 'gabriel.a@mail.com', 'Av. Santos Dumont', 'C'),
(1006, 'Richar', 'Lazarte', 72055667, 'M', 'richar.l@mail.com', 'Villa Primero de Mayo', 'C'),
(1007, 'Jherson', 'Dach', 77080901, 'M', 'jherson@mail.com', 'Equipetrol', 'C'),
(1008, 'Lucía', 'Vargas', 78066778, 'F', 'lucia.v@mail.com', 'Av. Piraí', 'C'),
(1009, 'Carlos', 'Mendoza', 79077889, 'M', 'carlos.m@constructora.bo', 'Urubó', 'C'),
(1010, 'Elena', 'Pinto', 3321100, 'F', 'elena.p@mail.com', 'Calle Sucre Central', 'C'),
(1011, 'Jorge', 'Suarez', 71122334, 'M', 'jorge.s@mail.com', 'Km 6 Doble Vía La Guardia', 'C');

INSERT IGNORE INTO empleado (ci, salario, estado) VALUES (1001, 4500.00, 'Activo'), (1002, 3800.00, 'Activo'), (1003, 3800.00, 'Activo'); 

INSERT IGNORE INTO cliente (ci) VALUES (1004), (1005), (1006), (1007), (1008), (1009), (1010), (1011); 

INSERT IGNORE INTO rol (id, nombre, descripcion) VALUES (1, 'Administrador', 'Acceso total al sistema y reportes'), (2, 'Vendedor', 'Realiza facturación y registro de clientes'), (3, 'Almacenero', 'Gestiona ingresos de mercadería y stock');

INSERT IGNORE INTO estadoRol (id_rol, ci_empleado, fechaInicio, fechaFin, estado) VALUES 
(1, 1001, '2025-01-15', '2026-12-31', 'Activo'),
(2, 1002, '2025-06-01', '2026-12-31', 'Activo'),
(3, 1003, '2026-02-10', '2026-12-31', 'Activo');

INSERT IGNORE INTO NotaCompra (nro, fecha, total, ci_proveedor, id_pago) VALUES 
(3001, '2026-03-25 00:00:00', 8400.00, 9001, 1),
(3002, '2026-03-26 00:00:00', 27500.00, 9002, 1),
(3005, '2026-03-29 00:00:00', 2480.00, 9005, 2);

INSERT IGNORE INTO detalleNotaCompra (nro_factura, id_producto, precio_unitario, cantidad) VALUES 
(3001, 601, 450.00, 10), (3001, 602, 390.00, 10),
(3002, 101, 55.00, 400), (3002, 102, 82.50, 60), (3002, 302, 22.00, 20),
(3005, 301, 18.00, 100), (3005, 303, 12.50, 40), (3005, 402, 5.00, 36);

INSERT IGNORE INTO NotaVenta (nro, fecha, total, ci_cliente, ci_empleado, id_pago) VALUES 
(8001, '2026-03-31 08:30:00', 2030.00, 1004, 1002, 1),
(8003, '2026-03-31 10:00:00', 555.00, 1006, 1002, 1),
(8006, '2026-03-31 12:00:00', 2555.00, 1009, 1001, 2);

INSERT IGNORE INTO detalleNotaVenta (nro_factura, id_producto, precio_unitario, cantidad, descuento) VALUES 
(8001, 101, 55.00, 30, 0), (8001, 102, 82.50, 4, 0), (8001, 103, 35.00, 2, 0), 
(8003, 602, 390.00, 1, 0), (8003, 202, 48.00, 3, 0), (8003, 203, 21.00, 1, 0),  
(8006, 501, 380.00, 6, 0), (8006, 502, 12.50, 10, 0), (8006, 102, 82.50, 2, 0);
