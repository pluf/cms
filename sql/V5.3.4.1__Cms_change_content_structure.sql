ALTER TABLE `cms_contents`
  ADD COLUMN `comment_count` int(11) NOT NULL DEFAULT 0 AFTER `downloads`,
  ADD COLUMN `comment_status` varchar(64) DEFAULT '' AFTER `downloads`,
  ADD COLUMN `password` varchar(150) DEFAULT '' AFTER `downloads`,
  ADD COLUMN `status` varchar(64) DEFAULT 'published' AFTER `downloads`,
  ADD COLUMN `parent_id` mediumint(9) unsigned DEFAULT 0 AFTER `modif_dtime`,
  ADD COLUMN `author_id` mediumint(9) unsigned NOT NULL DEFAULT 0 AFTER `modif_dtime`;
CREATE INDEX `parent_id_foreignkey_idx` ON `cms_contents`(`parent_id`);
CREATE INDEX `author_id_foreignkey_idx` ON `cms_contents`(`author_id`);

INSERT INTO `user_roles` (`name`,`description`,`application`,`code_name`,`tenant`)
    SELECT 'cms editor', 'Permission given to cms editors', 'cms', 'editor', `id` 
    FROM `tenants` ORDER BY `tenants`.`id`;
INSERT INTO `user_roles` (`name`,`description`,`application`,`code_name`,`tenant`)
    SELECT 'cms author', 'Permission given to cms authors', 'cms', 'author', `id` 
    FROM `tenants` ORDER BY `tenants`.`id`;