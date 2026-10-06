<?php
function load_categories($pdo){
    $rows = $pdo->query("SELECT c.code, c.label, c.short_name, c.color, c.buy_price, c.sell_price, c.capacity_kg,
            (SELECT COALESCE(SUM(kg), 0) FROM stock_entries WHERE category_id = c.id)
          - (SELECT COALESCE(SUM(kg), 0) FROM sales WHERE category_id = c.id) AS stock
        FROM categories c ORDER BY c.sort_order")->fetchAll();
    return array_map(function($r){
        $stock = max(round((float)$r['stock'], 2), 0);
        $cap = (float)$r['capacity_kg'] > 0 ? min(100, round($stock / (float)$r['capacity_kg'] * 100)) : 0;
        return [
            'code' => $r['code'], 'label' => $r['label'], 'short_name' => $r['short_name'], 'color' => $r['color'],
            'buy' => (float)$r['buy_price'], 'sell' => (float)$r['sell_price'], 'stock' => $stock, 'cap' => $cap,
        ];
    }, $rows);
}

function map_log($r){
    return [
        'type' => $r['type'], 'code' => $r['code'], 'kg' => (float)$r['kg'],
        'amt' => $r['amt'] === null ? null : (float)$r['amt'], 'party' => (string)$r['party'], 't' => $r['t'],
    ];
}

function load_app_data($pdo){
    $union = "SELECT 'in' AS type, c.code, e.kg, NULL AS amt, e.source AS party, e.created_at AS t
              FROM stock_entries e JOIN categories c ON c.id = e.category_id %s
              UNION ALL
              SELECT 'out', c.code, s.kg, s.kg * s.price_per_kg, s.buyer, s.created_at
              FROM sales s JOIN categories c ON c.id = s.category_id %s";

    $log = $pdo->query('SELECT * FROM (' . sprintf($union, 'WHERE e.created_at >= CURDATE() - INTERVAL 366 DAY', 'WHERE s.created_at >= CURDATE() - INTERVAL 366 DAY') . ') x ORDER BY t')->fetchAll();
    $activity = $pdo->query('SELECT * FROM (' . sprintf($union, '', '') . ') x ORDER BY t DESC LIMIT 20')->fetchAll();
    $revenue = $pdo->query('SELECT COALESCE(SUM(kg * price_per_kg), 0) FROM sales')->fetchColumn();

    return [
        'categories' => load_categories($pdo),
        'log' => array_map('map_log', $log),
        'activity' => array_map('map_log', $activity),
        'revenue' => (float)$revenue,
    ];
}
