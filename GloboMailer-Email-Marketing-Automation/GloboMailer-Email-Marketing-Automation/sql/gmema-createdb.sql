CREATE TABLE IF NOT EXISTS `gmema_pluginconfig` (
  `gmema_c_id` INT unsigned NOT NULL AUTO_INCREMENT,
  `gmema_c_publickey` VARCHAR(255) NOT NULL,
  `gmema_c_privatekey` VARCHAR(255) NOT NULL,
  `gmema_c_list` VARCHAR(255) NOT NULL,
  `selected_fields` VARCHAR(255) NOT NULL,
  `transact` VARCHAR(255) NOT NULL,
  `transact_msg` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`gmema_c_id`)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;