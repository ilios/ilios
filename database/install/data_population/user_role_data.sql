
LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES
	(1, 'Course Director'),
	(2, 'Developer'),
	(3, 'Faculty'),
	(4, 'Student'),
	(5, 'Public'),
	(9, 'Former Student');
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;
