CREATE TABLE IF NOT EXISTS `visits` (
  `id` int(10) UNSIGNED NOT NULL,
  `island_code` varchar(255) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `visited` datetime NOT NULL,
  PRIMARY KEY  ( `id` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;