LOCK TABLES `competency` WRITE;
/*!40000 ALTER TABLE `competency` DISABLE KEYS */;
INSERT INTO `competency` VALUES
    (1,'Patient Care',NULL,1),
    (2,'Medical Knowledge',NULL,1),
    (3,'Practice-Based Learning & Improvement',NULL,1),
    (4,'Interpersonal & Communication skills',NULL,1),
    (5,'Professionalism',NULL,1),
    (6,'Systems-Based Practice',NULL,1),
    (7,'History Taking',1,1),
    (8,'Physical Exam',1,1),
    (9,'Oral Case Presentation',1,1),
    (10,'Medical Notes',1,1),
    (11,'Procedures and Skills',1,1),
    (12,'Patient Management',1,1),
    (13,'Problem-Solving and Diagnosis',2,1),
    (14,'Knowledge for Practice',2,1),
    (15,'Information Management',3,1),
    (16,'Evidence-Based Medicine',3,1),
    (17,'Reflection and Self-Improvement',3,1),
    (18,'Doctor-Patient Relationship',4,1),
    (19,'Communication and Information Sharing with Patients and Families',4,1),
    (20,'Communication with the Medical Team',4,1),
    (21,'Professional Relationships',5,1),
    (22,'Boundaries and Priorities',5,1),
    (23,'Work Habits, Appearance, and Etiquette',5,1),
    (24,'Ethical Principles',5,1),
    (25,'Institutional, Regulatory, and Professional Society Standards',5,1),
    (26,'Healthcare Delivery Systems',6,1);
    (50, 'Systems Improvement',6,1);
    (51, 'Treatment',2,1);
    (52, 'Inquiry and Discovery',2,1);
/*!40000 ALTER TABLE `competency` ENABLE KEYS */;
UNLOCK TABLES;