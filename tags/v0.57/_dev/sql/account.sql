CREATE TABLE hhb_account (
  `acc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acc_email` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `acc_password` char(32) COLLATE utf8_polish_ci NOT NULL,
  `acc_first_name` varchar(100) COLLATE utf8_polish_ci,
  `acc_last_name` varchar(100) COLLATE utf8_polish_ci,
  `acc_recovery_key` char(32) COLLATE utf8_polish_ci DEFAULT '',
  PRIMARY KEY (`acc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;