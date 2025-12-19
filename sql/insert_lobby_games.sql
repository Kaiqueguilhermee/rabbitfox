-- SQL para inserir jogos no lobby
-- Tabela: games
-- Data: 2025-12-08

INSERT INTO `games` (`category_id`, `name`, `uuid`, `image`, `type`, `provider`, `provider_service`, `technology`, `has_lobby`, `is_mobile`, `has_freespins`, `has_tables`, `slug`, `active`, `views`, `created_at`, `updated_at`) VALUES
-- Fortune Tiger (game_id: 51092)
(1, 'Fortune Tiger', '51092', 'https://gator.drakon.casino/storage/drakon/Fortune-Tiger.webp', 'slots', 'pgsoft', 'drakon', 'html5', 1, 0, 0, 0, 'fortune-tiger', 1, 0, NOW(), NOW()),

-- Fortune Ox (game_id: 51065)
(1, 'Fortune Ox', '51065', 'https://gator.drakon.casino/storage/drakon/Fortune-Ox.webp', 'slots', 'pgsoft', 'drakon', 'html5', 1, 0, 0, 0, 'fortune-ox', 1, 0, NOW(), NOW()),

-- Fortune Mouse (game_id: 51041)
(1, 'Fortune Mouse', '51041', 'https://gator.drakon.casino/storage/drakon/Fortune-Mouse.webp', 'slots', 'pgsoft', 'drakon', 'html5', 1, 0, 0, 0, 'fortune-mouse', 1, 0, NOW(), NOW()),

-- Fortune Rabbit (game_id: 51105)
(1, 'Fortune Rabbit', '51105', 'https://gator.drakon.casino/storage/drakon/Fortune-Rabbit.webp', 'slots', 'pgsoft', 'drakon', 'html5', 1, 0, 0, 0, 'fortune-rabbit', 1, 0, NOW(), NOW()),

-- Dragon Hatch 2 (game_id: 51123)
(1, 'Dragon Hatch 2', '51123', 'https://gator.drakon.casino/storage/drakon/Dragon-Hatch-2.webp', 'slots', 'pgsoft', 'drakon', 'html5', 1, 0, 0, 0, 'dragon-hatch-2', 1, 0, NOW(), NOW()),

-- Aviator (game_id: 15000)
(1, 'Aviator', '15000', 'https://gator.drakon.casino/storage/drakon/Aviator.webp', 'crashgame', 'aviator', 'drakon', 'html5', 1, 0, 0, 0, 'aviator', 1, 0, NOW(), NOW()),

-- Mines (game_id: 15004)
(1, 'Mines', '15004', 'https://gator.drakon.casino/storage/drakon/Mines.webp', 'interactivegame', 'spribe', 'drakon', 'html5', 1, 0, 0, 0, 'mines', 1, 0, NOW(), NOW()),

-- Fortune Gods (game_id: 51003)
(1, 'Fortune Gods', '51003', 'https://gator.drakon.casino/storage/drakon/Fortune-Gods.webp', 'slots', 'pgsoft', 'drakon', 'html5', 1, 0, 0, 0, 'fortune-gods', 1, 0, NOW(), NOW()),

-- Medusa 2: the Quest of Perseus (game_id: 51004)
(1, 'Medusa 2: the Quest of Perseus', '51004', 'https://gator.drakon.casino/storage/drakon/Medusa-2-the-Quest-of-Perseus.webp', 'slots', 'pgsoft', 'drakon', 'html5', 1, 0, 0, 0, 'medusa-2-the-quest-of-perseus', 1, 0, NOW(), NOW());
