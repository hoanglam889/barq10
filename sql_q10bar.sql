-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 02, 2026 lúc 08:27 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlbar`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','paid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_details`
--

CREATE TABLE `cart_details` (
  `cart_detail` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `drink_id` int(11) NOT NULL,
  `drink_quantity` int(11) NOT NULL,
  `cart_detail_price` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_phone`, `status`) VALUES
(0, 'Vãng lai', '', 1),
(1, 'Phan Huỳnh Hoàng Lâm', '0797551612', 0),
(5, 'Anh Thư - Bar', '', 1),
(6, 'anh Huỳnh', '', 1),
(8, 'Tuyết Sa', '', 1),
(9, 'Em Trang', '', 1),
(12, 'Chị Phương', '', 1),
(18, 'Lâm Ốc Quế', '', 1),
(21, 'Yến chu che', '', 1),
(22, 'Lâm test', '', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT 0,
  `order_time` datetime DEFAULT current_timestamp(),
  `order_payby` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('indebtedness','paid','cancelled') DEFAULT 'paid',
  `canceled_at` datetime DEFAULT NULL,
  `Order_note` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `order_details_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `drink_id` int(11) NOT NULL,
  `order_detail_quantity` int(11) NOT NULL,
  `order_detail_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `soft_drink`
--

CREATE TABLE `soft_drink` (
  `drink_id` int(11) NOT NULL,
  `drink_name` varchar(100) NOT NULL,
  `drink_quantity` int(11) NOT NULL,
  `drink_cost` float NOT NULL,
  `drink_img` varchar(255) DEFAULT NULL,
  `drink_price` float NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `soft_drink`
--

INSERT INTO `soft_drink` (`drink_id`, `drink_name`, `drink_quantity`, `drink_cost`, `drink_img`, `drink_price`, `status`) VALUES
(1, 'Pepsi', 10, 6666, 'img/pepsi.jpg', 10000, 1),
(2, '7Up lon', 20, 7777, 'img/7up.jpg', 10000, 1),
(3, 'Aquafina chai', 20, 3333, 'img/aquafina.jpg', 5000, 1),
(4, 'Sting dâu lon', 22, 8888, 'img/sting.jpg', 10000, 1),
(5, 'Twister lon', 12, 8888, 'img/stwister.jpg', 12000, 1),
(6, 'Rockstar lon', 12, 9999, 'img/rockstar.jpg', 12000, 1),
(7, '7Up Soda chanh lon', 33, 5555, 'img/soda.jpg', 10000, 1),
(8, 'Pepsi chanh', 33, 6666, 'img/pepsichanh.jpg', 12000, 1),
(9, 'Pepsi phúc bồn tử', 11, 8386, 'img/drink_68cabee763a901.70014140.jpg', 12000, 1),
(14, 'Bia Tiger Crystal lon', 0, 17000, 'img/drink_68cac210c52c19.55465814.jpg', 20000, 0),
(16, 'Bia Tiger Sleek lon', 0, 160001, 'img/drink_69f59d2fc12e56.79072584.jfif', 20000, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` char(100) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` char(10) NOT NULL,
  `user_profile` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_password`, `user_role`, `user_profile`, `status`) VALUES
(2, 'phanhuynhhoanglam', '1', 'admin', 'Phan Huỳnh Hoàng Lâm', 1),
(7, 'admin', 'admin', 'admin', 'Admin Lâm', 1),
(8, 'anhthu', '1', 'admin', 'Nguyễn Ngọc Anh Thư', 1),
(9, 'baotran', '1', 'admin', 'Nguyễn Ngọc Bảo Trân', 1),
(10, 'hoangthai', '1', 'user', 'Trương Hoàng Thái', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_details`
--
ALTER TABLE `cart_details`
  ADD PRIMARY KEY (`cart_detail`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `drink_id` (`drink_id`);

--
-- Chỉ mục cho bảng `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_details_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `drink_id` (`drink_id`);

--
-- Chỉ mục cho bảng `soft_drink`
--
ALTER TABLE `soft_drink`
  ADD PRIMARY KEY (`drink_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=314;

--
-- AUTO_INCREMENT cho bảng `cart_details`
--
ALTER TABLE `cart_details`
  MODIFY `cart_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=352;

--
-- AUTO_INCREMENT cho bảng `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `soft_drink`
--
ALTER TABLE `soft_drink`
  MODIFY `drink_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `cart_details`
--
ALTER TABLE `cart_details`
  ADD CONSTRAINT `cart_details_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`),
  ADD CONSTRAINT `cart_details_ibfk_2` FOREIGN KEY (`drink_id`) REFERENCES `soft_drink` (`drink_id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`drink_id`) REFERENCES `soft_drink` (`drink_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
