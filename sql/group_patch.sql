CREATE TABLE IF NOT EXISTS concurrent_user_xref(
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier of the group',
  `groupid` int NOT NULL,
  `userid` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
);