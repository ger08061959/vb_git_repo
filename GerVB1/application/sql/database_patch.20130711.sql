ALTER TABLE  `video` DROP FOREIGN KEY  `video_ibfk_1` ,
ADD FOREIGN KEY (  `created_by` ) REFERENCES  `users` (
`id`
) ON DELETE SET NULL ON UPDATE NO ACTION ;

ALTER TABLE  `video` DROP FOREIGN KEY  `video_ibfk_2` ,
ADD FOREIGN KEY (  `deleted_by` ) REFERENCES  `users` (
`id`
) ON DELETE SET NULL ON UPDATE NO ACTION ;

ALTER TABLE  `video` ADD  `metaoption_7` TINYTEXT NOT NULL AFTER  `metaoption_6`