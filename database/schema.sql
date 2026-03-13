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
-- Stores: Undergraduate, Postgraduate
CREATE TABLE Levels (
    LevelID   INTEGER      PRIMARY KEY,
    LevelName VARCHAR(100) NOT NULL
);

-- ── Staff ────────────────────────────────────────────────────────────────
-- All university staff — used as programme leaders and module leaders
CREATE TABLE Staff (
    StaffID INTEGER      PRIMARY KEY,
    Name    VARCHAR(255) NOT NULL
);

-- ── Modules ──────────────────────────────────────────────────────────────
-- Individual teaching modules. Each has one module leader from Staff.
CREATE TABLE Modules (
    ModuleID       INTEGER       PRIMARY KEY,
    ModuleName     VARCHAR(255)  NOT NULL,
    ModuleLeaderID INTEGER,
    Description    TEXT,
    Image          VARCHAR(255)  DEFAULT NULL,
    ImageAlt       VARCHAR(255)  DEFAULT NULL,
    FOREIGN KEY (ModuleLeaderID) REFERENCES Staff(StaffID)
);

-- ── Programmes ───────────────────────────────────────────────────────────
-- Degree programmes (BSc, MSc). Each has a level and a programme leader.
-- IsPublished controls visibility on the student-facing site.
CREATE TABLE Programmes (
    ProgrammeID       INTEGER      PRIMARY KEY AUTO_INCREMENT,
    ProgrammeName     VARCHAR(255) NOT NULL,
    LevelID           INTEGER,
    ProgrammeLeaderID INTEGER,
    Description       TEXT,
    Image             VARCHAR(255) DEFAULT NULL,
    ImageAlt          VARCHAR(255) DEFAULT NULL,
    IsPublished       TINYINT(1)   NOT NULL DEFAULT 0,
    FOREIGN KEY (LevelID)           REFERENCES Levels(LevelID),
    FOREIGN KEY (ProgrammeLeaderID) REFERENCES Staff(StaffID)
);

-- ── ProgrammeModules ─────────────────────────────────────────────────────
-- Junction table: links Programmes to Modules.
-- Year tells us which year of study the module belongs to.
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
-- Stores prospective student interest registrations.
-- IsActive = 1 means active interest. IsActive = 0 means withdrawn.
-- UNIQUE (ProgrammeID, Email) prevents duplicate registrations.
CREATE TABLE InterestedStudents (
    InterestID    INT          AUTO_INCREMENT PRIMARY KEY,
    ProgrammeID   INT          NOT NULL,
    StudentName   VARCHAR(100) NOT NULL,
    Email         VARCHAR(255) NOT NULL,
    IsActive      TINYINT(1)   NOT NULL DEFAULT 1,
    RegisteredAt  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    WithdrawnAt   TIMESTAMP    NULL DEFAULT NULL,
    UNIQUE KEY uq_programme_email (ProgrammeID, Email),
    FOREIGN KEY (ProgrammeID) REFERENCES Programmes(ProgrammeID)
        ON DELETE CASCADE
);

-- ── Admins ───────────────────────────────────────────────────────────────
-- Admin users who can log into the admin panel.
-- PasswordHash stores bcrypt hash — NEVER store plain text passwords.
-- Role: 'admin' = standard, 'superadmin' = full access
CREATE TABLE Admins (
    AdminID      INT          AUTO_INCREMENT PRIMARY KEY,
    Username     VARCHAR(100) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role         VARCHAR(50)  NOT NULL DEFAULT 'admin',
    CreatedAt    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);
