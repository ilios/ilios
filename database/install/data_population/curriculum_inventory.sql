LOCK TABLES `curriculum_inventory_institution` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_institution` DISABLE KEYS */;
INSERT INTO `curriculum_inventory_institution` (`school_id`, `name`, `aamc_code`, `address_street`, `address_city`, `address_state_or_province`, `address_zipcode`, `address_country_code`) VALUES (1, 'School of Medicine', '00000', '', '', '', '', '');
INSERT INTO `curriculum_inventory_institution` (`school_id`, `name`, `aamc_code`, `address_street`, `address_city`, `address_state_or_province`, `address_zipcode`, `address_country_code`) VALUES (2, 'School of Dentistry', '00000', '', '', '', '', '');
INSERT INTO `curriculum_inventory_institution` (`school_id`, `name`, `aamc_code`, `address_street`, `address_city`, `address_state_or_province`, `address_zipcode`, `address_country_code`) VALUES (3, 'School of Pharmacy', '00000', '', '', '', '', '');
INSERT INTO `curriculum_inventory_institution` (`school_id`, `name`, `aamc_code`, `address_street`, `address_city`, `address_state_or_province`, `address_zipcode`, `address_country_code`) VALUES (4, 'School of Nursing', '00000', '', '', '', '', '');
INSERT INTO `curriculum_inventory_institution` (`school_id`, `name`, `aamc_code`, `address_street`, `address_city`, `address_state_or_province`, `address_zipcode`, `address_country_code`) VALUES (5, 'Other', '00000', '', '', '', '', '');
/*!40000 ALTER TABLE `curriculum_inventory_institution` ENABLE KEYS */;
UNLOCK TABLES;
