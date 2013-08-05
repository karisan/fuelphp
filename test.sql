
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;


