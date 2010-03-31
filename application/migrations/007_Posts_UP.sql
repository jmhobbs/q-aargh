CREATE TABLE  `posts` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`island_code` varchar(255) NOT NULL,
	`user_id` int(10) UNSIGNED NOT NULL,
	`posted` datetime NOT NULL,
	`post_id` int(10) UNSIGNED NOT NULL,
	`type` varchar(30) NOT NULL DEFAULT 'text',
	PRIMARY KEY  ( `id` )
);

CREATE TABLE `text_posts` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`content` text NOT NULL,
	PRIMARY KEY  ( `id` )
);

CREATE TABLE `link_posts` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`link` varchar(255) NOT NULL,
	PRIMARY KEY  ( `id` )
);

CREATE TABLE `image_posts` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`image` varchar(255) NOT NULL,
	PRIMARY KEY  ( `id` )
);