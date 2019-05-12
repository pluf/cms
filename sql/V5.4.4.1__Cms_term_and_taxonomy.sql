
CREATE TABLE `cms_terms` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cms_term_taxonomy` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `taxonomy` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(2048) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT 0,
  `term_id` mediumint(9) unsigned DEFAULT 0,
  `parent_id` mediumint(9) unsigned DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term_taxonomy_unique_idx` (`tenant`,`taxonomy`,`term_id`,`parent_id`),
  KEY `term_id_foreignkey_idx` (`term_id`),
  KEY `parent_id_foreignkey_idx` (`parent_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cms_content_cms_termtaxonomy_assoc` (
  `cms_termtaxonomy_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `cms_content_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`cms_termtaxonomy_id`,`cms_content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;