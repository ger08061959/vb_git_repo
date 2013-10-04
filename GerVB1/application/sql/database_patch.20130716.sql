CREATE TABLE  `whitelist` (
`id` INT( 11 ) UNSIGNED NOT NULL ,
`ip` VARCHAR( 32 ) NOT NULL ,
`description` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE  `whitelist` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT

ALTER TABLE  `organisation` ADD  `player_minoto_id` INT( 4 ) UNSIGNED NOT NULL ,
ADD  `logo_url` TINYTEXT NOT NULL ,
ADD  `color_1` VARCHAR( 20 ) NOT NULL ,
ADD  `color_2` VARCHAR( 20 ) NOT NULL

UPDATE  `organisation` SET  `player_minoto_id` =  '3133' WHERE 1;
UPDATE  `organisation` SET  `logo_url` =  'assets/ing/FWD_logo_RGB_117x30px_72dpi.png' WHERE 1;
UPDATE  `organisation` SET  `color_1` =  '#EA650D' WHERE 1;
UPDATE  `organisation` SET  `color_2` =  '#E64415' WHERE 1;

ALTER TABLE  `organisation` ADD  `theme` VARCHAR( 20 ) NOT NULL AFTER  `player_minoto_id`

ALTER TABLE  `organisation` ADD  `publish_url_1` VARCHAR( 200 ) NOT NULL ,
ADD  `publish_url_2` VARCHAR( 2000 ) NOT NULL

INSERT INTO `whitelist` (`id`, `ip`, `description`) VALUES
(2, '194.153.74.10/32', 'Datiq BV, Cessnalaan'),
(3, '82.136.209.246/32', 'Datiq BV, Developer Xiao, Rotterdam, NL'),
(4, '203.127.7.0/24', 'Asia 1'),
(5, '203.117.180.0/24', 'Asia 2'),
(6, '202.38.157.0/24', 'Australia'),
(7, '193.178.209.0/24', 'Belgium'),
(8, '194.127.138.0/24', 'Direct Germany + Austria'),
(9, '193.26.29.0/24', 'Direct France'),
(10, '91.199.173.0/24', 'Direct Italy'),
(11, '193.41.0.0/16', 'Direct Spain'),
(12, '221.134.114.0/24', 'Vysia Bank India'),
(13, '178.251.161.0/24', 'Luxembourg'),
(14, '10.67.4.0/24', 'Luxembourg private ip!!!'),
(15, '145.221.0.0/16', 'Netherlands'),
(16, '193.193.181.0/24', 'Poland'),
(17, '193.17.195.0/24', 'Romania'),
(18, '85.158.101.0/24', 'Turkey'),
(19, '193.178.209.0/24', 'CB CWE Brussels'),
(20, '80.169.232.0/24', 'CB Switserland'),
(21, '193.178.209.0/24', 'CB Italy'),
(22, '217.127.199.0/24', 'CB Spain'),
(23, '193.178.209.0/24', 'CB France'),
(24, '10.114.0.0/16', 'CB France private ip!!!'),
(25, '193.178.209.0/24', 'CB Portugal'),
(26, '145.221.0.0/16', 'CB UK'),
(27, '24.157.48.0/24', 'CB New York'),
(28, '177.43.228.0/24', 'CB Brazil'),
(29, '189.253.139.0/24', 'CB Mexico'),
(30, '199.19.251.0/24', 'CB Argentina'),
(31, '213.215.65.0/24', 'CB Slovakia'),
(32, '193.226.203.0/24', 'CB Hungary'),
(33, '10.98.129.0/24', 'CB Bulgaria private ip!!!'),
(34, '10.98.128.0/24', 'CB Bulgaria private ip!!!'),
(35, '91.198.155.0/24', 'CB Russia');

UPDATE `organisation` SET `id` = 1,`parent_id` = NULL,`minoto_id` = 1795,`type` = 'reseller',`name` = 'Test resellers',`url` = 'http://',`enabled` = 'true',`player_minoto_id` = 3133,`theme` = 'custom',`logo_url` = 'assets/datiq/datiq_logo.png',`color_1` = '#FF0000',`color_2` = '#A60000',`publish_url_1` = '',`publish_url_2` = '' WHERE `organisation`.`id` = 1;
UPDATE `organisation` SET `id` = 2,`parent_id` = NULL,`minoto_id` = 1797,`type` = 'publisher',`name` = 'Communicatie Nationale Nederlanden',`url` = 'http://',`enabled` = 'true',`player_minoto_id` = 3133,`theme` = '',`logo_url` = 'assets/ing/FWD_logo_RGB_117x30px_72dpi.png',`color_1` = '#EA650D',`color_2` = '#E64415',`publish_url_1` = '',`publish_url_2` = '' WHERE `organisation`.`id` = 2;
UPDATE `organisation` SET `id` = 3,`parent_id` = NULL,`minoto_id` = 1815,`type` = 'publisher',`name` = 'LeBlanc producties Test',`url` = 'http://www.datiq.com',`enabled` = 'true',`player_minoto_id` = 3133,`theme` = '',`logo_url` = 'assets/ing/FWD_logo_RGB_117x30px_72dpi.png',`color_1` = '#EA650D',`color_2` = '#E64415',`publish_url_1` = '',`publish_url_2` = '' WHERE `organisation`.`id` = 3;
UPDATE `organisation` SET `id` = 4,`parent_id` = NULL,`minoto_id` = 1816,`type` = 'publisher',`name` = 'Datiq Developers',`url` = 'http://xiao.datiq.net',`enabled` = 'true',`player_minoto_id` = 3133,`theme` = 'custom',`logo_url` = 'assets/datiq/datiq_logo.png',`color_1` = '#0088CC',`color_2` = '#006DCC',`publish_url_1` = 'http://xiao.datiq.net/',`publish_url_2` = 'http://dev.fwd.datiq.net/' WHERE `organisation`.`id` = 4;
UPDATE `organisation` SET `id` = 16,`parent_id` = 1,`minoto_id` = 1969,`type` = 'publisher',`name` = 'publisher test',`url` = 'http://www.google.com',`enabled` = 'true',`player_minoto_id` = 3133,`theme` = '',`logo_url` = 'assets/ing/FWD_logo_RGB_117x30px_72dpi.png',`color_1` = '#EA650D',`color_2` = '#E64415',`publish_url_1` = '',`publish_url_2` = '' WHERE `organisation`.`id` = 16;