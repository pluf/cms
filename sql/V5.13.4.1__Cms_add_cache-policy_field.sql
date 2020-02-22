ALTER TABLE `cms_contents`
   ADD COLUMN `cache_policy` varchar(512) DEFAULT 'max-age=21600' AFTER `comment_count`;


