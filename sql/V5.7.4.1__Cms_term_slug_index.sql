ALTER TABLE `cms_terms` CHANGE `slug` `slug` varchar(256) NULL DEFAULT NULL;
UPDATE `cms_terms` SET `slug`=NULL where `slug`='';
/*
 * Create unique index on field 'slug' of term
 */ 
CREATE UNIQUE INDEX slug_unique_idx ON cms_terms (`tenant`, `slug`);
