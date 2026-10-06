USE scraplog;
TRUNCATE TABLE sales;
TRUNCATE TABLE stock_entries;
UPDATE categories SET buy_price = 0, sell_price = 0;
