-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 05:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `log_date`) VALUES
(1, 1, 'Updated category ID: 1', '2025-12-21 05:09:45'),
(2, 1, 'Updated product ID: 1', '2025-12-21 05:10:13'),
(3, 1, 'Updated user ID: 2', '2025-12-21 05:14:35'),
(4, 1, 'Archived category ID: 1', '2025-12-21 05:23:00'),
(5, 1, 'Archived user ID: 2', '2025-12-21 05:23:05'),
(6, 1, 'Restored user: cashier', '2025-12-21 05:23:16'),
(7, 1, 'Restored category: Rice', '2025-12-21 05:23:21'),
(8, 1, 'Archived category ID: 1', '2025-12-21 05:24:09'),
(9, 1, 'Restored category ID: 1', '2025-12-21 05:24:12'),
(10, 1, 'Archived product ID: 1', '2025-12-21 05:24:41'),
(11, 1, 'Restored product: Princess Beas', '2025-12-21 05:25:06'),
(12, 1, 'Archived category ID: 1', '2025-12-21 05:25:23'),
(13, 1, 'Restored category: Rice', '2025-12-21 05:25:38'),
(14, 1, 'Archived category ID: 1', '2025-12-21 05:26:21'),
(15, 1, 'Restored category: Rice', '2025-12-21 05:26:25'),
(16, 1, 'Archived product ID: 1', '2025-12-21 05:26:35'),
(17, 1, 'Restored product: Princess Beas', '2025-12-21 05:26:47'),
(18, 1, 'Archived category ID: 1', '2025-12-21 05:27:04'),
(19, 1, 'Restored category: Rice', '2025-12-21 05:27:12'),
(20, 1, 'Archived product ID: 1', '2025-12-21 05:29:17'),
(21, 1, 'Restored product: Princess Beas', '2025-12-21 05:29:28'),
(22, 1, 'Archived user: cashier', '2025-12-21 05:32:49'),
(23, 1, 'Restored user: cashier', '2025-12-21 05:32:57'),
(24, 1, 'Updated user: cashier', '2025-12-21 05:34:07'),
(25, 1, 'Added user: charitycashier', '2025-12-21 05:35:36'),
(26, 1, 'Deleted user: charitycashier', '2025-12-21 05:35:44'),
(27, 1, 'Added category: Fruits', '2025-12-21 05:36:16'),
(28, 1, 'Added product: Nike', '2025-12-21 05:37:18'),
(29, 1, 'Deleted product: Nike', '2025-12-21 05:37:22'),
(30, 1, 'Deleted category: Fruits', '2025-12-21 05:37:28'),
(31, 1, 'Updated stock for product ID: 1', '2025-12-21 07:23:25'),
(32, 1, 'Added category: Fruits', '2025-12-21 07:27:09'),
(33, 1, 'Added product: Bayabas', '2025-12-21 07:27:50'),
(34, 1, 'Updated product: Princess Beas', '2025-12-21 07:28:42'),
(35, 1, 'Updated stock for product: Princess Beas', '2025-12-21 07:32:53'),
(36, 1, 'Updated product: Princess Beas', '2025-12-21 07:34:43'),
(37, 1, 'Updated stock: Princess Beas (Qty: 1, Threshold: 6)', '2025-12-21 07:34:58'),
(38, 1, 'Added category: Glasswear', '2025-12-21 07:44:52'),
(39, 1, 'Updated category: Glasswear', '2025-12-21 07:45:03'),
(40, 1, 'Added product: Baso', '2025-12-21 07:45:24'),
(41, 1, 'Added product: test', '2025-12-21 07:46:24'),
(42, 1, 'Archived product: test', '2025-12-21 07:46:29'),
(43, 1, 'Added user: warehouse', '2025-12-21 15:10:17'),
(44, 1, 'Added user: manager', '2025-12-21 15:12:22'),
(45, 9, 'Updated stock: Princess Beas (Qty: 6, Threshold: 6)', '2025-12-21 15:36:49'),
(46, 9, 'Updated stock: Princess Beas (Qty: 5, Threshold: 5)', '2025-12-21 15:44:46'),
(47, 1, 'Restored product: test', '2025-12-22 04:50:49'),
(48, 1, 'Deleted product: test', '2025-12-22 04:50:54'),
(49, 1, 'Added user: inventory', '2025-12-23 06:36:09'),
(50, 11, 'Added category: Canned Goods', '2025-12-23 07:14:58'),
(51, 11, 'Added category: Canned Goods', '2025-12-23 07:17:36'),
(52, 11, 'Added product: asa', '2025-12-23 07:17:56'),
(53, 11, 'Added category: test', '2025-12-23 07:21:38'),
(54, 11, 'Added product: test', '2025-12-23 07:26:18'),
(55, 11, 'Added product: test2', '2025-12-23 07:29:36'),
(56, 11, 'Added category: test2', '2025-12-23 07:32:59'),
(57, 1, 'Deleted product: asa', '2025-12-23 07:42:46'),
(58, 1, 'Deleted product: test2', '2025-12-23 07:42:57'),
(59, 1, 'Deleted product: test', '2025-12-23 07:42:59'),
(60, 1, 'Deleted category: test2', '2025-12-23 08:44:03'),
(61, 1, 'Deleted category: Canned Goods', '2025-12-23 08:44:08'),
(62, 1, 'Deleted category: Canned Goods', '2025-12-23 08:44:09'),
(63, 11, 'Added category: Charity', '2025-12-23 08:44:52'),
(64, 11, 'Added product: Charity Daayata', '2025-12-23 08:45:15'),
(65, 1, 'Archived product: Charity Daayata', '2025-12-23 08:51:25'),
(66, 1, 'Restored product: Charity Daayata', '2025-12-23 08:56:22'),
(67, 1, 'Archived category: Charity', '2025-12-23 09:01:28'),
(68, 1, 'Restored category: Charity', '2025-12-23 09:02:06'),
(69, 9, 'Updated stock: Bayabas (Qty: 50, Threshold: 10)', '2025-12-23 09:06:40'),
(70, 11, 'Added category: pak yo', '2025-12-23 09:09:04'),
(71, 1, 'Added product: pamela', '2025-12-23 09:22:57'),
(72, 1, 'Deleted product: pamela', '2025-12-23 09:29:16'),
(73, 1, 'Deleted category: pak yo', '2025-12-23 09:29:30'),
(74, 1, 'Updated category: Rice', '2025-12-23 15:16:30'),
(75, 1, 'Archived category: Charity', '2025-12-23 15:16:35'),
(76, 1, 'Restored category: Charity', '2025-12-23 15:16:40'),
(77, 1, 'Deleted category: test', '2025-12-23 15:16:50'),
(78, 1, 'Added category: test', '2025-12-23 15:18:47'),
(79, 1, 'Added product: test', '2025-12-23 15:19:02'),
(80, 1, 'Archived category: test', '2025-12-23 15:20:47'),
(81, 1, 'Restored category: test', '2025-12-23 15:20:53'),
(82, 1, 'Archived category: test', '2025-12-23 15:28:13'),
(83, 1, 'Restored category: test', '2025-12-23 15:28:19'),
(84, 1, 'Archived category: test', '2025-12-23 15:28:53'),
(85, 1, 'Restored category: test', '2025-12-23 15:28:56'),
(86, 1, 'Archived category: test', '2025-12-23 15:31:11'),
(87, 1, 'Restored category: test', '2025-12-23 15:31:14'),
(88, 1, 'Archived product: test', '2025-12-23 15:35:53'),
(89, 1, 'Restored product: test', '2025-12-23 15:35:56'),
(90, 1, 'Archived category: test', '2025-12-23 15:36:09'),
(91, 1, 'Updated category: Rice', '2025-12-23 15:39:55'),
(92, 1, 'Archived category: Rice', '2025-12-23 15:40:01'),
(93, 1, 'Restored category: Rice', '2025-12-23 15:40:06'),
(94, 1, 'Restored category: test', '2025-12-23 15:40:07'),
(95, 1, 'Archived product: test', '2025-12-23 15:41:22'),
(96, 1, 'Restored product: test', '2025-12-23 15:41:32'),
(97, 1, 'Updated product: tests', '2025-12-23 15:45:56'),
(98, 1, 'Added product: test2', '2025-12-23 15:46:09'),
(99, 1, 'Deleted product: test2', '2025-12-23 15:46:16'),
(100, 1, 'Updated stock: Charity Daayata (Qty: 1, Threshold: 1)', '2025-12-23 15:53:01'),
(101, 1, 'Updated user: cashier', '2025-12-23 15:56:06'),
(102, 1, 'Updated user: cashier', '2025-12-23 15:56:12'),
(103, 1, 'Archived user: cashier', '2025-12-23 15:56:18'),
(104, 1, 'Restored user: cashier', '2025-12-23 15:56:24'),
(105, 11, 'Updated category: Charity', '2025-12-23 15:57:35'),
(106, 11, 'Updated category: Charity', '2025-12-23 15:59:42'),
(107, 11, 'Updated product: BasoO', '2025-12-23 16:02:55'),
(108, 11, 'Updated product: Baso', '2025-12-23 16:05:05'),
(109, 11, 'Added product: test', '2025-12-23 16:05:18'),
(110, 1, 'Updated stock: Charity Daayata (Qty: 2, Threshold: 2)', '2025-12-23 16:19:59'),
(111, 9, 'Updated stock: Charity Daayata (Qty: 2, Threshold: 2)', '2025-12-23 16:20:27'),
(112, 9, 'Updated stock: Charity Daayata (Qty: 2, Threshold: 2)', '2025-12-23 16:20:37'),
(113, 9, 'Updated stock: Charity Daayata (Qty: 4, Threshold: 2)', '2025-12-23 16:20:48'),
(114, 9, 'Updated stock: Princess Beas (Qty: 2, Threshold: 1)', '2025-12-23 16:22:21');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `status`) VALUES
(1, 'Rice', 'bugas ni mama', 'active'),
(3, 'Fruits', 'fruits lang', 'active'),
(4, 'Glasswear', 'mga mabuak\r\n', 'active'),
(9, 'Charity', 'ako lang to', 'active'),
(11, 'test', 'text', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `barcode` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `stock_threshold` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category_id`, `price`, `quantity`, `barcode`, `status`, `stock_threshold`) VALUES
(1, 'Princess Beas', 1, 1500.00, 2, '123asd', 'active', 1),
(3, 'Bayabas', 3, 10.00, 37, 'bayabas123', 'active', 10),
(4, 'Baso', 4, 25.00, 34, 'baso123', 'active', 5),
(9, 'Charity Daayata', 9, 10.00, 4, 'charity123', 'active', 2),
(11, 'tests', 11, 1.00, 12, '121212', 'active', 5),
(13, 'test', 1, 123.00, 23, '123123asdad', 'active', 5);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `requested_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `status` enum('pending','approved','sent','received','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `po_item_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Cashier'),
(3, 'Warehouse Manager'),
(5, 'Inventory Manager'),
(6, 'Inventory Manager');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','gcash','card') DEFAULT 'cash',
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `user_id`, `total_amount`, `payment_method`, `sale_date`) VALUES
(1, 2, 1500.00, 'cash', '2025-12-21 06:27:11'),
(2, 2, 1500.00, 'cash', '2025-12-21 06:47:33'),
(3, 2, 1500.00, 'cash', '2025-12-21 06:50:05'),
(4, 2, 1500.00, 'cash', '2025-12-21 07:40:10'),
(5, 2, 50.00, 'gcash', '2025-12-22 04:50:22'),
(6, 2, 50.00, 'cash', '2025-12-23 08:42:34'),
(7, 2, 10.00, 'cash', '2025-12-23 08:45:38'),
(8, 2, 80.00, 'cash', '2025-12-23 09:07:53'),
(9, 2, 50.00, 'cash', '2025-12-23 15:06:52'),
(10, 2, 75.00, 'gcash', '2025-12-23 15:12:50'),
(11, 2, 105.00, 'gcash', '2025-12-23 15:13:22'),
(12, 2, 20.00, 'gcash', '2025-12-23 16:10:21'),
(13, 2, 25.00, 'gcash', '2025-12-23 16:10:48'),
(14, 2, 25.00, 'cash', '2025-12-23 16:11:19'),
(15, 2, 25.00, 'cash', '2025-12-23 16:14:35'),
(16, 2, 25.00, 'cash', '2025-12-23 16:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `sales_item_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_items`
--

INSERT INTO `sales_items` (`sales_item_id`, `sale_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 1500.00),
(2, 2, 1, 1, 1500.00),
(3, 3, 1, 1, 1500.00),
(4, 4, 1, 1, 1500.00),
(5, 5, 4, 2, 25.00),
(6, 6, 4, 2, 25.00),
(7, 7, 9, 1, 10.00),
(8, 8, 3, 8, 10.00),
(9, 9, 4, 2, 25.00),
(10, 10, 4, 3, 25.00),
(11, 11, 3, 3, 10.00),
(12, 11, 4, 3, 25.00),
(13, 12, 3, 2, 10.00),
(14, 13, 4, 1, 25.00),
(15, 14, 4, 1, 25.00),
(16, 15, 4, 1, 25.00),
(17, 16, 4, 1, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_in`
--

CREATE TABLE `stock_in` (
  `stockin_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_receipts`
--

CREATE TABLE `stock_receipts` (
  `receipt_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `received_by` int(11) NOT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `role_id`, `created_at`, `status`) VALUES
(1, 'System Administrator', 'admin', '$2y$10$nRkGH5dFvyocmTeq8Z9Mp.ugB5q9h1lu883ImlpKUkuuDqd5ozBt.', 1, '2025-12-21 03:08:44', 'active'),
(2, 'Cashier', 'cashier', '$2y$10$.gpusBv/SKZE1R5uMEgoLOr6IlG4QYnF1sE/xtn2wAj.ONzwkqbCC', 2, '2025-12-21 03:08:44', 'active'),
(9, 'Warehouse Manager', 'warehouse', '$2y$10$7O5gC62JSqAyn5Ht0lflkeMMEZabymtc96EPOhA64t/Zz7jeSKvZq', 3, '2025-12-21 15:10:17', 'active'),
(11, 'Inventory Manager', 'inventory', '$2y$10$YtVfwOM18GQ6.Vln1YaJHex6tcVkbFKtPYDnznwxEFsVGm6EZ5Otu', 5, '2025-12-23 06:36:09', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`po_item_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD PRIMARY KEY (`sales_item_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD PRIMARY KEY (`stockin_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `stock_receipts`
--
ALTER TABLE `stock_receipts`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `received_by` (`received_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `po_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `sales_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stock_in`
--
ALTER TABLE `stock_in`
  MODIFY `stockin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_receipts`
--
ALTER TABLE `stock_receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `purchase_orders_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`),
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`),
  ADD CONSTRAINT `sales_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD CONSTRAINT `stock_in_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `stock_in_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `stock_receipts`
--
ALTER TABLE `stock_receipts`
  ADD CONSTRAINT `stock_receipts_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`),
  ADD CONSTRAINT `stock_receipts_ibfk_2` FOREIGN KEY (`received_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
