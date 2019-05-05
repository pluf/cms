
CREATE TABLE `cms_term_metas` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(256) NOT NULL DEFAULT '',
  `value` longtext DEFAULT NULL,
  `term_id` mediumint(9) unsigned DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_unique_idx` (`tenant`,`key`),
  KEY `term_id_foreignkey_idx` (`term_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8