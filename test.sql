
--
-- 資料庫: `test`
--

-- --------------------------------------------------------

--
-- 表的結構 `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `m_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `m_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `m_email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `m_time` datetime NOT NULL,
  `m_context` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


--
-- 表的結構 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

