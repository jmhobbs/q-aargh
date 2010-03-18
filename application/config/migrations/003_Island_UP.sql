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
  `last_view` int(10) UNSIGNED,
  
   `created` int(10) UNSIGNED NOT NULL,
   `modified` int(10) UNSIGNED NOT NULL,
  
  PRIMARY KEY  ( `id` ),
  UNIQUE KEY `uniq_code` ( `code` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `islands`
  ADD CONSTRAINT `islands_ibfk_1` FOREIGN KEY ( `user_id` ) REFERENCES `users` ( `id` ) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `text_posts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `island_id` int(11) UNSIGNED NOT NULL,

  `title` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL DEFAULT '',
  
  `visibility` varchar(10) NOT NULL DEFAULT 'private',
  `created` int(10) UNSIGNED NOT NULL,
  
  PRIMARY KEY  ( `id` ),
  KEY `fk_user_id` ( `user_id` ),
  KEY `fk_island_id` ( `island_id` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `text_posts`
  ADD CONSTRAINT `text_posts_ibfk_1` FOREIGN KEY ( `user_id` ) REFERENCES `users` ( `id` ) ON DELETE CASCADE,
  ADD CONSTRAINT `text_posts_ibfk_2` FOREIGN KEY ( `island_id` ) REFERENCES `islands` ( `id` ) ON DELETE CASCADE;