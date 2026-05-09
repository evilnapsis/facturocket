/*
* Base de datos Facturocket
* Actualizado 2026-05-08
* Author @evilnapsis
*/
create database facturocket;
use facturocket; 

/**
* Tabla de usuarios para el acceso al sistema
*/
create table user (
	id int not null auto_increment primary key,
	username varchar(50),
	name varchar(50),
	lastname varchar(50),
	email varchar(255),
	password varchar(60),
	is_active boolean not null default 1,
	type int not null default 1, /* 1. admin, 2. user */
	created_at datetime
);

insert into user (username,email,password,type,created_at) value ("admin","admin",sha1(md5("admin")),1,NOW());

/**
* Ajustes para datos del emisor y configuración del sistema
*/
create table setting (
	id int not null auto_increment primary key,
	name varchar(255),
	short varchar(255),
	val text,
	created_at datetime
);

insert into setting (name, short, val, created_at) values 
('Certificado CSD', 'csd_cert', '', NOW()),
('Llave CSD', 'csd_key', '', NOW()),
('Contraseña CSD', 'csd_pass', '', NOW()),
('Tipo de Razón Social', 'business_type', '', NOW()),
('Nombre', 'first_name', '', NOW()),
('Apellidos', 'last_name', '', NOW()),
('Nombre Comercial', 'commercial_name', '', NOW()),
('Logo', 'logo', '', NOW()),
('Serial PAC/API Key', 'pac_serial', '', NOW()),
('RFC', 'rfc', '', NOW()),
('País', 'country', 'México', NOW()),
('Estado', 'state', '', NOW()),
('Municipio', 'municipality', '', NOW()),
('Dirección Fiscal', 'address', '', NOW()),
('Régimen Fiscal', 'tax_regime_id', '', NOW()),
('Código Postal', 'zip_code', '', NOW()),
('Correo Electrónico', 'email', '', NOW()),
('Página Web', 'website', '', NOW()),
('Teléfono', 'phone', '', NOW());

/**
* Catálogos del SAT (Autogestionables)
*/
/* Régimen Fiscal */
create table tax_regime (
	id int not null auto_increment primary key,
	code varchar(10),
	name varchar(255)
);

insert into tax_regime (code,name) values ('601', 'General de Ley Personas Morales'), ('605', 'Sueldos y Salarios e Ingresos Asimilados a Salarios'), ('612', 'Personas Físicas con Actividades Empresariales y Profesionales'), ('626', 'Régimen Simplificado de Confianza');

/* Uso de CFDI */
create table cfdi_use (
	id int not null auto_increment primary key,
	code varchar(10),
	name varchar(255)
);

insert into cfdi_use (code,name) values ('G01', 'Adquisición de mercancías'), ('G03', 'Gastos en general'), ('S01', 'Sin efectos fiscales'), ('CP01', 'Pagos');

/* Método de Pago */
create table payment_method (
	id int not null auto_increment primary key,
	code varchar(10),
	name varchar(255)
);

insert into payment_method (code,name) values ('PUE', 'Pago en una sola exhibición'), ('PPD', 'Pago en parcialidades o diferido');

/* Forma de Pago */
create table payment_form (
	id int not null auto_increment primary key,
	code varchar(10),
	name varchar(255)
);

insert into payment_form (code,name) values ('01', 'Efectivo'), ('03', 'Transferencia electrónica de fondos'), ('99', 'Por definir');

/* Clave de Unidad */
create table unit (
	id int not null auto_increment primary key,
	code varchar(10),
	name varchar(255)
);

insert into unit (code,name) values ('H87', 'Pieza'), ('E48', 'Unidad de servicio'), ('KGM', 'Kilogramo'), ('LTR', 'Litro');

/* Clave de Producto o Servicio */
create table product_type (
	id int not null auto_increment primary key,
	code varchar(20),
	name varchar(255)
);

insert into product_type (code,name) values ('01010101', 'No existe en el catálogo'), ('84111506', 'Servicios de facturación');

/* Objeto de Impuesto */
create table tax_object (
	id int not null auto_increment primary key,
	code varchar(10),
	name varchar(255)
);

insert into tax_object (code,name) values ('01', 'No objeto de impuesto'), ('02', 'Sí objeto de impuesto'), ('03', 'Sí objeto de impuesto y no obligado al desglose');

/* Impuestos (IVA, ISR, IEPS) */
create table tax (
	id int not null auto_increment primary key,
	code varchar(10),
	name varchar(255)
);

insert into tax (code,name) values ('002', 'IVA'), ('001', 'ISR'), ('003', 'IEPS');

/**
* Tablas de Negocio
*/
/* Clientes (Receptores) */
create table client (
	id int not null auto_increment primary key,
	rfc varchar(20),
	name varchar(255),
	email varchar(255),
	address varchar(255),
	zip_code varchar(10),
	tax_regime_id int,
	cfdi_use_id int,
	created_at datetime,
	foreign key (tax_regime_id) references tax_regime(id),
	foreign key (cfdi_use_id) references cfdi_use(id)
);

insert into client (rfc, name, email, address, zip_code, tax_regime_id, cfdi_use_id, created_at) values ('XAXX010101000', 'PUBLICO EN GENERAL', 'publico@facturocket.com', 'DOMICILIO CONOCIDO', '00000', 1, 1, NOW());

/* Categorías de productos */
create table category (
	id int not null auto_increment primary key,
	name varchar(255),
	created_at datetime
);

/* Productos y Servicios */
create table product (
	id int not null auto_increment primary key,
	name varchar(255),
	code varchar(100),
	description text,
	price float,
	category_id int,
	product_type_id int, /* clave sat   */
	unit_id int, /* clave unidad de medida*/
	tax_id int, /* impuesto aplicable */
	created_at datetime,
	foreign key (category_id) references category(id),
	foreign key (product_type_id) references product_type(id),
	foreign key (unit_id) references unit(id),
	foreign key (tax_id) references tax(id)
);

-- Nuevas tablas para CFDI 4.0 y Pagos

CREATE TABLE IF NOT EXISTS relation_type (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    code VARCHAR(5) NOT NULL, 
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS invoice_relation (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    invoice_id INT NOT NULL, 
    relation_type_id INT NOT NULL, 
    related_uuid VARCHAR(50) NOT NULL, 
    FOREIGN KEY (invoice_id) REFERENCES invoice(id), 
    FOREIGN KEY (relation_type_id) REFERENCES relation_type(id)
);

CREATE TABLE IF NOT EXISTS payment (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    invoice_id INT NOT NULL, 
    payment_form_id INT NOT NULL, 
    amount DECIMAL(10,2) NOT NULL, 
    saldo_anterior DECIMAL(10,2) NOT NULL, 
    saldo_insoluto DECIMAL(10,2) NOT NULL, 
    num_parcialidad INT NOT NULL, 
    date_at DATETIME NOT NULL, 
    uuid VARCHAR(50), 
    xml_path VARCHAR(255), 
    is_stamped TINYINT(1) DEFAULT 0, 
    created_at DATETIME NOT NULL, 
    FOREIGN KEY (invoice_id) REFERENCES invoice(id), 
    FOREIGN KEY (payment_form_id) REFERENCES payment_form(id)
);

CREATE TABLE IF NOT EXISTS folio_sequence (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    type_code VARCHAR(10) NOT NULL, 
    serie VARCHAR(5) NOT NULL, 
    next_folio INT NOT NULL DEFAULT 1
);

INSERT INTO relation_type (code, name) VALUES 
('01', 'Nota de crédito de los documentos relacionados'), 
('02', 'Nota de débito de los documentos relacionados'), 
('03', 'Devolución de mercancía sobre facturas o traslados previos'), 
('04', 'Sustitución de los CFDI previos'), 
('05', 'Traslados de mercancías facturados previamente'), 
('06', 'Factura generada por los traslados previos'), 
('07', 'CFDI por aplicación de anticipo');

INSERT INTO folio_sequence (type_code, serie, next_folio) VALUES 
('I', 'A', 1), 
('E', 'NC', 1), 
('P', 'P', 1), 
('NV', 'NV', 1);

insert into category (name, created_at) values ('General', NOW());
insert into product (name, code, description, price, category_id, product_type_id, unit_id, tax_id, created_at) values ('PRODUCTO GENERICO', 'GEN-01', 'Descripción del producto genérico', 100.00, 1, 1, 1, 1, NOW());

/* Facturas (Cabecera del CFDI) */
create table invoice (
	id int not null auto_increment primary key,
	serie varchar(10),
	folio varchar(20),
	date datetime,
	client_id int,
	payment_form_id int,
	payment_method_id int,
	currency varchar(10) default 'MXN',
	exchange_rate float default 1,
	subtotal float,
	discount float default 0,
	total float,
	type varchar(10) default 'I', /* I: Ingreso, E: Egreso, P: Pago, N: Nomina, T: Traslado */
	status int default 1, /* 1: Draft, 2: Signed, 3: Cancelled */
	uuid varchar(100),
	xml_path varchar(255),
	pdf_path varchar(255),
	user_id int,
	created_at datetime,
	foreign key (client_id) references client(id),
	foreign key (payment_form_id) references payment_form(id),
	foreign key (payment_method_id) references payment_method(id),
	foreign key (user_id) references user(id)
);

/* Conceptos de la factura (Detalle del CFDI) */
create table invoice_item (
	id int not null auto_increment primary key,
	invoice_id int,
	product_id int,
	description text,
	quantity float,
	price float,
	discount float default 0,
	total float,
	tax_object_id int,
	created_at datetime,
	foreign key (invoice_id) references invoice(id),
	foreign key (product_id) references product(id),
	foreign key (tax_object_id) references tax_object(id)
);

/* Impuestos por concepto */
create table invoice_tax (
	id int not null auto_increment primary key,
	invoice_item_id int,
	tax_id int,
	base float,
	rate float,
	amount float,
	type int default 1, /* 1: Traslado, 2: Retencion */
	created_at datetime,
	foreign key (invoice_item_id) references invoice_item(id),
	foreign key (tax_id) references tax(id)
);