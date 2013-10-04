INSERT INTO `organisation` (`id`, `parent_id`, `minoto_id`, `type`, `name`, `url`, `enabled`, `player_minoto_id`, `theme`, `logo_url`, `color_1`, `color_2`, `publish_url_1`, `publish_url_2`) VALUES (NULL, NULL, '1788', 'reseller', 'FWD', 'http://', 'true', '3133', '', 'assets/ing/FWD_logo_RGB_117x30px_72dpi.png', '#EA650D', '#E64415', '', '');

UPDATE  `organisation` SET  `parent_id` =  '17' WHERE  `organisation`.`id` =1;
UPDATE  `organisation` SET  `parent_id` =  '17' WHERE  `organisation`.`id` =2;
UPDATE  `organisation` SET  `parent_id` =  '17' WHERE  `organisation`.`id` =3;
UPDATE  `organisation` SET  `parent_id` =  '17' WHERE  `organisation`.`id` =4;

INSERT INTO `organisation` (`id`, `parent_id`, `minoto_id`, `type`, `name`, `url`, `enabled`, `player_minoto_id`, `theme`, `logo_url`, `color_1`, `color_2`, `publish_url_1`, `publish_url_2`) VALUES
(18, NULL, 2037, 'reseller', 'Dutchview', 'http://xiao.datiq.net', 'true', 3133, 'default', '', '#EA650D', '#E64415', '', '');