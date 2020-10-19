
CREATE DATABASE IF NOT EXISTS `default` COLLATE 'utf8_general_ci' ;
CREATE DATABASE IF NOT EXISTS `testing` COLLATE 'utf8_general_ci' ;
GRANT ALL ON `default`.* TO 'default'@'%' ;
GRANT ALL ON `testing`.* TO 'default'@'%' ;


FLUSH PRIVILEGES ;
