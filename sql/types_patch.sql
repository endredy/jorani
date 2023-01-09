ALTER TABLE `types` ADD COLUMN `nodeduction` INT DEFAULT 0 DEFAULT NULL COMMENT 'This type is independent from employee\'s paid leave (e.g. home office, sick leave, etc). This type of leave doesn\'t decrease days off.';
ALTER TABLE `types` ADD COLUMN `noapproval` INT DEFAULT 0 COMMENT 'this type needs no approval (e.g. working in an external location)';
ALTER TABLE `types` ADD COLUMN `extrainput` varchar(64) DEFAULT NULL COMMENT 'this type needs an extra input (e.g. name of external location)';
ALTER TABLE `types` ADD COLUMN `extrainput` INT DEFAULT 0 COMMENT 'this type needs an extra input (e.g. name of external location)';
ALTER TABLE `types` ADD COLUMN `limit` INT DEFAULT NULL COMMENT 'weekly limit (e.g. home office)';
ALTER TABLE `types` ADD COLUMN `color` varchar(10) DEFAULT NULL COMMENT 'color of this type in calendars';
ALTER TABLE `types` ADD COLUMN `textcolor` varchar(10) DEFAULT NULL COMMENT 'textcolor of this type in calendars';
