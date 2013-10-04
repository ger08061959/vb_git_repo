UPDATE `video`
SET `status` = "published"
WHERE `status` = "authorised";

ALTER TABLE  `video` CHANGE  `business_unit`  `business_unit` VARCHAR( 100 ) NOT NULL;

ALTER TABLE  `activity` ADD  `item_type` VARCHAR( 20 ) NOT NULL ,
ADD  `item_id` INT( 11 ) UNSIGNED NOT NULL ,
ADD  `ip` VARCHAR( 20 ) NOT NULL ,
ADD  `date_created` DATETIME NOT NULL ,
ADD INDEX (  `item_id` );

ALTER TABLE  `activity` CHANGE  `item_type`  `item_type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `item_id`  `item_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE  `activity` CHANGE  `item_type`  `model` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `item_id`  `model_type` INT( 11 ) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE  `activity` CHANGE  `model_type`  `model_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `activity` CHANGE  `description`  `description` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE  `users` ADD  `business_unit` VARCHAR( 100 ) NOT NULL