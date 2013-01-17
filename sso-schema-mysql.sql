CREATE TABLE IF NOT EXISTS `auth_data` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `service_id` varchar(200) NOT NULL default '',
  `service_name` varchar(200) NOT NULL default '',
  `service_type` varchar(100) NOT NULL default '',
  `email` varchar(200) default NULL,
  `avatar` varchar(200) default NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `service_key` (`service_id`,`service_type`),
  KEY `email_key` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `token` varchar(200) NOT NULL default '',
  `driver` varchar(200) NOT NULL default '',
  `user_agent` varchar(200) NOT NULL default '',
  `expires` int(10) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;