-- schema.sql — Full database schema for Student Course Hub
-- CTEC2712N — Musanna Khandakar

DROP DATABASE IF EXISTS student_course_hub;
CREATE DATABASE student_course_hub;
USE student_course_hub;

DROP TABLE IF EXISTS InterestedStudents;
DROP TABLE IF EXISTS ProgrammeModules;
DROP TABLE IF EXISTS Programmes;
DROP TABLE IF EXISTS Modules;
DROP TABLE IF EXISTS Staff;
DROP TABLE IF EXISTS Levels;
DROP TABLE IF EXISTS Admins;

CREATE TABLE Levels (
    LevelID INTEGER PRIMARY KEY,
    LevelName TEXT NOT NULL
);

CREATE TABLE Admins (
    AdminID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role ENUM('superadmin','editor') DEFAULT 'editor',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Staff (
    StaffID INTEGER PRIMARY KEY,
    Name TEXT NOT NULL,
    Email VARCHAR(255),
    Bio TEXT,
    Photo VARCHAR(500),
    PhotoAlt VARCHAR(255)
);

CREATE TABLE Modules (
    ModuleID INTEGER PRIMARY KEY,
    ModuleName TEXT NOT NULL,
    ModuleLeaderID INTEGER,
    Description TEXT,
    Image TEXT,
    ImageAlt VARCHAR(255),
    FOREIGN KEY (ModuleLeaderID) REFERENCES Staff(StaffID)
);

CREATE TABLE Programmes (
    ProgrammeID INTEGER PRIMARY KEY AUTO_INCREMENT,
    ProgrammeName TEXT NOT NULL,
    LevelID INTEGER,
    ProgrammeLeaderID INTEGER,
    Description TEXT,
    Image TEXT,
    ImageAlt VARCHAR(255),
    IsPublished TINYINT(1) DEFAULT 0,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (LevelID) REFERENCES Levels(LevelID),
    FOREIGN KEY (ProgrammeLeaderID) REFERENCES Staff(StaffID)
);

CREATE TABLE ProgrammeModules (
    ProgrammeModuleID INTEGER PRIMARY KEY AUTO_INCREMENT,
    ProgrammeID INTEGER,
    ModuleID INTEGER,
    Year INTEGER,
    FOREIGN KEY (ProgrammeID) REFERENCES Programmes(ProgrammeID),
    FOREIGN KEY (ModuleID) REFERENCES Modules(ModuleID)
);

CREATE TABLE InterestedStudents (
    InterestID INT AUTO_INCREMENT PRIMARY KEY,
    ProgrammeID INT NOT NULL,
    StudentName VARCHAR(100) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    IsActive TINYINT(1) DEFAULT 1,
    WithdrawnAt TIMESTAMP NULL,
    RegisteredAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ProgrammeID) REFERENCES Programmes(ProgrammeID) ON DELETE CASCADE,
    UNIQUE KEY uq_student_programme (Email, ProgrammeID)
);

CREATE INDEX idx_prog_level ON Programmes(LevelID);
CREATE INDEX idx_prog_published ON Programmes(IsPublished);
CREATE INDEX idx_pm_programme ON ProgrammeModules(ProgrammeID);
CREATE INDEX idx_pm_year ON ProgrammeModules(ProgrammeID, Year);
CREATE INDEX idx_is_programme ON InterestedStudents(ProgrammeID);
CREATE INDEX idx_is_active ON InterestedStudents(IsActive);