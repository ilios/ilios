
LOCK TABLES `alert_change_type` WRITE;
/*!40000 ALTER TABLE `alert_change_type` DISABLE KEYS */;
INSERT INTO `alert_change_type` VALUES
	(1, 'Time'),
	(2, 'Location'),
	(3, 'Learning Materials'),
	(4, 'Instructor'),
	(5, 'Course Director'),
	(6, 'Learner Group Membership'),
	(7, 'New Offering Created'),
	(8, 'Session Published');
/*!40000 ALTER TABLE `alert_change_type` ENABLE KEYS */;
UNLOCK TABLES;
