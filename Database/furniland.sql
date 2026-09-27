SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `furniland` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `furniland`;

CREATE TABLE `cart` (
  `cartID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `productID` int(10) NOT NULL,
  `quantity` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cart` (`cartID`, `userID`, `productID`, `quantity`) VALUES
(7, 3, 4, 1);

CREATE TABLE `products` (
  `productID` int(10) NOT NULL,
  `productName` varchar(30) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `vendorID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`productID`, `productName`, `description`, `price`, `image`, `vendorID`) VALUES
(1, 'Felix Accent Armchair', 'Mid-century armchair with soft velvet fabric and wooden legs', 1950000, 'felix-chair.jpg', 1),
(2, 'Kyra Dining Set (4 Chairs)', 'Simple wood table with cushioned chairs, perfect for small spaces', 3790000, 'kyra-dining.jpg', 2),
(3, 'Zeno Floating Wall Shelf', 'Wall-mounted shelf made of wood with modern design', 489000, 'zeno-shelf.jpg', 3),
(4, 'Chao Study Table + Drawer', 'Sleek desk with tapered legs 100 drawer unit and minimalist style', 1675000, 'chao-desk.jpg', 1),
(5, 'Verra Minimalist Coffee Table', 'Square table with tempered glass top and metal frame', 1150000, 'verra-table.jpg', 4),
(6, 'Nova Mirror Cabinet', 'Multipurpose cabinet with soft-close mirror doors', 1780000, 'nova-cabin.jpg', 2),
(7, 'Velvet L-Shape Sofa', 'Premium grey velvet sofa with extra cushioning and modern wooden legs', 5499000, 'sofa.png', 1),
(8, 'Crystal Palace Chandelier', 'Elegant gold-plated chandelier with hanging crystals for a luxury ambiance', 2850000, 'chandelier.jpg', 4);

CREATE TABLE `transactions` (
  `transactionID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `totalPrice` float DEFAULT NULL,
  `transactionDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transactions` (`transactionID`, `userID`, `totalPrice`, `transactionDate`) VALUES
(2, 3, 10330000, '2025-12-17'),
(3, 3, 7449000, '2025-12-17');

CREATE TABLE `transaction_details` (
  `detailID` int(10) NOT NULL,
  `transactionID` int(10) NOT NULL,
  `productID` int(10) NOT NULL,
  `quantity` int(5) DEFAULT NULL,
  `subtotal` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transaction_details` (`detailID`, `transactionID`, `productID`, `quantity`, `subtotal`) VALUES
(2, 2, 8, 3, 8550000),
(3, 2, 6, 1, 1780000),
(4, 3, 7, 1, 5499000),
(5, 3, 1, 1, 1950000);

CREATE TABLE `users` (
  `userID` int(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `dob` date NOT NULL,
  `role` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`userID`, `username`, `email`, `password`, `gender`, `dob`, `role`) VALUES
(1, 'LS015', 'admin@gmail.com', '$2y$10$rEcLbYGaqZjdDYggVzxj2OaNtHKyu1eD4mOAPA1fUw/as5nc13ZSi', 'male', '1990-01-01', 'admin'),
(2, 'shan', 'shan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female', '1995-05-15', 'member'),
(3, 'Vanessa', 'vanessa@gmail.com', '$2y$10$f2jZxwadsG0Ozm5EhRvfNOj8MUGPHqrnPinWJVYRyM6zCyNWFjuJa', 'female', '2007-01-01', 'member');

CREATE TABLE `vendors` (
  `vendorID` int(10) NOT NULL,
  `vendorName` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`vendorID`, `vendorName`, `location`) VALUES
(1, 'Nordico', 'Jakarta Selatan'),
(2, 'IKEA', 'Jakarta Pusat'),
(3, 'Informa', 'Bekasi'),
(4, 'Atria', 'Tangerang');


ALTER TABLE `cart`
  ADD PRIMARY KEY (`cartID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `productID` (`productID`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `vendorID` (`vendorID`);

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionID`),
  ADD KEY `userID` (`userID`);

ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`detailID`),
  ADD KEY `transactionID` (`transactionID`),
  ADD KEY `productID` (`productID`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendorID`);


ALTER TABLE `cart`
  MODIFY `cartID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `products`
  MODIFY `productID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `transactions`
  MODIFY `transactionID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `transaction_details`
  MODIFY `detailID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `users`
  MODIFY `userID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `vendors`
  MODIFY `vendorID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`vendorID`) REFERENCES `vendors` (`vendorID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transactionID`) REFERENCES `transactions` (`transactionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
