/*
 * The key must be unique for a term not a tenant
 */
ALTER TABLE `cms_term_metas` 
	DROP INDEX `key_unique_idx`;

CREATE UNIQUE INDEX `key_unique_idx` 
	ON `cms_term_metas`(`term_id`, `key`);