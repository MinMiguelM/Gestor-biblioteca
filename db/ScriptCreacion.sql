drop database if exists biblioteca;
create database biblioteca;
use biblioteca;

create table usuario(
	idusuario int primary key auto_increment,
	nombre_usuario varchar(100) unique,
	email varchar(100) unique,
	password varchar(50),
	rol varchar(10) default 'user'
);

create table equipo(
	idequipo int primary key auto_increment,
	prestado bit default 0,
	nombre varchar(50),
	fabricante varchar(50),
	num_serie varchar(50),
	total int,
	disponibles int,
	imagen varchar(255)
);

create table sala(
	idsala int primary key auto_increment,
	nombre varchar(50),
	disponible bit default 1
);

create table evento(
	idevento int primary key auto_increment,
	idsala int null,
	fecha_inicio datetime,
	fecha_fin datetime,
	nombre varchar(50),
	lugar varchar(100),
	foreign key (idsala) references sala(idsala) on delete cascade
);

create table libro(
	idlibro int primary key auto_increment,
	titulo varchar(50),
	autor varchar(50),
	edicion varchar(50),
	editorial varchar(50),
	paginas int,
	ISBN varchar(50),
	prestado bit default 0,
	total int,
	disponibles int,
	imagen varchar(255)
);

create table solicitud(
	idsolicitud int primary key auto_increment,
	estado varchar(50) default 'espera',
	estado_objeto varchar(50) default 'excelente',
	fecha_vencimiento datetime,
	reporte int null,
	fecha_inicial datetime,
	idusuario int,
	idsala int null,
	idlibro int null,
	idequipo int null,
	foreign key (idusuario) references usuario(idusuario) on delete cascade,
	foreign key (idsala) references sala(idsala) on delete cascade,
	foreign key (idlibro) references libro(idlibro) on delete cascade,
	foreign key (idequipo) references equipo(idequipo) on delete cascade
);

create table reporte(
	idreporte int primary key auto_increment,
	estado varchar(50),
	comentarios varchar(255),
	idsolicitud int,
	foreign key (idsolicitud) references solicitud(idsolicitud) on delete cascade
);

create table usuarioXevento(
	id int primary key auto_increment,
	idusuario int,
	idevento int,
	foreign key (idusuario) references usuario(idusuario) on delete cascade,
	foreign key (idevento) references evento(idevento) on delete cascade
);

-- Insert admin user

insert into usuario (nombre_usuario,rol,email,password) values ('admin','admin','admin@mail.com','admin');