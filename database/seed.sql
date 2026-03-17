-- database/seed.sql
-- Student Course Hub — Seed Data
-- CTEC2712N — Musanna Khandakar
-- Run this AFTER schema.sql

USE student_course_hub;

-- ── Levels ───────────────────────────────────────────────────────────────
INSERT INTO Levels (LevelID, LevelName) VALUES
(1, 'Undergraduate'),
(2, 'Postgraduate');

-- ── Staff — 20 members ───────────────────────────────────────────────────
INSERT INTO Staff (StaffID, Name) VALUES
(1,  'Dr. Alice Johnson'),
(2,  'Dr. Brian Lee'),
(3,  'Dr. Carol White'),
(4,  'Dr. David Green'),
(5,  'Dr. Emma Scott'),
(6,  'Dr. Frank Moore'),
(7,  'Dr. Grace Adams'),
(8,  'Dr. Henry Clark'),
(9,  'Dr. Irene Hall'),
(10, 'Dr. James Wright'),
(11, 'Dr. Sophia Miller'),
(12, 'Dr. Benjamin Carter'),
(13, 'Dr. Chloe Thompson'),
(14, 'Dr. Daniel Robinson'),
(15, 'Dr. Emily Davis'),
(16, 'Dr. Nathan Hughes'),
(17, 'Dr. Olivia Martin'),
(18, 'Dr. Samuel Anderson'),
(19, 'Dr. Victoria Hall'),
(20, 'Dr. William Scott');

-- ── Modules — 31 modules ─────────────────────────────────────────────────
INSERT INTO Modules (ModuleID, ModuleName, ModuleLeaderID, Description) VALUES
(1,  'Introduction to Programming',            1,  'Covers the fundamentals of programming using Python and Java.'),
(2,  'Mathematics for Computer Science',       2,  'Teaches discrete mathematics, linear algebra, and probability theory.'),
(3,  'Computer Systems and Architecture',      3,  'Explores CPU design, memory management, and assembly language.'),
(4,  'Databases',                              4,  'Covers SQL, relational database design, and NoSQL systems.'),
(5,  'Software Engineering',                   5,  'Focuses on agile development, design patterns, and project management.'),
(6,  'Algorithms and Data Structures',         6,  'Examines sorting, searching, graphs, and complexity analysis.'),
(7,  'Cyber Security Fundamentals',            7,  'Provides an introduction to network security, cryptography, and vulnerabilities.'),
(8,  'Artificial Intelligence',                8,  'Introduces AI concepts such as neural networks, expert systems, and robotics.'),
(9,  'Machine Learning',                       9,  'Explores supervised and unsupervised learning, including decision trees and clustering.'),
(10, 'Ethical Hacking',                        10, 'Covers penetration testing, security assessments, and cybersecurity laws.'),
(11, 'Computer Networks',                      1,  'Teaches TCP/IP, network layers, and wireless communication.'),
(12, 'Software Testing and Quality Assurance', 2,  'Focuses on automated testing, debugging, and code reliability.'),
(13, 'Embedded Systems',                       3,  'Examines microcontrollers, real-time OS, and IoT applications.'),
(14, 'Human-Computer Interaction',             4,  'Studies UI/UX design, usability testing, and accessibility.'),
(15, 'Blockchain Technologies',                5,  'Covers distributed ledgers, consensus mechanisms, and smart contracts.'),
(16, 'Cloud Computing',                        6,  'Introduces cloud services, virtualization, and distributed systems.'),
(17, 'Digital Forensics',                      7,  'Teaches forensic investigation techniques for cybercrime.'),
(18, 'Final Year Project',                     8,  'A major independent project where students develop a software solution.'),
(19, 'Advanced Machine Learning',              11, 'Covers deep learning, reinforcement learning, and cutting-edge AI techniques.'),
(20, 'Cyber Threat Intelligence',              12, 'Focuses on cybersecurity risk analysis, malware detection, and threat mitigation.'),
(21, 'Big Data Analytics',                     13, 'Explores data mining, distributed computing, and AI-driven insights.'),
(22, 'Cloud and Edge Computing',               14, 'Examines scalable cloud platforms, serverless computing, and edge networks.'),
(23, 'Blockchain and Cryptography',            15, 'Covers decentralised applications, consensus algorithms, and security measures.'),
(24, 'AI Ethics and Society',                  16, 'Analyses ethical dilemmas in AI, fairness, bias, and regulatory considerations.'),
(25, 'Quantum Computing',                      17, 'Introduces quantum algorithms, qubits, and cryptographic applications.'),
(26, 'Cybersecurity Law and Policy',           18, 'Explores digital privacy, GDPR, and international cyber law.'),
(27, 'Neural Networks and Deep Learning',      19, 'Delves into convolutional networks, GANs, and AI advancements.'),
(28, 'Human-AI Interaction',                   20, 'Studies AI usability, NLP systems, and social robotics.'),
(29, 'Autonomous Systems',                     11, 'Focuses on self-driving technology, robotics, and intelligent agents.'),
(30, 'Digital Forensics and Incident Response',12, 'Teaches forensic analysis, evidence gathering, and threat mitigation.'),
(31, 'Postgraduate Dissertation',              13, 'A major research project where students explore advanced topics in computing.');

-- ── Programmes — 10 programmes (IsPublished = 1 means visible) ───────────
INSERT INTO Programmes
    (ProgrammeName, LevelID, ProgrammeLeaderID, Description, IsPublished)
VALUES
('BSc Computer Science',       1,  1,  'A broad computer science degree covering programming, AI, cybersecurity, and software engineering.', 1),
('BSc Software Engineering',   1,  2,  'A specialised degree focusing on the development and lifecycle of software applications.', 1),
('BSc Artificial Intelligence',1,  3,  'Focuses on machine learning, deep learning, and AI applications.', 1),
('BSc Cyber Security',         1,  4,  'Explores network security, ethical hacking, and digital forensics.', 1),
('BSc Data Science',           1,  5,  'Covers big data, machine learning, and statistical computing.', 1),
('MSc Machine Learning',       2,  11, 'A postgraduate degree focusing on deep learning, AI ethics, and neural networks.', 1),
('MSc Cyber Security',         2,  12, 'A specialised programme covering digital forensics, cyber threat intelligence, and security policy.', 1),
('MSc Data Science',           2,  13, 'Focuses on big data analytics, cloud computing, and AI-driven insights.', 1),
('MSc Artificial Intelligence',2,  14, 'Explores autonomous systems, AI ethics, and deep learning technologies.', 1),
('MSc Software Engineering',   2,  15, 'Emphasises software design, blockchain applications, and cutting-edge methodologies.', 1);

-- ── ProgrammeModules — 85 links ───────────────────────────────────────────
INSERT INTO ProgrammeModules (ProgrammeID, ModuleID, Year) VALUES
(1, 1, 1), (1, 2, 1), (1, 3, 1), (1, 4, 1),
(1, 5, 2), (1, 6, 2), (1, 7, 2), (1, 8, 2),
(1, 11, 3), (1, 13, 3), (1, 15, 3), (1, 18, 3),
(2, 1, 1), (2, 2, 1), (2, 3, 1), (2, 4, 1),
(2, 5, 2), (2, 6, 2), (2, 12, 2), (2, 14, 2),
(2, 13, 3), (2, 15, 3), (2, 16, 3), (2, 18, 3),
(3, 1, 1), (3, 2, 1), (3, 3, 1), (3, 4, 1),
(3, 5, 2), (3, 9, 2), (3, 8, 2), (3, 10, 2),
(3, 13, 3), (3, 15, 3), (3, 16, 3), (3, 18, 3),
(4, 1, 1), (4, 2, 1), (4, 3, 1), (4, 4, 1),
(4, 7, 2), (4, 10, 2), (4, 11, 2), (4, 17, 2),
(4, 15, 3), (4, 16, 3), (4, 17, 3), (4, 18, 3),
(5, 1, 1), (5, 2, 1), (5, 3, 1), (5, 4, 1),
(5, 5, 2), (5, 6, 2), (5, 9, 2), (5, 16, 2),
(5, 9, 3), (5, 14, 3), (5, 16, 3), (5, 18, 3),
(6, 19, 1), (6, 24, 1), (6, 27, 1), (6, 29, 1), (6, 31, 1),
(7, 20, 1), (7, 26, 1), (7, 30, 1), (7, 23, 1), (7, 31, 1),
(8, 21, 1), (8, 22, 1), (8, 27, 1), (8, 28, 1), (8, 31, 1),
(9, 19, 1), (9, 24, 1), (9, 28, 1), (9, 29, 1), (9, 31, 1),
(10, 23, 1), (10, 22, 1), (10, 25, 1), (10, 26, 1), (10, 31, 1);

-- ── InterestedStudents — 4 sample registrations ───────────────────────────
INSERT INTO InterestedStudents (ProgrammeID, StudentName, Email) VALUES
(1, 'John Doe',    'john.doe@example.com'),
(4, 'Jane Smith',  'jane.smith@example.com'),
(6, 'Alex Brown',  'alex.brown@example.com'),
<<<<<<< HEAD
(9, 'Priya Patel', 'priya.patel@example.com');
=======
(9, 'Priya Patel', 'priya.patel@example.com');
>>>>>>> origin/main
