ALTER TABLE  `video` ADD  `metadata` BLOB NOT NULL;
ALTER TABLE  `whitelist` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `whitelist` (`id` ,`ip` ,`description`) VALUES (NULL, '193.67.218.4/32', 'ING Insurance (2013-07-31)');
INSERT INTO `whitelist` (`id`, `ip`, `description`) VALUES (NULL, '193.67.218.5/32', 'ING Insurance (2013-07-31)');

-------------

ALTER TABLE  `whitelist` ADD  `organisation_id` MEDIUMINT( 8 ) NULL DEFAULT NULL AFTER  `id`;
ALTER TABLE  `whitelist` ADD INDEX (  `organisation_id` );
ALTER TABLE  `whitelist` CHANGE  `organisation_id`  `organisation_id` MEDIUMINT( 8 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `whitelist` ADD FOREIGN KEY (  `organisation_id` ) REFERENCES  `organisation` (
`id`
) ON DELETE CASCADE ON UPDATE NO ACTION ;

TRUNCATE `whitelist`;
INSERT INTO `whitelist` (`id`, `organisation_id`, `ip`, `description`) VALUES
(NULL, NULL, '194.153.74.10/32', 'Datiq BV, Cessnalaan'),
(NULL, NULL, '77.173.205.83/32', 'Datiq BV, Developer Xiao, Rotterdam, NL (new ip)'),
(NULL, 2, '193.67.218.4/32', 'ING Insurance'),
(NULL, 2, '193.67.218.5/32', 'ING Insurance'),
(NULL, 4, '77.173.205.83/32', 'XIAO');