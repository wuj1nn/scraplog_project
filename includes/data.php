<?php
function removal_reasons(){
    return ['Rusted / corroded', 'Broken / damaged', 'Wet / contaminated', 'Moldy / rotten', 'Mis-sorted (wrong material)', 'Rejected by buyer', 'Weighing / count correction', 'Other'];
}

function item_label($short, $name){
    return $name === $short ? $short : $short . ' – ' . $name;
}

function mat_cell($color, $short, $item, $detail = ''){
    $sub = $item === $short ? '' : ' · ' . ($detail !== '' ? $item . ': ' . $detail : $item);
    return '<span class="catChip"><i style="background:' . e($color) . '"></i>' . e($short) . ($sub === '' ? '' : '<span class="catSub">' . e($sub) . '</span>') . '</span>';
}

function item_stock($pdo, $id){
    $s = $pdo->prepare('SELECT (SELECT COALESCE(SUM(kg), 0) FROM stock_entries WHERE item_id = ?)
        - (SELECT COALESCE(SUM(kg), 0) FROM sales WHERE item_id = ?)
        - (SELECT COALESCE(SUM(kg), 0) FROM removals WHERE item_id = ?)');
    $s->execute([$id, $id, $id]);
    return round((float)$s->fetchColumn(), 2);
}

function load_categories($pdo){
    $cats = $pdo->query('SELECT id, code, label, short_name, color, capacity_kg FROM categories ORDER BY sort_order')->fetchAll();
    $rows = $pdo->query("SELECT i.id, i.category_id, i.name, i.is_other, i.sell_price, i.markup_pct, i.buy_price,
            (SELECT COALESCE(SUM(kg), 0) FROM stock_entries WHERE item_id = i.id)
          - (SELECT COALESCE(SUM(kg), 0) FROM sales WHERE item_id = i.id)
          - (SELECT COALESCE(SUM(kg), 0) FROM removals WHERE item_id = i.id) AS stock
        FROM items i ORDER BY i.sort_order, i.id")->fetchAll();
    $by = [];
    foreach($rows as $r){
        $by[$r['category_id']][] = [
            'id' => (int)$r['id'], 'name' => $r['name'], 'other' => (int)$r['is_other'],
            'sell' => (float)$r['sell_price'], 'buy' => (float)$r['buy_price'], 'markup' => (float)$r['markup_pct'],
            'stock' => max(round((float)$r['stock'], 2), 0),
        ];
    }
    return array_map(function($c) use ($by){
        $list = array_map(function($i) use ($c){
            $i['cat'] = $c['code'];
            return $i;
        }, $by[$c['id']] ?? []);
        $stock = array_sum(array_column($list, 'stock'));
        $value = array_sum(array_map(function($i){ return $i['stock'] * $i['buy']; }, $list));
        $proj = array_sum(array_map(function($i){ return $i['stock'] * $i['sell']; }, $list));
        $cap = (float)$c['capacity_kg'] > 0 ? min(100, round($stock / (float)$c['capacity_kg'] * 100)) : 0;
        return [
            'code' => $c['code'], 'label' => $c['label'], 'short_name' => $c['short_name'], 'color' => $c['color'],
            'stock' => round($stock, 2), 'cap' => $cap, 'value' => round($value, 2), 'proj' => round($proj, 2), 'items' => $list,
        ];
    }, $cats);
}

function map_log($r){
    return [
        'type' => $r['type'], 'code' => $r['code'], 'item' => (int)$r['item'], 'kg' => (float)$r['kg'],
        'amt' => $r['amt'] === null ? null : (float)$r['amt'], 'party' => (string)$r['party'],
        'detail' => (string)$r['detail'], 't' => $r['t'],
    ];
}

function load_app_data($pdo, $role = 'admin'){
    $cats = load_categories($pdo);
    $items = [];
    foreach($cats as $c){
        foreach($c['items'] as $i) $items[] = $i;
    }

    if($role === 'customer'){
        return [
            'categories' => array_map(function($c){
                return ['code' => $c['code'], 'label' => $c['label'], 'short_name' => $c['short_name'], 'color' => $c['color'], 'stock' => 0, 'cap' => 0, 'value' => 0, 'proj' => 0];
            }, $cats),
            'items' => array_map(function($i){
                return ['id' => $i['id'], 'cat' => $i['cat'], 'name' => $i['name'], 'other' => $i['other'], 'sell' => $i['sell'], 'buy' => $i['buy'], 'stock' => $i['stock'] > 0 ? 1 : 0];
            }, $items),
            'log' => [], 'activity' => [], 'revenue' => 0,
        ];
    }

    $union = "SELECT 'in' AS type, c.code, e.item_id AS item, e.kg, NULL AS amt, e.source AS party, e.detail, e.created_at AS t
              FROM stock_entries e JOIN categories c ON c.id = e.category_id %s
              UNION ALL
              SELECT 'out', c.code, s.item_id, s.kg, s.kg * s.price_per_kg, s.buyer, '', s.created_at
              FROM sales s JOIN categories c ON c.id = s.category_id %s
              UNION ALL
              SELECT 'rm', c.code, r.item_id, r.kg, r.kg * r.price_per_kg, r.reason, r.note, r.created_at
              FROM removals r JOIN categories c ON c.id = r.category_id %s";

    $log = $pdo->query('SELECT * FROM (' . sprintf($union, 'WHERE e.created_at >= CURDATE() - INTERVAL 366 DAY', 'WHERE s.created_at >= CURDATE() - INTERVAL 366 DAY', 'WHERE r.created_at >= CURDATE() - INTERVAL 366 DAY') . ') x ORDER BY t')->fetchAll();
    $activity = $pdo->query('SELECT * FROM (' . sprintf($union, '', '', '') . ') x ORDER BY t DESC LIMIT 20')->fetchAll();
    $revenue = $pdo->query('SELECT COALESCE(SUM(kg * price_per_kg), 0) FROM sales')->fetchColumn();

    return [
        'categories' => array_map(function($c){ unset($c['items']); return $c; }, $cats),
        'items' => array_map(function($i){ unset($i['markup']); return $i; }, $items),
        'log' => array_map('map_log', $log),
        'activity' => array_map('map_log', $activity),
        'revenue' => (float)$revenue,
    ];
}
