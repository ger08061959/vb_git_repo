CREATE TABLE `video` (
	`id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`business_unit` INT(10) NOT NULL,
	`identifier` TINYTEXT NOT NULL,
	`date_created` DATETIME NOT NULL,
	`date_modified` DATETIME NOT NULL, 
	`date_deleted` DATETIME NOT NULL, 
	`date_release` DATETIME NOT NULL, 
	`date_expiration` DATETIME NOT NULL, 
	`created_by` INT(10) NOT NULL, 
	`deleted_by` INT(10) NOT NULL, 
	`title` TINYTEXT NOT NULL, 
	`author` TINYTEXT NOT NULL, 
	`caption` TINYTEXT NOT NULL, 
	`source` TINYTEXT NOT NULL, 
	`description` TEXT NOT NULL, 
	`keywords` TINYTEXT NOT NULL, 
	`copyright` TINYTEXT NOT NULL, 
	`duration` INT NOT NULL, 
	`thumbnail` TINYTEXT NOT NULL, 
	`screenshot` TINYTEXT NOT NULL, 
	`metaoption_1` TINYTEXT NOT NULL, 
	`metaoption_2` TINYTEXT NOT NULL, 
	`metaoption_3` TINYTEXT NOT NULL, 
	`metaoption_4` TINYTEXT NOT NULL, 
	`metaoption_5` TINYTEXT NOT NULL, 
	`metaoption_6` TINYTEXT NOT NULL)
ENGINE = MyISAM;


ALTER TABLE `video`
	CHANGE `date_created` `date_created` DATETIME NULL DEFAULT NULL,
	CHANGE `date_modified` `date_modified` DATETIME NULL DEFAULT NULL,
	CHANGE `date_deleted` `date_deleted` DATETIME NULL DEFAULT NULL,
	CHANGE `date_release` `date_release` DATETIME NULL DEFAULT NULL,
	CHANGE `date_expiration` `date_expiration` DATETIME NULL DEFAULT NULL;
