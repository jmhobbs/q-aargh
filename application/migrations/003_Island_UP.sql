CREATE TABLE IF NOT EXISTS `islands` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  
  `code` varchar(42) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `introduction` text NOT NULL DEFAULT '',
  
  `visibility` varchar(10) NOT NULL DEFAULT 'private',
  `password` varchar(255) NOT NULL,
  
  `postibility` varchar(10) NOT NULL DEFAULT 'private',
  `post_password` varchar(255) NOT NULL,
  
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0',
  
   `created` datetime NOT NULL,
   `modified` datetime NOT NULL,
  
  PRIMARY KEY  ( `id` ),
  UNIQUE KEY `uniq_code` ( `code` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `islands`
  ADD CONSTRAINT `islands_ibfk_1` FOREIGN KEY ( `user_id` ) REFERENCES `users` ( `id` ) ON DELETE CASCADE;