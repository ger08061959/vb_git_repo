CREATE TABLE `video_metadata` (`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `organisation_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL, `sort_order` INT NOT NULL, `name` TINYTEXT NOT NULL, `label` TINYTEXT NOT NULL, `type` TINYTEXT NOT NULL, `values` TINYTEXT NOT NULL, `value` TINYTEXT NOT NULL, INDEX (`organisation_id`)) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE  `video_metadata` ADD FOREIGN KEY (  `organisation_id` ) REFERENCES  `organisation` (
`id`
) ON DELETE CASCADE ON UPDATE NO ACTION ;

------------------------------------------------
-- Do these when updating the minoto library. --
------------------------------------------------

INSERT INTO `organisation` (`id`, `parent_id`, `minoto_id`, `type`, `name`, `url`, `enabled`, `player_minoto_id`, `theme`, `logo_url`, `color_1`, `color_2`, `publish_url_1`, `publish_url_2`) VALUES
(21, NULL, 2091, 'reseller', 'Dutchview', 'http://www.dutchview.nl', 'true', 3133, '', '', '#EA650D', '#E64415', '', '');