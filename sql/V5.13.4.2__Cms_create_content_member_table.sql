
CREATE TABLE `cms_content_user_account_assoc` (
  `cms_content_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `user_account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_account_id`,`cms_content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

