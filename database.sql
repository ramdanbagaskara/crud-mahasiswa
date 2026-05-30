-- =============================================
-- Script SQL: Aplikasi CRUD Mahasiswa
-- Jalankan di phpMyAdmin atau MySQL CLI
-- =============================================

-- 1. Buat database
CREATE DATABASE IF NOT EXISTS db_mahasiswa
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- 2. Gunakan database
USE db_mahasiswa;

-- 3. Buat tabel mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    nim           VARCHAR(20)  NOT NULL UNIQUE,
    nama          VARCHAR(100) NOT NULL,
    jurusan       VARCHAR(100) NOT NULL,
    semester      TINYINT(2)   NOT NULL DEFAULT 1,
    jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Insert data sample (opsional)
INSERT INTO mahasiswa (nim, nama, jurusan, semester, jenis_kelamin) VALUES
('2021001001', 'Ahmad Fauzi',       'Teknik Informatika', 4, 'Laki-laki'),
('2021001002', 'Siti Rahayu',       'Sistem Informasi',   4, 'Perempuan'),
('2022001003', 'Budi Santoso',      'Teknik Informatika', 2, 'Laki-laki'),
('2022001004', 'Dewi Anggraini',    'Manajemen Informatika', 2, 'Perempuan'),
('2023001005', 'Rizky Pratama',     'Teknik Informatika', 1, 'Laki-laki');
