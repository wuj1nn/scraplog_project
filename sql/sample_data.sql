USE scraplog;

UPDATE categories SET buy_price = 14.50, sell_price = 17.00 WHERE code = 'bakal';
UPDATE categories SET buy_price = 285.00, sell_price = 310.00 WHERE code = 'tanso';
UPDATE categories SET buy_price = 62.00, sell_price = 70.00 WHERE code = 'aluminum';
UPDATE categories SET buy_price = 9.00, sell_price = 11.00 WHERE code = 'plastik';
UPDATE categories SET buy_price = 6.50, sell_price = 8.00 WHERE code = 'karton';
UPDATE categories SET buy_price = 3.00, sell_price = 4.00 WHERE code = 'bote';
UPDATE categories SET buy_price = 38.00, sell_price = 45.00 WHERE code = 'ewaste';

INSERT INTO stock_entries (category_id, kg, source, user_id, created_at)
SELECT c.id, v.kg, v.source, (SELECT id FROM users WHERE username = 'staff'), v.t
FROM categories c JOIN (
  SELECT 'bakal' AS code, 150 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 25 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 120 AS kg, 'Aling Nena' AS source, NOW() - INTERVAL 18 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 95 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 12 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 60 AS kg, 'Junk buyer route' AS source, NOW() - INTERVAL 6 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 22 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 5 HOUR AS t
  UNION ALL SELECT 'tanso' AS code, 12 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 20 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 9 AS kg, 'Aling Nena' AS source, NOW() - INTERVAL 11 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 4.6 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 4 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 3.4 AS kg, 'Aling Nena' AS source, NOW() - INTERVAL 4 HOUR AS t
  UNION ALL SELECT 'aluminum' AS code, 30 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 21 DAY AS t
  UNION ALL SELECT 'aluminum' AS code, 18 AS kg, 'Junk buyer route' AS source, NOW() - INTERVAL 9 DAY AS t
  UNION ALL SELECT 'aluminum' AS code, 10 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 2 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 140 AS kg, 'Junk buyer route' AS source, NOW() - INTERVAL 22 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 110 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 14 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 95 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 7 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 18.2 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 3 HOUR AS t
  UNION ALL SELECT 'karton' AS code, 160 AS kg, 'Junk buyer route' AS source, NOW() - INTERVAL 24 DAY AS t
  UNION ALL SELECT 'karton' AS code, 130 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 16 DAY AS t
  UNION ALL SELECT 'karton' AS code, 100 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 8 DAY AS t
  UNION ALL SELECT 'karton' AS code, 40 AS kg, 'Junk buyer route' AS source, NOW() - INTERVAL 6 HOUR AS t
  UNION ALL SELECT 'bote' AS code, 60 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 19 DAY AS t
  UNION ALL SELECT 'bote' AS code, 45 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 10 DAY AS t
  UNION ALL SELECT 'bote' AS code, 20 AS kg, 'Aling Nena' AS source, NOW() - INTERVAL 3 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 30 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 23 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 22 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 13 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 15 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 5 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 6 AS kg, 'Walk-in' AS source, NOW() - INTERVAL 1 DAY AS t
) v ON v.code = c.code;

INSERT INTO sales (category_id, kg, price_per_kg, buyer, user_id, created_at)
SELECT c.id, v.kg, v.price, v.buyer, (SELECT id FROM users WHERE username = 'staff'), v.t
FROM categories c JOIN (
  SELECT 'bakal' AS code, 35 AS kg, 17 AS price, 'Metro Steel Co.' AS buyer, NOW() - INTERVAL 10 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 1 AS kg, 310 AS price, 'Copper Trader' AS buyer, NOW() - INTERVAL 3 DAY AS t
  UNION ALL SELECT 'aluminum' AS code, 4 AS kg, 70 AS price, 'Metro Steel Co.' AS buyer, NOW() - INTERVAL 5 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 40 AS kg, 11 AS price, 'City Recyclers' AS buyer, NOW() - INTERVAL 8 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 18.2 AS kg, 11 AS price, 'City Recyclers' AS buyer, NOW() - INTERVAL 2 HOUR AS t
  UNION ALL SELECT 'karton' AS code, 50 AS kg, 8 AS price, 'PaperMill Inc.' AS buyer, NOW() - INTERVAL 6 DAY AS t
  UNION ALL SELECT 'bote' AS code, 29 AS kg, 4 AS price, 'Glassworks' AS buyer, NOW() - INTERVAL 4 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 10 AS kg, 45 AS price, 'E-Cycle PH' AS buyer, NOW() - INTERVAL 2 DAY AS t
) v ON v.code = c.code;
