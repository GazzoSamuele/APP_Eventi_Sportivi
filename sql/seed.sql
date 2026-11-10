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

INSERT INTO `broadcasters` (`id`, `name`, `type`, `website_url`) VALUES
(1, 'Sky Sport', 'pay_tv', NULL),
(2, 'NOW', 'streaming', NULL),
(3, 'TV8', 'free_tv', NULL),
(4, 'DAZN', 'streaming', NULL),
(5, 'SuperTennis', 'free_tv', NULL),
(6, 'Rai Sport', 'free_tv', NULL);

INSERT INTO `competition_broadcaster` (`competition_id`, `broadcaster_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(4, 1),
(4, 2),
(4, 3),
(7, 4),
(8, 4),
(9, 4),
(11, 1),
(11, 2),
(13, 1),
(13, 5),
(14, 1),
(14, 6),
(17, 4);

INSERT INTO `events` (`id`, `competition_id`, `title`, `location`, `start_utc`, `end_utc`, `status`, `natural_key`) VALUES
(1, 1, 'GP d''Australia', 'Melbourne', '2027-03-14 04:00:00', NULL, 'confirmed', 'formula-1|2027-03-14|gp-australia'),
(2, 1, 'GP di Monaco', 'Monte Carlo', '2027-06-06 13:00:00', NULL, 'confirmed', 'formula-1|2027-06-06|gp-monaco'),
(3, 1, 'GP d''Italia', 'Monza', '2027-09-05 13:00:00', NULL, 'confirmed', 'formula-1|2027-09-05|gp-italia'),
(4, 4, 'GP d''Italia', 'Mugello', '2027-05-30 12:00:00', NULL, 'confirmed', 'motogp|2027-05-30|gp-italia'),
(5, 4, 'GP di Valencia', 'Valencia', '2027-11-14 13:00:00', NULL, 'confirmed', 'motogp|2027-11-14|gp-valencia'),
(6, 7, '24 Ore di Le Mans', 'Le Mans', '2027-06-12 14:00:00', '2027-06-13 14:00:00', 'confirmed', 'wec|2027-06-12|24-ore-le-mans'),
(7, 8, 'Rally Italia Sardegna', 'Olbia', '2027-06-03 07:00:00', '2027-06-06 13:00:00', 'confirmed', 'wrc|2027-06-03|rally-italia-sardegna'),
(8, 9, 'Australian Open', 'Melbourne', '2027-01-18 00:00:00', '2027-01-31 11:00:00', 'confirmed', 'australian-open|2027-01-18|australian-open'),
(9, 13, 'Internazionali d''Italia', 'Roma', '2027-05-05 09:00:00', '2027-05-16 16:00:00', 'to_verify', 'atp-masters-1000|2027-05-05|internazionali-italia'),
(10, 11, 'Wimbledon', 'Londra', '2027-06-28 10:00:00', '2027-07-11 16:00:00', 'confirmed', 'wimbledon|2027-06-28|wimbledon'),
(11, 14, 'ATP Finals', 'Torino', '2027-11-14 11:00:00', '2027-11-21 18:00:00', 'confirmed', 'atp-finals|2027-11-14|atp-finals'),
(12, 17, 'Italy Major', 'Roma', '2027-06-14 08:00:00', '2027-06-20 20:00:00', 'to_verify', 'major|2027-06-14|italy-major');

INSERT INTO `event_broadcaster` (`event_id`, `broadcaster_id`) VALUES
(7, 6);
