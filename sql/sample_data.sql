USE scraplog;

UPDATE items i JOIN categories c ON c.id = i.category_id JOIN (
  SELECT 'bakal' AS code, 'Bakal' AS name, 17 AS sell
  UNION ALL SELECT 'tanso' AS code, 'Tanso' AS name, 310 AS sell
  UNION ALL SELECT 'aluminum' AS code, 'Aluminum' AS name, 70 AS sell
  UNION ALL SELECT 'plastik' AS code, 'PET bottles' AS name, 15 AS sell
  UNION ALL SELECT 'plastik' AS code, 'HDPE containers' AS name, 12.5 AS sell
  UNION ALL SELECT 'plastik' AS code, 'PP / hard plastic' AS name, 10 AS sell
  UNION ALL SELECT 'plastik' AS code, 'Soft plastic / sando' AS name, 5 AS sell
  UNION ALL SELECT 'plastik' AS code, 'Other' AS name, 5 AS sell
  UNION ALL SELECT 'karton' AS code, 'Corrugated cardboard' AS name, 6.25 AS sell
  UNION ALL SELECT 'karton' AS code, 'Newspaper (Dyaryo)' AS name, 7.5 AS sell
  UNION ALL SELECT 'karton' AS code, 'White paper' AS name, 11.25 AS sell
  UNION ALL SELECT 'karton' AS code, 'Mixed paper' AS name, 5 AS sell
  UNION ALL SELECT 'karton' AS code, 'Other' AS name, 3.75 AS sell
  UNION ALL SELECT 'bote' AS code, 'Clear bottles' AS name, 2.5 AS sell
  UNION ALL SELECT 'bote' AS code, 'Brown / beer bottles' AS name, 2.5 AS sell
  UNION ALL SELECT 'bote' AS code, 'Green bottles' AS name, 1.25 AS sell
  UNION ALL SELECT 'bote' AS code, 'Jars & containers' AS name, 1.25 AS sell
  UNION ALL SELECT 'bote' AS code, 'Other' AS name, 1.25 AS sell
  UNION ALL SELECT 'ewaste' AS code, 'Circuit boards' AS name, 125 AS sell
  UNION ALL SELECT 'ewaste' AS code, 'Cellphones & tablets' AS name, 62.5 AS sell
  UNION ALL SELECT 'ewaste' AS code, 'Computers & laptops' AS name, 25 AS sell
  UNION ALL SELECT 'ewaste' AS code, 'Cables & chargers' AS name, 37.5 AS sell
  UNION ALL SELECT 'ewaste' AS code, 'Small appliances' AS name, 10 AS sell
  UNION ALL SELECT 'ewaste' AS code, 'Other' AS name, 10 AS sell
) p ON p.code = c.code AND p.name = i.name
SET i.sell_price = p.sell, i.markup_pct = 25, i.buy_price = ROUND(p.sell / 1.25, 2);

INSERT INTO stock_entries (category_id, item_id, kg, source, detail, user_id, created_at)
SELECT i.category_id, i.id, v.kg, v.source, v.detail, (SELECT id FROM users WHERE username = 'staff'), v.t
FROM items i JOIN categories c ON c.id = i.category_id JOIN (
  SELECT 'bakal' AS code, 'Bakal' AS name, 150 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 25 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 'Bakal' AS name, 120 AS kg, 'Aling Nena' AS source, '' AS detail, NOW() - INTERVAL 18 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 'Bakal' AS name, 95 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 12 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 'Bakal' AS name, 60 AS kg, 'Junk buyer route' AS source, '' AS detail, NOW() - INTERVAL 6 DAY AS t
  UNION ALL SELECT 'bakal' AS code, 'Bakal' AS name, 22 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 5 HOUR AS t
  UNION ALL SELECT 'tanso' AS code, 'Tanso' AS name, 12 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 20 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 'Tanso' AS name, 9 AS kg, 'Aling Nena' AS source, '' AS detail, NOW() - INTERVAL 11 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 'Tanso' AS name, 4.6 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 4 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 'Tanso' AS name, 3.4 AS kg, 'Aling Nena' AS source, '' AS detail, NOW() - INTERVAL 4 HOUR AS t
  UNION ALL SELECT 'aluminum' AS code, 'Aluminum' AS name, 30 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 21 DAY AS t
  UNION ALL SELECT 'aluminum' AS code, 'Aluminum' AS name, 18 AS kg, 'Junk buyer route' AS source, '' AS detail, NOW() - INTERVAL 9 DAY AS t
  UNION ALL SELECT 'aluminum' AS code, 'Aluminum' AS name, 10 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 2 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'PET bottles' AS name, 80 AS kg, 'Junk buyer route' AS source, '' AS detail, NOW() - INTERVAL 22 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'PET bottles' AS name, 60 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 14 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'HDPE containers' AS name, 45 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 14 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'PP / hard plastic' AS name, 50 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 7 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'Soft plastic / sando' AS name, 30 AS kg, 'Junk buyer route' AS source, '' AS detail, NOW() - INTERVAL 7 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'Other' AS name, 18.2 AS kg, 'Walk-in' AS source, 'Plastic chairs' AS detail, NOW() - INTERVAL 3 HOUR AS t
  UNION ALL SELECT 'karton' AS code, 'Corrugated cardboard' AS name, 160 AS kg, 'Junk buyer route' AS source, '' AS detail, NOW() - INTERVAL 24 DAY AS t
  UNION ALL SELECT 'karton' AS code, 'Corrugated cardboard' AS name, 40 AS kg, 'Junk buyer route' AS source, '' AS detail, NOW() - INTERVAL 6 HOUR AS t
  UNION ALL SELECT 'karton' AS code, 'Newspaper (Dyaryo)' AS name, 70 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 16 DAY AS t
  UNION ALL SELECT 'karton' AS code, 'White paper' AS name, 60 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 16 DAY AS t
  UNION ALL SELECT 'karton' AS code, 'Mixed paper' AS name, 100 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 8 DAY AS t
  UNION ALL SELECT 'bote' AS code, 'Clear bottles' AS name, 60 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 19 DAY AS t
  UNION ALL SELECT 'bote' AS code, 'Brown / beer bottles' AS name, 45 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 10 DAY AS t
  UNION ALL SELECT 'bote' AS code, 'Green bottles' AS name, 20 AS kg, 'Aling Nena' AS source, '' AS detail, NOW() - INTERVAL 3 DAY AS t
  UNION ALL SELECT 'bote' AS code, 'Other' AS name, 8 AS kg, 'Walk-in' AS source, 'Perfume bottles' AS detail, NOW() - INTERVAL 1 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 'Circuit boards' AS name, 10 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 23 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 'Cellphones & tablets' AS name, 12 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 13 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 'Computers & laptops' AS name, 15 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 5 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 'Cables & chargers' AS name, 12 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 23 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 'Small appliances' AS name, 6 AS kg, 'Walk-in' AS source, '' AS detail, NOW() - INTERVAL 1 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 'Other' AS name, 7 AS kg, 'Walk-in' AS source, 'Remote controls' AS detail, NOW() - INTERVAL 13 DAY AS t
) v ON v.code = c.code AND v.name = i.name;

INSERT INTO sales (category_id, item_id, kg, price_per_kg, buyer, user_id, created_at)
SELECT i.category_id, i.id, v.kg, i.sell_price, v.buyer, (SELECT id FROM users WHERE username = 'staff'), v.t
FROM items i JOIN categories c ON c.id = i.category_id JOIN (
  SELECT 'bakal' AS code, 'Bakal' AS name, 35 AS kg, 'Metro Steel Co.' AS buyer, NOW() - INTERVAL 10 DAY AS t
  UNION ALL SELECT 'tanso' AS code, 'Tanso' AS name, 1 AS kg, 'Copper Trader' AS buyer, NOW() - INTERVAL 3 DAY AS t
  UNION ALL SELECT 'aluminum' AS code, 'Aluminum' AS name, 4 AS kg, 'Metro Steel Co.' AS buyer, NOW() - INTERVAL 5 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'PET bottles' AS name, 40 AS kg, 'City Recyclers' AS buyer, NOW() - INTERVAL 8 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'Other' AS name, 8 AS kg, 'City Recyclers' AS buyer, NOW() - INTERVAL 2 HOUR AS t
  UNION ALL SELECT 'karton' AS code, 'Corrugated cardboard' AS name, 50 AS kg, 'PaperMill Inc.' AS buyer, NOW() - INTERVAL 6 DAY AS t
  UNION ALL SELECT 'karton' AS code, 'Newspaper (Dyaryo)' AS name, 20 AS kg, 'PaperMill Inc.' AS buyer, NOW() - INTERVAL 3 DAY AS t
  UNION ALL SELECT 'bote' AS code, 'Clear bottles' AS name, 29 AS kg, 'Glassworks' AS buyer, NOW() - INTERVAL 4 DAY AS t
  UNION ALL SELECT 'ewaste' AS code, 'Circuit boards' AS name, 3 AS kg, 'E-Cycle PH' AS buyer, NOW() - INTERVAL 2 DAY AS t
) v ON v.code = c.code AND v.name = i.name;

INSERT INTO removals (category_id, item_id, kg, reason, note, price_per_kg, user_id, created_at)
SELECT i.category_id, i.id, v.kg, v.reason, v.note, i.buy_price, (SELECT id FROM users WHERE username = 'staff'), v.t
FROM items i JOIN categories c ON c.id = i.category_id JOIN (
  SELECT 'bakal' AS code, 'Bakal' AS name, 3 AS kg, 'Rusted / corroded' AS reason, '' AS note, NOW() - INTERVAL 4 DAY AS t
  UNION ALL SELECT 'plastik' AS code, 'Soft plastic / sando' AS name, 5 AS kg, 'Wet / contaminated' AS reason, 'Rain-soaked bag' AS note, NOW() - INTERVAL 6 DAY AS t
  UNION ALL SELECT 'bote' AS code, 'Green bottles' AS name, 2 AS kg, 'Broken / damaged' AS reason, '' AS note, NOW() - INTERVAL 2 DAY AS t
) v ON v.code = c.code AND v.name = i.name;
