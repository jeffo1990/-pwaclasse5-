-- Active: 1748050529964@@127.0.0.1@3306@tarea5
CREATE DATABASE gestion_tareas;
USE gestion_tareas;

CREATE TABLE roles (
  rol_id INT AUTO_INCREMENT PRIMARY KEY,
  rol_nombre VARCHAR(30) NOT NULL
);

INSERT INTO roles (rol_nombre) VALUES ('Administrador'), ('Gerente de proyecto'), ('Miembro del equipo');

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  contraseña VARCHAR(100) NOT NULL,
  rol_id INT NOT NULL,
  FOREIGN KEY (rol_id) REFERENCES roles(rol_id)
);

CREATE TABLE tareas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(50) NOT NULL,
  descripcion VARCHAR(300),
  estado ENUM('Pendiente', 'En proceso', 'Completado') NOT NULL DEFAULT 'Pendiente',
  usuario_id INT NOT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

