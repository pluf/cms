ALTER TABLE `cms_terms` CHANGE `slug` `slug` varchar(256) NULL DEFAULT NULL;

/*
 * Create unique index on field 'slug' of term
 */ 
CREATE UNIQUE INDEX slug_unique_idx ON cms_terms (`tenant`, `slug`);
