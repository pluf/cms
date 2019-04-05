/*
 * Content metas
 * 
 * The meta date of a content.
 */
CREATE TABLE `cms_content_metas` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  `content_id` mediumint(9) unsigned DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

