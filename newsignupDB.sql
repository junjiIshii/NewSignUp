-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 
-- サーバのバージョン： 10.1.38-MariaDB
-- PHP Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--


-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `nicname` varchar(25) CHARACTER SET utf8 NOT NULL,
  `grade` varchar(11) CHARACTER SET utf8 NOT NULL,
  `route` varchar(20) CHARACTER SET utf8 NOT NULL,
  `problem_word` varchar(255) CHARACTER SET utf8 NOT NULL,
  `grammar` varchar(255) CHARACTER SET utf8 NOT NULL,
  `reading` varchar(255) CHARACTER SET utf8 NOT NULL,
  `listening` varchar(255) CHARACTER SET utf8 NOT NULL,
  `studying` varchar(255) CHARACTER SET utf8 NOT NULL,
  `university` varchar(255) CHARACTER SET utf8 NOT NULL,
  `worst` varchar(255) CHARACTER SET utf8 NOT NULL,
  `others` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='英語の登録フォーム';

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `nicname`, `grade`, `route`, `problem_word`, `grammar`, `reading`, `listening`, `studying`, `university`, `worst`, `others`) VALUES
(21, 'iscii1996@gmail.com', '$2y$10$OK8q191gYHukCIong6VD.OXIVJaYsgvaNxJg/UVuFJ8O/shOAsciO', 'じゅんじ', '1年生', 'Twitter', 'pw0', 'pg1', 'pr2', 'pl3', 'pst4', '横浜市立大学', '単語を覚えること', 'メッセージなし');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
