CREATE TABLE `cms_content_histories` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(100) NOT NULL DEFAULT '',
  `workflow` varchar(100) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `actor_id` mediumint(9) unsigned DEFAULT 0,
  `content_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `content_history_idx` (`tenant`,`content_id`),
  KEY `actor_of_content_history_idx` (`tenant`,`actor_id`),
  KEY `actor_id_foreignkey_idx` (`actor_id`),
  KEY `content_id_foreignkey_idx` (`content_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8