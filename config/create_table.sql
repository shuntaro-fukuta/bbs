CREATE TABLE `posts` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `title`      varchar(255) NOT NULL,
  `comment`    text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `password`   varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8640 DEFAULT CHARSET=utf8 |
