-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2021-03-30 05:51:56
-- 伺服器版本： 10.4.18-MariaDB
-- PHP 版本： 8.0.3

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
(37, 13, 124, '[\"是\"]', NULL),
(38, 14, 120, '未填答', NULL),
(39, 14, 121, '[\"6\"]', ''),
(40, 14, 122, '[\"true\"]', 'NULL'),
(41, 14, 123, '[\"A\"]', NULL);

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
(13, 63, '1234567890', 0),
(14, 64, '1', -1),
(15, 64, '2', -1),
(16, 64, '3', 1),
(17, 64, '4', 1),
(18, 64, '5', 1),
(19, 64, '6', 1),
(20, 64, '7', 1),
(21, 64, '8', 1),
(22, 64, '9', 1);

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
(63, '問卷範例123', 1, 2, 'false'),
(64, '多邀請碼問卷', 2, 2, 'false');

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
(120, 63, '是非題', 1, 0, '[\"false\",\"\",\"\",\"\",\"\",\"\",\"\"]', 'false'),
(121, 63, '單選題', 2, 1, '[\"true\",\"單選題1\",\"單選題2\",\"單選題3\",\"單選題4\",\"單選題5\",\"單選題6\"]', 'true'),
(122, 63, '多選題', 3, 2, '[\"true\",\"多選題1\",\"多選題2\",\"多選題3\",\"多選題4\",\"多選題5\",\"多選題6\"]', 'true'),
(123, 63, '問答題', 4, 3, '[\"false\",\"\",\"\",\"\",\"\",\"\",\"\"]', 'true'),
(124, 64, '哈哈', 1, 0, '[\"false\",\"\",\"\",\"\",\"\",\"\",\"\"]', 'true');

-- --------------------------------------------------------

--
-- 資料表結構 `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `questionid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `result`
--

INSERT INTO `result` (`id`, `questionid`) VALUES
(14, 63),
(13, 64);

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
  ADD KEY `questionid` (`questionid`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `answer`
--
ALTER TABLE `answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `code`
--
ALTER TABLE `code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
