-- database/schema.sql
-- Student Course Hub — Full Database Schema
-- CTEC2712N — Musanna Khandakar
-- Run this FIRST before seed.sql

DROP DATABASE IF EXISTS student_course_hub;
CREATE DATABASE student_course_hub
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;
USE student_course_hub;

-- ── Drop tables in reverse dependency order ──────────────────────────────
DROP TABLE IF EXISTS InterestedStudents;
DROP TABLE IF EXISTS ProgrammeModules;
DROP TABLE IF EXISTS Programmes;
DROP TABLE IF EXISTS Modules;
DROP TABLE IF EXISTS Staff;
DROP TABLE IF EXISTS Levels;
DROP TABLE IF EXISTS Admins;

-- ── Levels ───────────────────────────────────────────────────────────────
CREATE TABLE Levels (
    LevelID   INTEGER      PRIMARY KEY,
    LevelName VARCHAR(100) NOT NULL
);

-- ── Admins ───────────────────────────────────────────────────────────────
CREATE TABLE Admins (
    AdminID      INT          AUTO_INCREMENT PRIMARY KEY,
    Username     VARCHAR(100) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role         ENUM('superadmin','editor') DEFAULT 'editor',
    CreatedAt    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ── Staff ────────────────────────────────────────────────────────────────
CREATE TABLE Staff (
    StaffID  INTEGER      PRIMARY KEY,
    Name     VARCHAR(255) NOT NULL,
    Email    VARCHAR(255),
    Bio      TEXT,
    Photo    VARCHAR(500),
    PhotoAlt VARCHAR(255)
);

-- ── Modules ──────────────────────────────────────────────────────────────
CREATE TABLE Modules (
    ModuleID       INTEGER      PRIMARY KEY,
    ModuleName     VARCHAR(255) NOT NULL,
    ModuleLeaderID INTEGER,
    Description    TEXT,
    Image          VARCHAR(255) DEFAULT NULL,
    ImageAlt       VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (ModuleLeaderID) REFERENCES Staff(StaffID)
);

-- ── Programmes ───────────────────────────────────────────────────────────
CREATE TABLE Programmes (
    ProgrammeID       INTEGER      PRIMARY KEY AUTO_INCREMENT,
    ProgrammeName     VARCHAR(255) NOT NULL,
    LevelID           INTEGER,
    ProgrammeLeaderID INTEGER,
    Description       TEXT,
    Image             VARCHAR(255) DEFAULT NULL,
    ImageAlt          VARCHAR(255) DEFAULT NULL,
    IsPublished       TINYINT(1)   NOT NULL DEFAULT 0,
    CreatedAt         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (LevelID)           REFERENCES Levels(LevelID),
    FOREIGN KEY (ProgrammeLeaderID) REFERENCES Staff(StaffID)
);

-- ── ProgrammeModules ─────────────────────────────────────────────────────
CREATE TABLE ProgrammeModules (
    ProgrammeModuleID INTEGER PRIMARY KEY AUTO_INCREMENT,
    ProgrammeID       INTEGER NOT NULL,
    ModuleID          INTEGER NOT NULL,
    Year              INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (ProgrammeID) REFERENCES Programmes(ProgrammeID)
        ON DELETE CASCADE,
    FOREIGN KEY (ModuleID) REFERENCES Modules(ModuleID)
        ON DELETE CASCADE
);

-- ── InterestedStudents ───────────────────────────────────────────────────
CREATE TABLE InterestedStudents (
    InterestID   INT          AUTO_INCREMENT PRIMARY KEY,
    ProgrammeID  INT          NOT NULL,
    StudentName  VARCHAR(100) NOT NULL,
    Email        VARCHAR(255) NOT NULL,
    IsActive     TINYINT(1)   NOT NULL DEFAULT 1,
    RegisteredAt TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    WithdrawnAt  TIMESTAMP    NULL DEFAULT NULL,
    UNIQUE KEY uq_programme_email (ProgrammeID, Email),
    FOREIGN KEY (ProgrammeID) REFERENCES Programmes(ProgrammeID)
        ON DELETE CASCADE
);

-- ── Indexes ──────────────────────────────────────────────────────────────
CREATE INDEX idx_prog_level     ON Programmes(LevelID);
CREATE INDEX idx_prog_published ON Programmes(IsPublished);
CREATE INDEX idx_pm_programme   ON ProgrammeModules(ProgrammeID);
CREATE INDEX idx_pm_year        ON ProgrammeModules(ProgrammeID, Year);
CREATE INDEX idx_is_programme   ON InterestedStudents(ProgrammeID);
CREATE INDEX idx_is_active      ON InterestedStudents(IsActive);
