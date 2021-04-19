-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2021-04-20 01:57:37
-- 伺服器版本： 10.4.18-MariaDB
-- PHP 版本： 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `51`
--

-- --------------------------------------------------------

--
-- 資料表結構 `answer`
--

CREATE TABLE `answer` (
  `id` int(11) NOT NULL,
  `resultid` int(11) NOT NULL,
  `questionsid` int(11) NOT NULL,
  `ans` text NOT NULL,
  `elseans` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `answer`
--

INSERT INTO `answer` (`id`, `resultid`, `questionsid`, `ans`, `elseans`) VALUES
(956, 107, 446, '[\"否\",\"false\",\"false\",\"false\",\"false\",\"false\",\"false\"]', NULL),
(957, 107, 447, '[\"true\",\"false\",\"false\",\"false\",\"false\",\"false\",\"false\"]', 'asdasdasd'),
(958, 107, 448, '[\"false\",\"true\",\"false\",\"false\",\"false\",\"false\",\"false\"]', 'asdasd'),
(959, 107, 449, '[\"adasd\",\"false\",\"false\",\"false\",\"false\",\"false\",\"false\"]', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `code`
--

CREATE TABLE `code` (
  `id` int(11) NOT NULL,
  `questionid` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `cishu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `code`
--

INSERT INTO `code` (`id`, `questionid`, `code`, `cishu`) VALUES
(222, 161, '1611618543265', 0),
(225, 164, '1641618543346', 0),
(226, 165, '1651618543350', 0);

-- --------------------------------------------------------

--
-- 資料表結構 `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `invitecodemod` int(11) NOT NULL,
  `pcpage` int(11) NOT NULL,
  `locked` text NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `question`
--

INSERT INTO `question` (`id`, `name`, `invitecodemod`, `pcpage`, `locked`) VALUES
(161, '問卷範例123', 1, 1, 'false'),
(164, '複製_問卷範例123(含答案)', 1, 1, 'false'),
(165, '複製_問卷範例123(不含答案)', 1, 1, 'false');

-- --------------------------------------------------------

--
-- 資料表結構 `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `questionid` int(11) NOT NULL,
  `description` text NOT NULL,
  `mode` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `required` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `questions`
--

INSERT INTO `questions` (`id`, `questionid`, `description`, `mode`, `item`, `options`, `required`) VALUES
(446, 161, '這是是非題', 1, 0, '[\"false\",\"\",\"\",\"\",\"\",\"\",\"\"]', 'false'),
(447, 161, '這是單選題', 2, 1, '[\"true\",\"單選題1\",\"單選題2\",\"單選題3\",\"單選題4\",\"單選題5\",\"單選題6\"]', 'false'),
(448, 161, '這是多選題', 3, 2, '[\"true\",\"多選題1\",\"多選題2\",\"多選題3\",\"多選題4\",\"多選題5\",\"多選題6\"]', 'false'),
(449, 161, '這是問答題', 4, 3, '[\"false\",\"false\",\"false\",\"false\",\"false\",\"false\",\"false\"]', 'false'),
(464, 164, '這是是非題', 1, 0, '[\"false\",\"\",\"\",\"\",\"\",\"\",\"\"]', 'false'),
(465, 164, '這是單選題', 2, 1, '[\"true\",\"單選題1\",\"單選題2\",\"單選題3\",\"單選題4\",\"單選題5\",\"單選題6\"]', 'false'),
(466, 164, '這是多選題', 3, 2, '[\"true\",\"多選題1\",\"多選題2\",\"多選題3\",\"多選題4\",\"多選題5\",\"多選題6\"]', 'false'),
(467, 164, '這是問答題', 4, 3, '[\"false\",\"false\",\"false\",\"false\",\"false\",\"false\",\"false\"]', 'false'),
(471, 165, '這是是非題', 1, 0, '[\"false\",\"\",\"\",\"\",\"\",\"\",\"\"]', 'false'),
(472, 165, '這是單選題', 2, 1, '[\"true\",\"單選題1\",\"單選題2\",\"單選題3\",\"單選題4\",\"單選題5\",\"單選題6\"]', 'false'),
(473, 165, '這是多選題', 3, 2, '[\"true\",\"多選題1\",\"多選題2\",\"多選題3\",\"多選題4\",\"多選題5\",\"多選題6\"]', 'false'),
(474, 165, '這是問答題', 4, 3, '[\"false\",\"false\",\"false\",\"false\",\"false\",\"false\",\"false\"]', 'false');

-- --------------------------------------------------------

--
-- 資料表結構 `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `questionid` int(11) NOT NULL,
  `codeid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `result`
--

INSERT INTO `result` (`id`, `questionid`, `codeid`) VALUES
(107, 161, 222);

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `account` varchar(50) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `username`, `account`, `password`) VALUES
(0, 'admin', 'admin', '1234');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questionsid` (`questionsid`),
  ADD KEY `resultid` (`resultid`);

--
-- 資料表索引 `code`
--
ALTER TABLE `code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questionid` (`questionid`);

--
-- 資料表索引 `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questionid` (`questionid`);

--
-- 資料表索引 `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questionid` (`questionid`),
  ADD KEY `codeid` (`codeid`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `answer`
--
ALTER TABLE `answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=960;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `code`
--
ALTER TABLE `code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=552;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `answer_ibfk_1` FOREIGN KEY (`questionsid`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `answer_ibfk_2` FOREIGN KEY (`resultid`) REFERENCES `result` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `code`
--
ALTER TABLE `code`
  ADD CONSTRAINT `code_ibfk_1` FOREIGN KEY (`questionid`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`questionid`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `result_ibfk_1` FOREIGN KEY (`questionid`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
