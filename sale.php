<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'sale';
require_page($page);
$pageScripts = ['sale.js'];
include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/data.php';
$cats = load_categories($pdo);
$rows = $pdo->query("SELECT s.kg, s.price_per_kg, s.buyer, s.created_at, c.short_name, c.color, i.name AS item
    FROM sales s JOIN categories c ON c.id = s.category_id JOIN items i ON i.id = s.item_id
    ORDER BY s.created_at DESC, s.id DESC LIMIT 20")->fetchAll();
?>
<section class="panel active" id="panel-sale">
  <div class="split">
    <div class="card">
      <div class="cardHead"><div><h2>Record a sale</h2><div class="cardDesc">Pick the material and quantity sold — stock and revenue update together</div></div></div>
      <form method="post" action="actions/sale_save.php">
        <?= csrf_field() ?>
        <div class="formGrid">
          <div><label for="saleItem">Material</label><select id="saleItem" name="item" required>
<?php $any = false; foreach($cats as $c): $list = array_filter($c['items'], function($i){ return $i['stock'] > 0; }); if(!$list) continue; $any = true; ?>
            <optgroup label="<?= e($c['label']) ?>">
<?php foreach($list as $i): ?>
              <option value="<?= $i['id'] ?>"><?= e($i['other'] ? 'Other' : $i['name']) ?> — <?= $i['stock'] ?> kg on hand</option>
<?php endforeach; ?>
            </optgroup>
<?php endforeach; if(!$any): ?>
            <option value="">Nothing in storage yet</option>
<?php endif; ?>
          </select></div>
          <div><label for="saleKg">Quantity (kg)</label><input type="number" id="saleKg" name="kg" step="0.01" min="0.01" placeholder="0.0" required></div>
          <div><label for="saleBuyer">Sold to (optional)</label><input type="text" id="saleBuyer" name="buyer" maxlength="100" placeholder="e.g. City Recyclers Inc."></div>
          <div><label>Remaining stock after sale</label><input type="text" id="saleRemaining" value="—" disabled></div>
        </div>
        <div class="previewBox"><span>Revenue for this sale</span><span class="amt" id="salePreview">₱0.00</span></div>
        <button class="btnCta" id="saleBtn" type="submit" style="border-radius:8px;padding:11px 22px;">Confirm sale</button>
      </form>
    </div>

    <div class="card">
      <div class="cardHead"><div><h2>Price preview</h2><div class="cardDesc">Selling price per kilo of everything in storage — tap a row to pick it</div></div></div>
      <div class="tableWrap scrollBox lgScroll"><table class="lgTable">
        <thead><tr><th>Material</th><th class="num">Sell / kg</th><th class="num">On hand</th></tr></thead>
        <tbody id="saleLegendBody"></tbody>
      </table></div>
    </div>
  </div>

  <div class="card">
    <div class="cardHead"><h2>Recent sales</h2></div>
    <div class="tableWrap scrollBox"><table>
      <thead><tr><th>Time</th><th>Material</th><th>Buyer</th><th class="num">Kg</th><th class="num">Revenue</th></tr></thead>
      <tbody>
<?php if(!$rows): ?>
        <tr><td colspan="5" style="text-align:center;color:#999;padding:24px 0;">No sales recorded yet.</td></tr>
<?php endif; foreach($rows as $r): ?>
        <tr><td><?= date('M j, g:i A', strtotime($r['created_at'])) ?></td><td><?= mat_cell($r['color'], $r['short_name'], $r['item']) ?></td><td><?= e($r['buyer'] ?: '—') ?></td><td class="num"><?= number_format($r['kg'], 1) ?></td><td class="num">₱<?= number_format($r['kg'] * $r['price_per_kg'], 2) ?></td></tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
