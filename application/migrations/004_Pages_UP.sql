CREATE TABLE IF NOT EXISTS `pages` (
  `stub` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_view` int(10) UNSIGNED,  
  `created` int(10) UNSIGNED NOT NULL,
  `modified` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  ( `stub` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pages` VALUES ('home','Welcome to Q-Aargh!','Welcome!',0,0,0,0);