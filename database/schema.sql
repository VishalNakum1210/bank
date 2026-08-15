-- ====================================================================
-- NNP Online Banking System - Database Schema
-- Compatible with MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.4+
-- ====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Create Database if not exists
-- CREATE DATABASE IF NOT EXISTS `bank` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE `bank`;

-- --------------------------------------------------------
-- Table structure for table `account_details`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `account_details` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) NOT NULL DEFAULT 'NNP Bank',
  `account_number` varchar(20) NOT NULL,
  `account_type` varchar(255) NOT NULL DEFAULT 'Savings',
  `age` int(11) NOT NULL,
  `pin` int(11) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `money_bank`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `money_bank` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `debit_card` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `persnol`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `persnol` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `history`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `paymant_status` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_account_id` (`account_id`),
  KEY `idx_time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Seed Sample Demo Account (Optional)
-- Password for demo user: Demo@123
-- --------------------------------------------------------

-- Insert demo personal info
INSERT INTO `persnol` (`account_id`, `name`, `mobile_number`, `email`) VALUES
(1, 'Demo User', '9876543210', 'demo@nnpbank.com')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert demo account details
INSERT INTO `account_details` (`account_id`, `bank_name`, `account_number`, `account_type`, `age`, `pin`) VALUES
(1, 'NNP Bank', '8492 1092 3847 5019', 'Savings', 24, 1234)
ON DUPLICATE KEY UPDATE `account_number` = VALUES(`account_number`);

-- Insert demo bank credentials ($2y$10$e.wFvBq6k6jM0iZtLlhB9u0v2z9C.a8W9w1aE2Xw8hA7p9g7vO.eq is hash of Demo@123)
INSERT INTO `money_bank` (`account_id`, `username`, `user_password`, `amount`, `debit_card`) VALUES
(1, 'demouser', '$2y$10$wN1rYpB13uV3.oK6vR1Eae1c8Bq0D9uJ8iZtLlhB9u0v2z9Ca8W9w', 25000.00, 1)
ON DUPLICATE KEY UPDATE `amount` = VALUES(`amount`);

-- Insert sample transaction records
INSERT INTO `history` (`account_id`, `paymant_status`, `desc`, `time`, `amount`) VALUES
(1, 'Credited', 'Initial Account Opening Balance', NOW() - INTERVAL 5 DAY, 25000.00),
(1, 'Debited', 'Purchase Debit card', NOW() - INTERVAL 3 DAY, 399.00),
(1, 'Credited', 'Salary Credit', NOW() - INTERVAL 1 DAY, 15000.00)
ON DUPLICATE KEY UPDATE `amount` = VALUES(`amount`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
