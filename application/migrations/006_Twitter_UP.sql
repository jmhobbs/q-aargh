CREATE TABLE  `twitter_users` (
	`user` VARCHAR( 50 ) NOT NULL,
	`access_key` TEXT NOT NULL,
	`secret_key` TEXT NOT NULL,
	`user_id` INT UNSIGNED,
	PRIMARY KEY (  `user` )
);