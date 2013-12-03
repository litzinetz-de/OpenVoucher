CREATE DATABASE IF NOT EXISTS openvoucher;
USE openvoucher;

DROP TABLE IF EXISTS `devices`;
CREATE TABLE IF NOT EXISTS `devices` (
  `type` varchar(10) NOT NULL,
  `addr` varchar(255) NOT NULL,
  `voucher_id` varchar(255) NOT NULL,
  PRIMARY KEY (`type`,`addr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `username` varchar(255) NOT NULL,
  `permission` varchar(255) NOT NULL,
  PRIMARY KEY (`username`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE IF NOT EXISTS `vouchers` (
  `voucher_id` varchar(255) NOT NULL,
  `dev_count` int(11) NOT NULL,
  `valid_until` int(11) NOT NULL,
  `verification_key` varchar(255),
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ---------------------------------------------------------

DROP TABLE IF EXISTS `settings`;
CREATE TABLE settings (
	setting VARCHAR(255) NOT NULL PRIMARY KEY,
	s_value VARCHAR(255) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- add an admin user
INSERT INTO users (username,pwd) VALUES ('admin',SHA1('admin'));
INSERT INTO permissions (username,permission) VALUES ('admin','all');

-- default config
INSERT INTO settings (setting,s_value) VALUES ('vouchertext1','Please enter the code');
INSERT INTO settings (setting,s_value) VALUES ('vouchertext2','to get internet access');
INSERT INTO settings (setting,s_value) VALUES ('use_verification','n');