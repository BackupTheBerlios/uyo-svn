# If you alter the database in any way, make sure to record the changes here!

USE `uyo_development`;

CREATE TABLE `uyo_settings` (
  `mail_server` VARCHAR(30),
  `mail_username` VARCHAR(30),
  `mail_password` VARCHAR(32),
  `mail_account` VARCHAR(40),
  `mail_mailbox` VARCHAR(30)
);

# Let's insert the one row this table should contain
INSERT INTO `uyo_settings` () VALUES ();

CREATE TABLE `feeds` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
  `url` VARCHAR(255) NOT NULL, `timer` DATETIME DEFAULT '0000-00-00 00:00:00', 
  `lastmodified` DATETIME DEFAULT '0000-00-00 00:00:00', 
  `etag` VARCHAR(255), 
  `reports` ENUM('lmonly', 'etagonly', 'complete', 'none')
);

CREATE TABLE `users` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
  `username` VARCHAR(15) NOT NULL,
  `password` CHAR(32) NOT NULL DEFAULT MD5(''),
  `rank` TINYINT(1) NOT NULL DEFAULT 0,
  `firstname` VARCHAR(50) NOT NULL, 
  `lastname` VARCHAR(50), 
  `nickname` VARCHAR(50), 
  `url` VARCHAR(255),
  `email` VARCHAR(255) NOT NULL,
  `photo` BLOB # 65 KB ought to be more than enough
  # is it better to have the photos as files or as blobs?
);

# Create an admin user and a user for automatic posts (must update autobot's password with a strong, random password later!)
INSERT INTO `users` (`username`, `password`, `rank`, `firstname`) VALUES ('admin', MD5('admin'), 9, 'Administrator');
INSERT INTO `users` (`username`, `rank`, `firstname`) VALUES ('autobot', 4, 'Automatic post');

CREATE TABLE `groups` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255),
  `description` TEXT
);

# Create a demo group
INSERT INTO `groups` (`name`, `description`) VALUES ('demogroup', 'This is a demonstration of a group. Make your own, or modify/delete this one if you want. (You probably do. ;) )');

CREATE TABLE `entries` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
  `date` DATETIME NOT NULL,
  `author` INT NOT NULL REFERENCES users(id), 
  `group` INT NOT NULL REFERENCES groups(id), 
  `title` VARCHAR(255), 
  `text` TEXT,
  `status` ENUM('manual', 'automatic'),
  `type` ENUM('text', 'link', 'rescheduling', 'important', 'mail') NOT NULL DEFAULT 'text'
);

# Create a couple of demo posts
INSERT INTO `entries` (`author`, `group`, `title`, `text`, `status`, `type`) VALUES (2, 1, 'Demo Post 1', '<p>Hey there! This is a small demo post, just to show you what entries into this system can look like.</p>', 'automatic', 'text');
INSERT INTO `entries` (`author`, `group`, `title`, `text`, `status`, `type`) VALUES (2, 1, 'Demo Post 2', '<p>If you like what you see, well, GOOD FOR YOU! ;)</p><p>Nah, seriously, if you do--send me an email! You can reach me at \'mail\', the usual A shaped doodle, \'funky-m.com\'. Thanks!</p>', 'automatic', 'link');

CREATE TABLE `comments` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entry` INT REFERENCES entries(id),
  `author` INT REFERENCES users(id),
  `author_ip` VARCHAR(15),
  `author_email` VARCHAR(50),
  `author_url` VARCHAR(255),
  `text` TEXT
);

# Create a demo comment
INSERT INTO `comments` (`date`, `author`, `entry`, `text`) VALUES (NOW(), 2, 1, 'Hi there!<br />This is a comment, just to show you how these guys can look. Enjoy!<br />--UyO');

CREATE TABLE `links` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `entry` INT NOT NULL REFERENCES entries(id),
  `description` VARCHAR(255) NOT NULL, 
  `url` VARCHAR(255) NOT NULL, 
  `type` ENUM('link', 'pdf', 'doc') NOT NULL
);

# Create a link to my blog in post #2
INSERT INTO `links` (`entry`, `description`, `url`, `type`) VALUES (2, 'Funky M Dot Com', 'http://funky-m.com', 'link');