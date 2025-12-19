-- SQL para aumentar as visualizações (views) dos jogos em destaque
-- Isso fará com que apareçam primeiro na home (ordenado por views DESC)
-- Data: 2025-12-08

-- Atualizar visualizações dos jogos mais populares para aparecerem em destaque
UPDATE `games` SET `views` = 5000 WHERE `uuid` = '51092' AND `name` = 'Fortune Tiger';
UPDATE `games` SET `views` = 4500 WHERE `uuid` = '51105' AND `name` = 'Fortune Rabbit';
UPDATE `games` SET `views` = 4000 WHERE `uuid` = '51065' AND `name` = 'Fortune Ox';
UPDATE `games` SET `views` = 3500 WHERE `uuid` = '51041' AND `name` = 'Fortune Mouse';
UPDATE `games` SET `views` = 3000 WHERE `uuid` = '15000' AND `name` = 'Aviator';
UPDATE `games` SET `views` = 2500 WHERE `uuid` = '15004' AND `name` = 'Mines';
UPDATE `games` SET `views` = 2000 WHERE `uuid` = '51123' AND `name` = 'Dragon Hatch 2';
UPDATE `games` SET `views` = 1500 WHERE `uuid` = '51003' AND `name` = 'Fortune Gods';
UPDATE `games` SET `views` = 1000 WHERE `uuid` = '51004' AND `name` = 'Medusa 2: the Quest of Perseus';
