INSERT INTO `sport` (`id`, `name`, `description`, `image_path`) VALUES
(1, 'Motorsport', NULL, NULL),
(2, 'Tennis', NULL, NULL),
(3, 'Padel', NULL, NULL);

INSERT INTO `categories` (`id`, `name`, `sport_id`, `image_path`) VALUES
(1, 'Formula', 1, NULL),
(2, 'Moto', 1, NULL),
(3, 'Endurance', 1, NULL),
(4, 'Rally', 1, NULL),
(5, 'Grande Slam', 2, NULL),
(6, 'Circuito ATP', 2, NULL),
(7, 'Circuito WTA', 2, NULL),
(8, 'Premier Padel', 3, NULL),
(9, 'Nazionali', 3, NULL);

INSERT INTO `competitions` (`id`, `category_id`, `name`, `image_path`) VALUES
(1, 1, 'Formula 1', NULL),
(2, 1, 'Formula 2', NULL),
(3, 1, 'Formula E', NULL),
(4, 2, 'MotoGP', NULL),
(5, 2, 'Moto2', NULL),
(6, 2, 'WorldSBK', NULL),
(7, 3, 'WEC', NULL),
(8, 4, 'WRC', NULL),
(9, 5, 'Australian Open', NULL),
(10, 5, 'Roland Garros', NULL),
(11, 5, 'Wimbledon', NULL),
(12, 5, 'US Open', NULL),
(13, 6, 'ATP Masters 1000', NULL),
(14, 6, 'ATP Finals', NULL),
(15, 7, 'WTA 1000', NULL),
(16, 7, 'WTA Finals', NULL),
(17, 8, 'Major', NULL),
(18, 8, 'P1', NULL),
(19, 8, 'P2', NULL),
(20, 9, 'Campionato Italiano', NULL);