USE scraplog;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE sales;
TRUNCATE TABLE removals;
TRUNCATE TABLE stock_entries;
SET FOREIGN_KEY_CHECKS = 1;

UPDATE items SET sell_price = 0, buy_price = 0, markup_pct = 25;
