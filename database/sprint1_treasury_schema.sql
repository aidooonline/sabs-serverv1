-- Sprint 1: Treasury & Product Foundation Schema
-- Run this script to create the necessary tables for the new Loan System.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Capital Accounts (Funding Sources)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `capital_accounts` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'bank_account' COMMENT 'bank_account, investor_fund, mobile_money, etc',
  `account_details` TEXT DEFAULT NULL COMMENT 'JSON or text details like account number, sort code',
  `balance` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `description` TEXT DEFAULT NULL,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Central Loan Accounts (The Lending Pool)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `central_loan_accounts` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT 'Main Loan Pool',
  `balance` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'GHS',
  `description` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Fund Transfers (Internal Ledger for Capital -> Pool)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fund_transfers` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_account_id` BIGINT(20) UNSIGNED NOT NULL,
  `to_account_id` BIGINT(20) UNSIGNED NOT NULL,
  `amount` DECIMAL(15, 2) NOT NULL,
  `date` DATETIME NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fund_transfers_from` (`from_account_id`),
  KEY `idx_fund_transfers_to` (`to_account_id`),
  CONSTRAINT `fk_fund_transfers_from` FOREIGN KEY (`from_account_id`) REFERENCES `capital_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fund_transfers_to` FOREIGN KEY (`to_account_id`) REFERENCES `central_loan_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Loan Fees (Reusable Definitions)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `loan_fees` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'e.g. Interest Rate, Processing Fee',
  `type` VARCHAR(50) NOT NULL COMMENT 'percent, flat',
  `value` DECIMAL(10, 4) NOT NULL COMMENT 'Percentage (e.g. 8.00) or Flat Amount (e.g. 20.00)',
  `is_default` TINYINT(1) DEFAULT 0,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Loan Products (Configuration)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `loan_products` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `min_principal` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `max_principal` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `duration_options` VARCHAR(255) NOT NULL COMMENT 'Comma separated: 30,60,90,180,360',
  `repayment_frequency_options` VARCHAR(255) NOT NULL COMMENT 'Comma separated: Daily,Weekly,Monthly',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Loan Product Fees (Pivot Table)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `loan_product_fees` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_product_id` BIGINT(20) UNSIGNED NOT NULL,
  `loan_fee_id` BIGINT(20) UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lpf_product` (`loan_product_id`),
  KEY `idx_lpf_fee` (`loan_fee_id`),
  CONSTRAINT `fk_lpf_product` FOREIGN KEY (`loan_product_id`) REFERENCES `loan_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lpf_fee` FOREIGN KEY (`loan_fee_id`) REFERENCES `loan_fees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Default Fees
INSERT INTO `loan_fees` (`name`, `type`, `value`, `is_default`, `created_at`, `updated_at`) VALUES
('Standard Interest', 'percent', 8.00, 1, NOW(), NOW()),
('Processing Fee', 'percent', 5.00, 1, NOW(), NOW()),
('Application Fee', 'flat', 20.00, 1, NOW(), NOW());

-- Seed Initial Loan Pool
INSERT INTO `central_loan_accounts` (`name`, `balance`, `currency`) VALUES ('Main Loan Pool', 0.00, 'GHS');

COMMIT;
