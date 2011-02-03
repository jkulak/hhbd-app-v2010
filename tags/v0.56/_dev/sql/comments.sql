CREATE TABLE `comments` (
  `com_id` int(11) NOT NULL AUTO_INCREMENT,
  `com_content` text CHARACTER SET utf8,
  `com_author` tinytext CHARACTER SET utf8,
  `com_author_ip` mediumtext CHARACTER SET utf8,
  `com_object_type` enum('a','n','p','l','s','u') COLLATE utf8_polish_ci NOT NULL,
  `com_object_id` int(11) DEFAULT NULL,
  `com_added` datetime DEFAULT NULL,
  `com_updated` datetime DEFAULT NULL,
  `com_updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`com_id`),
  KEY `com_object_id_index` (`com_object_id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;