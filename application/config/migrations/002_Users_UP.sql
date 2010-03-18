CREATE TABLE IF NOT EXISTS `relationships` (
  `user_a_id` int(10) UNSIGNED NOT NULL,
  `user_b_id` int(10) UNSIGNED NOT NULL,
  `created` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  ( `user_a_id`,`user_b_id` ),
  KEY `fk_user_a_id` ( `user_a_id` ),
  KEY `fk_user_b_id` ( `user_b_id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `relationships`
  ADD CONSTRAINT `relationships_ibfk_1` FOREIGN KEY ( `user_a_id` ) REFERENCES `users` ( `id` ) ON DELETE CASCADE,
  ADD CONSTRAINT `relationships_ibfk_2` FOREIGN KEY ( `user_b_id` ) REFERENCES `users` ( `id` ) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `user_properties` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `key` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  ( `id` ),
  UNIQUE KEY `uniq_user_key` ( `user_id`, `key` ),
  KEY `fk_user_id` ( `user_id` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `user_properties`
  ADD CONSTRAINT `user_properties_ibfk_1` FOREIGN KEY ( `user_id` ) REFERENCES `users` ( `id` ) ON DELETE CASCADE;