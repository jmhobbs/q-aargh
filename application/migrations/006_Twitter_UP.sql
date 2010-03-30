CREATE TABLE  `twitter_users` (
	`username` VARCHAR( 50 ) NOT NULL,
	`user_id` INT UNSIGNED,
	PRIMARY KEY (  `username` )
);

INSERT INTO `roles` ( `id`, `name`, `description` ) VALUES(3, 'twitter', 'Account created through twitter.');