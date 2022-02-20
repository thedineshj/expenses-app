-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3308
-- Generation Time: Feb 20, 2022 at 07:22 PM
-- Server version: 5.7.24
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expense_sharing_application`
--
CREATE DATABASE IF NOT EXISTS `expense_sharing_application` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `expense_sharing_application`;

-- --------------------------------------------------------

--
-- Table structure for table `balance_records`
--

CREATE TABLE `balance_records` (
  `id` int(11) NOT NULL,
  `lender_id` int(11) DEFAULT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `balance` decimal(19,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `balance_records`
--

INSERT INTO `balance_records` (`id`, `lender_id`, `borrower_id`, `balance`) VALUES
(1, 1, 2, '250.00'),
(2, 1, 3, '250.00'),
(3, 1, 4, '250.00');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `about_bill` varchar(512) NOT NULL,
  `billed_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `expense_type` tinyint(4) NOT NULL COMMENT '0 = equal, exact = 1, percent = 2',
  `expense` decimal(19,2) NOT NULL,
  `bill_paid_by` int(11) DEFAULT NULL COMMENT 'primary user who takes from all and pays the bill'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `about_bill`, `billed_date`, `expense_type`, `expense`, `bill_paid_by`) VALUES
(1, 'abcf', '2022-02-20 19:17:09', 0, '1000.00', 1),
(2, 'abcf', '2022-02-20 19:19:17', 1, '1000.00', 1),
(3, 'abcf', '2022-02-20 19:20:21', 2, '1000.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(320) NOT NULL,
  `mobile` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`) VALUES
(1, 'Jake', 'jake@xyz.com', '1234567910'),
(2, 'Rosy', 'Rosy@xyz.com', '7894561230'),
(3, 'Tim', 'tim@xyz.com', '4561237890'),
(4, 'Pam', 'pam@xyz.com', '7531597412');

-- --------------------------------------------------------

--
-- Table structure for table `user_expenses`
--

CREATE TABLE `user_expenses` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_paid` decimal(19,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_expenses`
--

INSERT INTO `user_expenses` (`id`, `bill_id`, `user_id`, `amount_paid`) VALUES
(1, 1, 1, '250.00'),
(2, 1, 2, '250.00'),
(3, 1, 3, '250.00'),
(4, 1, 4, '250.00'),
(5, 2, 2, '600.00'),
(6, 2, 3, '400.00'),
(7, 3, 2, '600.00'),
(8, 3, 3, '400.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance_records`
--
ALTER TABLE `balance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `LENDER_ID_idx` (`lender_id`),
  ADD KEY `BORROWER_ID_idx` (`borrower_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_paid_by_idx` (`bill_paid_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `mobile_UNIQUE` (`mobile`);

--
-- Indexes for table `user_expenses`
--
ALTER TABLE `user_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id_idx` (`bill_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `balance_records`
--
ALTER TABLE `balance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_expenses`
--
ALTER TABLE `user_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance_records`
--
ALTER TABLE `balance_records`
  ADD CONSTRAINT `BORROWER_ID` FOREIGN KEY (`borrower_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `LENDER_ID` FOREIGN KEY (`lender_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bill_paid_by` FOREIGN KEY (`bill_paid_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_expenses`
--
ALTER TABLE `user_expenses`
  ADD CONSTRAINT `bill_id` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
