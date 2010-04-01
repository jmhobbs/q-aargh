CREATE TABLE `footsteps` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occurred` DATETIME  NOT NULL,
  `request_uri` text  NOT NULL,
  `query_string` text  NOT NULL,
  `controller` varchar(255)  NOT NULL,
  `method` varchar(255)  NOT NULL,
  `sessionid` varchar(255)  NOT NULL,
  `referral` varchar(255)  NOT NULL,
  `ip` varchar(40)  NOT NULL,
  `user_username` varchar(255)  NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `note` text  NOT NULL,
  `type` varchar(10)  NOT NULL DEFAULT 'HIT',
  PRIMARY KEY (`id`),
  INDEX `idx_user_id`(`user_id`),
  INDEX `idx_ip`(`ip`),
  INDEX `idx_session`(`sessionid`)
)
ENGINE = MyISAM
COMMENT = 'Track users from page to page, instrument your site.';