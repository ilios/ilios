
LOCK TABLES `learning_material_status` WRITE;
/*!40000 ALTER TABLE `learning_material_status` DISABLE KEYS */;
INSERT INTO `learning_material_status` VALUES
	(1, 'Draft'),
	(2, 'Final'),
	(3, 'Revised');
/*!40000 ALTER TABLE `learning_material_status` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `learning_material_user_role` WRITE;
/*!40000 ALTER TABLE `learning_material_user_role` DISABLE KEYS */;
INSERT INTO `learning_material_user_role` VALUES
	(1, 'Instructional Designer'),
	(2, 'Author'),
	(3, 'Co-Author');
/*!40000 ALTER TABLE `learning_material_user_role` ENABLE KEYS */;
UNLOCK TABLES;
