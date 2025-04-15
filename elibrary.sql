-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 15, 2025 at 06:15 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elibrary`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `src` varchar(150) NOT NULL,
  `date` text NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `phone`, `src`, `date`) VALUES
(1, 'admin', 'test@gmail.com', '$2y$10$FjqCwPkL9h51gw/Q74cqYu7wTj5NfjyLzQzYXaCAzWIsPkMEaMyym', '98343445996', 'https://res.cloudinary.com/dobpreu5w/image/upload/v1743494966/upload/urw0dtvxqoooquwsemkf.jpg', '25-03-15');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `author` varchar(150) NOT NULL,
  `category` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `published` varchar(150) NOT NULL,
  `src` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL,
  `rack_no` varchar(11) NOT NULL,
  `date` text NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `name`, `author`, `category`, `description`, `published`, `src`, `quantity`, `rack_no`, `date`) VALUES
(12, 'Basic Biology 101', 'Dr. Simon Clerk', 'Biology', '0', '2017-02-26', 'https://res.cloudinary.com/dobpreu5w/image/upload/v1743804794/upload/vyi1nriyx9jfnscvoxwl.jpg', 10, '0', '2022-05-08 16:13:24'),
(20, 'Python Programming', 'Magnus Lie hetland', 'Computer Science', '', '2022-03-17', 'Cover.jpeg', 9, '0', '2022-05-08 16:13:24'),
(21, 'Quantam Physics', 'Steven Holzner', 'Physics', '', '2021-09-17', 'download.jpeg', 3, '0', '2022-05-08 16:13:24'),
(22, 'Social Science', 'Aesra', 'History', '', '2022-03-04', 'IJSS.jpeg', -3, '0', '2021-05-08 16:13:24'),
(30, 'React JS', 'Facebook', 'Programming', 'React is a free and open-source front-end JavaScript library for building user interfaces based on UI components. It is maintained by Meta and a community of individual developers and companies.', '2022-05-07', 'react.jpg', 6, 'D-23', '2022-05-08 16:13:24'),
(35, 'mcmdc', 'cdd', 'dvd', 'dvd', '2025-03-12', 'Error: Failed to create upload directory.', 3, '23', '2025-03-13'),
(36, 'mcmdc', 'cdd', 'dvd', 'dvd', '2025-03-12', 'Error: Failed to create upload directory.', 3, '23', '2025-03-13'),
(37, 'mcmdc', 'cdd', 'dvd', 'dvd', '2025-03-12', 'Error: Failed to create upload directory.', 3, '23', '2025-03-13'),
(39, 'mnbd', 'cdcc', 'dcdc', 'dcd', '2025-03-12', 'Error: Failed to create upload directory.', 2, '2', '2025-03-13');

-- --------------------------------------------------------

--
-- Table structure for table `fined_users`
--

CREATE TABLE `fined_users` (
  `id` int(11) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `amount` int(11) NOT NULL,
  `exceed_day` int(11) NOT NULL,
  `date` varchar(150) NOT NULL,
  `src` text NOT NULL,
  `bookname` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issued`
--

CREATE TABLE `issued` (
  `id` int(11) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `bookname` varchar(150) NOT NULL,
  `book_id` int(11) NOT NULL,
  `issued_date` varchar(150) NOT NULL,
  `deadline` varchar(150) NOT NULL,
  `src` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `src` varchar(200) NOT NULL,
  `date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reserved`
--

CREATE TABLE `reserved` (
  `id` int(11) NOT NULL,
  `user_email` text NOT NULL,
  `book_id` int(11) NOT NULL,
  `bookname` text NOT NULL,
  `src` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returned`
--

CREATE TABLE `returned` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `bookname` varchar(100) NOT NULL,
  `book_id` int(11) NOT NULL,
  `date` varchar(100) NOT NULL,
  `src` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returned`
--

INSERT INTO `returned` (`id`, `username`, `user_email`, `bookname`, `book_id`, `date`, `src`) VALUES
(66, 'ose', 'amiolemenemma@gmail.com', 'Quantam Physics', 21, '2025/04/11', 'download.jpeg'),
(67, 'ose', 'amiolemenemma@gmail.com', 'Python Programming', 20, '2025/04/11', 'Cover.jpeg'),
(68, 'ose', 'amiolemenemma@gmail.com', 'Basic Biology 101', 12, '2025/04/11', 'https://res.cloudinary.com/dobpreu5w/image/upload/v1743804794/upload/vyi1nriyx9jfnscvoxwl.jpg'),
(69, 'ose', 'amiolemenemma@gmail.com', 'Basic Biology 101', 12, '2025/04/15', 'https://res.cloudinary.com/dobpreu5w/image/upload/v1743804794/upload/vyi1nriyx9jfnscvoxwl.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `phone` varchar(150) NOT NULL,
  `src` varchar(150) NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `phone`, `src`, `date`) VALUES
(1, 'Ose', 'test@gmail.com', '$2y$10$FjqCwPkL9h51gw/Q74cqYu7wTj5NfjyLzQzYXaCAzWIsPkMEaMyym', '9834344522', 'cat.webp_6277b6abbf35d', '25/03/15'),
(29, 'ose', 'amiolemenemma@gmail.com', '$2y$10$pt8jRLE5SLF7CcOTs2Iije.bzBxDCEb2Omk9KdQyBFA87ry6ambw.', '07424489703', 'https://res.cloudinary.com/dobpreu5w/image/upload/v1743804417/upload/kxmtpuaiopen4nwclmit.jpg', '25/04/01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fined_users`
--
ALTER TABLE `fined_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issued`
--
ALTER TABLE `issued`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reserved`
--
ALTER TABLE `reserved`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returned`
--
ALTER TABLE `returned`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `fined_users`
--
ALTER TABLE `fined_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `issued`
--
ALTER TABLE `issued`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reserved`
--
ALTER TABLE `reserved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `returned`
--
ALTER TABLE `returned`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
