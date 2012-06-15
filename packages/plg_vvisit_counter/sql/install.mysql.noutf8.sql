CREATE TABLE IF NOT EXISTS `#__vvisit_counter` (
	`time` INT(10) UNSIGNED NOT NULL,
	`visits` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`guests` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`bots` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`members` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	UNIQUE INDEX `time` (`time`),
	PRIMARY KEY (`time`)
);

INSERT INTO `#__vvisit_counter` (`time`, `visits`, `members`) 
	VALUES(UNIX_TIMESTAMP(), 1, 1) ON DUPLICATE KEY 
	UPDATE `visits`=`visits`+1, `members`=`members`+1;