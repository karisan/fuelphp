
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` char(40) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  `login_hash` varchar(255) NOT NULL,
  `profile_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- 轉存資料表中的資料 `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `email`, `created_at`, `updated_at`, `group`, `last_login`, `login_hash`, `profile_fields`) VALUES
(1, 'alice', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', 1, 'alice@yahoo.com', 1375841852, 1375841852, 0, 0, '', ''),
(2, 'bob', '62cdb7020ff920e5aa642c3d4066950dd1f01f4d', 1, 'bob@foobar.com', 1375841852, 1375841852, 0, 0, '', ''),
(3, 'john', '9d989e8d27dc9e0ec3389fc855f142c3d40f0c50', 0, 'john@oracle.com', 1375841852, 1375841852, 0, 0, '', ''),
(4, 'kirsen', '9d989e8d27dc9e0ec3389fc855f142c3d40f0c50', 0, 'kirsten@go.com', 1375841852, 1375841852, 0, 0, '', ''),
(5, 'bill', 'e49512524f47b4138d850c9d9d85972927281da0', 0, 'bill@gmail.com', 1375841852, 1375841852, 0, 0, '', ''),
(6, 'mary', 'e49512524f47b4138d850c9d9d85972927281da0', 0, 'mary@wcupa.edu', 1375841852, 1375841852, 0, 0, '', ''),
(7, 'carla', '8843d7f92416211de9ebb963ff4ce28125932878', 1, 'carla@esu.edu', 1375841852, 1375841852, 0, 0, '', ''),
(8, 'dave', '60518c1c11dc0452be71a7118a43ab68e3451b82', 1, 'dave@temple.edu', 1375841852, 1375841852, 0, 0, '', ''),
(11, 'karisan', '6367c48dd193d56ea7b0baad25b19455e529f5ee', 0, 'karis083@hotmail.com', 1375862262, 0, 0, 0, '', ''),
(20, 'kk', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 1, 'kk@hotmail.com', 1376039902, 0, 1, 1376039902, '', ''),
(21, 'root', 'dc76e9f0c0006e8f919e0c515c66dbba3982f785', 1, 'root@hotmail.com', 1376272367, 0, 1, 1376272367, '', ''),
(23, 'ccccc', '7b21848ac9af35be0ddb2d6b9fc3851934db8420', 0, 'aaa@qqq.com', 1376274755, 0, 1, 1376274755, '', ''),
(24, 'abc', '011c945f30ce2cbafc452f39840f025693339c42', 0, 'abc@abc.com', 1376291797, 0, 1, 1376291797, '', '');

-- --------------------------------------------------------

--
-- 表的結構 `action_log`
--

CREATE TABLE IF NOT EXISTS `action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `time` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='儲存各種操作的記錄' AUTO_INCREMENT=1 ;

--
-- 表的結構 `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `binding` enum('paper','cloth') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- 轉存資料表中的資料 `books`
--

INSERT INTO `books` (`id`, `title`, `binding`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 'Java in a Nutshell', 'paper', 58, 1375841843, 1375841843),
(2, 'Programming Perl', 'paper', 3, 1375841843, 1375841843),
(3, 'Multimedia Systems', 'paper', 51, 1375841843, 1375841843),
(4, 'Data Structures in Java', 'cloth', 38, 1375841843, 1375841843),
(5, 'Java Foundation Classes', 'cloth', 17, 1375841843, 1375841843),
(8, 'jQuery Cookbook', 'paper', 23, 1375841843, 1375841843),
(10, 'Artificial Intelligence: A Modern Approach', 'cloth', 46, 1375841843, 1375841843),
(11, 'Introduction to Algorithms', 'cloth', 26, 1375841843, 1375841843),
(12, 'The C Programming Language', 'paper', 57, 1375841843, 1375841843),
(13, 'A Discipline of Programming', 'cloth', 30, 1375841843, 1375841843),
(14, 'Computability and Logic', 'paper', 49, 1375841843, 1375841843),
(15, 'The Psychology of Computer Programming', 'cloth', 7, 1375841843, 1375841843),
(16, 'Design and Validation of Computer Protocols', 'paper', 47, 1375841843, 1375841843),
(17, 'Computational Complexity', 'cloth', 11, 1375841843, 1375841843),
(18, 'Computability and Unsolvability', 'paper', 38, 1375841843, 1375841843),
(19, 'Database System Concepts', 'cloth', 14, 1375841843, 1375841843),
(20, 'Structured Computer Organization', 'cloth', 31, 1375841843, 1375841843),
(21, 'Modern Operating Systems', 'cloth', 23, 1375841843, 1375841843),
(22, 'Distributed Operating Systems', 'cloth', 33, 1375841843, 1375841843),
(23, 'The Mythical Man Month', 'cloth', 60, 1375841843, 1375841843),
(24, 'More Programming Pearls', 'paper', 32, 1375841843, 1375841843),
(25, 'Art of Computer Programming', 'cloth', 17, 1375841843, 1375841843),
(26, 'Machine learning', 'cloth', 8, 1375841843, 1375841843),
(27, 'High Performance Computing', 'paper', 56, 1375841843, 1375841843),
(28, 'Linux Programming Bible', 'cloth', 49, 1375841843, 1375841843),
(29, 'Real-Time Concepts for Embedded Systems', 'cloth', 44, 1375841843, 1375841843),
(30, 'Advanced Compiler Design and Implementation', 'paper', 34, 1375841843, 1375841843),
(31, 'Computer Networks', 'paper', 15, 1375841843, 1375841843),
(32, 'Crafting a Compiler with C', 'cloth', 54, 1375841843, 1375841843),
(33, 'UNIX Internals: The New Frontiers', 'cloth', 10, 1375841843, 1375841843),
(34, 'Understanding the Linux Kernel', 'paper', 27, 1375841843, 1375841843),
(35, 'IBM??', 'paper', 10, 0, 0);

