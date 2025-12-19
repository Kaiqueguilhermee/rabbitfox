-- SQL para mover jogos Drakon para TopTrend Gaming
-- Isso fará os jogos aparecerem na seção "TopTrend Gaming" da home
-- Data: 2025-12-08

-- Opção 1: Alterar o provider dos jogos do Drakon para TOPTREND
-- Isso moverá os jogos da seção "Todos os Jogos" para "TopTrend Gaming"

UPDATE `games` SET `provider` = 'TOPTREND' WHERE `uuid` IN ('51092', '51065', '51041', '51105', '51123', '15000', '15004', '51003', '51004');

-- Nota: Após executar, os jogos aparecerão na seção "TopTrend Gaming" da home
-- Se quiser reverter, execute:
-- UPDATE `games` SET `provider` = 'pgsoft' WHERE `uuid` IN ('51092', '51065', '51041', '51105', '51123', '51003', '51004');
-- UPDATE `games` SET `provider` = 'aviator' WHERE `uuid` = '15000';
-- UPDATE `games` SET `provider` = 'spribe' WHERE `uuid` = '15004';
