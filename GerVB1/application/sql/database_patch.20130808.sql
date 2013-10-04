CREATE TABLE IF NOT EXISTS `whitelist_domain` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` mediumint(8) unsigned DEFAULT NULL,
  `domain` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_id` (`organisation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;