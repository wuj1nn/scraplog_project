<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'manage';
require_page($page);
$pageScripts = ['manage.js'];
include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/data.php';
$cats = load_categories($pdo);
$admin = $role === 'admin';
$rows = $pdo->query("SELECT e.kg, e.source, e.detail, e.created_at, c.short_name, c.color, i.name AS item, i.buy_price
    FROM stock_entries e JOIN categories c ON c.id = e.category_id JOIN items i ON i.id = e.item_id
    WHERE DATE(e.created_at) = CURDATE() ORDER BY e.created_at DESC, e.id DESC LIMIT 20")->fetchAll();
?>
<section class="panel active" id="panel-manage">
  <div class="split">
    <div class="card">
      <div class="cardHead"><div><h2>Add to storage</h2><div class="cardDesc">Weigh in the material and file it under a category</div></div></div>
      <form method="post" action="actions/manage_save.php">
        <?= csrf_field() ?>
        <div class="formGrid">
          <div><label for="mgCat">Category</label><select id="mgCat"></select></div>
          <div id="mgItemWrap"><label for="mgItem">Item</label><select id="mgItem" name="item"></select></div>
          <div id="mgDetailWrap" class="hidden"><label for="mgDetail">Describe the item</label><input type="text" id="mgDetail" name="detail" maxlength="100" placeholder="e.g. toy parts, old keyboard"></div>
          <div><label for="mgKg">Weight (kg)</label><input type="number" id="mgKg" name="kg" step="0.01" min="0.01" placeholder="0.0" required></div>
          <div><label for="mgSource">Brought in by (optional)</label><input type="text" id="mgSource" name="source" maxlength="100" placeholder="Walk-in supplier name"></div>
          <div><label for="mgNote">Note (optional)</label><input type="text" id="mgNote" name="note" maxlength="255" placeholder="e.g. mixed with light rust"></div>
        </div>
        <div class="previewBox"><span>Estimated value at current buying price</span><span class="amt" id="mgPreview">₱0.00</span></div>
        <button class="btnCta" type="submit" style="border-radius:8px;padding:11px 22px;">Add to storage</button>
      </form>
    </div>

    <div class="card">
      <div class="cardHead"><div><h2>Price legend</h2><div class="cardDesc"><?= $admin ? 'Set the sell price per kilo — the buy price follows from the offset' : 'Current buying and selling price per kilo' ?></div></div></div>
<?php if($admin): ?>
      <form method="post" action="actions/price_save.php" id="lgForm">
        <?= csrf_field() ?>
<?php endif; ?>
        <div class="tableWrap scrollBox lgScroll"><table class="lgTable">
          <thead><tr><th>Category</th><th class="num">Sell / kg</th><?php if($admin): ?><th class="num">Offset</th><?php endif; ?><th class="num">Buy / kg</th><th class="num">On hand</th><?php if($admin): ?><th class="num">Value</th><?php endif; ?></tr></thead>
          <tbody>
<?php foreach($cats as $c): $multi = count($c['items']) > 1; ?>
<?php if($multi): ?>
            <tr class="lgGroup" data-g="<?= e($c['code']) ?>">
              <td><span class="catChip"><i style="background:<?= e($c['color']) ?>"></i><?= e($c['label']) ?></span></td>
              <td colspan="<?= $admin ? 3 : 2 ?>"></td>
              <td class="num"><?= $c['stock'] ?> kg</td>
<?php if($admin): ?>
              <td class="num">₱<span class="gVal"><?= number_format($c['value'], 2) ?></span></td>
<?php endif; ?>
            </tr>
<?php endif; ?>
<?php foreach($c['items'] as $i): ?>
            <tr class="lgRow<?= $multi ? ' lgSub' : '' ?>" data-g="<?= e($c['code']) ?>" data-stock="<?= $i['stock'] ?>">
              <td><?php if($multi): ?><?= e($i['other'] ? 'Other (describe)' : $i['name']) ?><?php else: ?><span class="catChip"><i style="background:<?= e($c['color']) ?>"></i><?= e($c['label']) ?></span><?php endif; ?></td>
<?php if($admin): ?>
              <td class="num"><input type="number" class="lgIn sellIn" name="sell[<?= $i['id'] ?>]" value="<?= number_format($i['sell'], 2, '.', '') ?>" step="0.01" min="0" max="1000000" required></td>
              <td class="num"><span class="offWrap" title="Sell price is this much above the buy price">+<input type="number" class="lgIn offIn" name="offset[<?= $i['id'] ?>]" value="<?= $i['markup'] + 0 ?>" step="0.01" min="0" max="300" required>%</span></td>
              <td class="num"><input type="text" class="lgIn buyOut" value="<?= number_format($i['buy'], 2, '.', '') ?>" disabled></td>
              <td class="num"><?= $i['stock'] ?> kg</td>
              <td class="num">₱<span class="vOut"><?= number_format($i['stock'] * $i['buy'], 2) ?></span></td>
<?php else: ?>
              <td class="num">₱<?= number_format($i['sell'], 2) ?></td>
              <td class="num">₱<?= number_format($i['buy'], 2) ?></td>
              <td class="num"><?= $i['stock'] ?> kg</td>
<?php endif; ?>
            </tr>
<?php endforeach; endforeach; ?>
          </tbody>
        </table></div>
<?php if($admin): ?>
        <div class="lgTools">
          <label for="lgAll">Offset for all</label>
          <span class="offWrap">+<input type="number" id="lgAll" class="lgAllIn" value="25" step="0.01" min="0" max="300">%</span>
          <button type="button" class="btnOutline" id="lgApply">Apply</button>
          <button class="btnCta" type="submit" style="border-radius:8px;padding:11px 22px;margin-left:auto;">Save price changes</button>
        </div>
        <p class="lgNote">Buy / kg = Sell / kg ÷ (1 + offset). Example: ₱18.00 sell at +25% gives ₱14.40 buy.</p>
      </form>
<?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="cardHead"><h2>Logged today</h2></div>
    <div class="tableWrap scrollBox"><table>
      <thead><tr><th>Time</th><th>Material</th><th>Supplier</th><th class="num">Kg</th><th class="num">Est. Value</th></tr></thead>
      <tbody>
<?php if(!$rows): ?>
        <tr><td colspan="5" style="text-align:center;color:#999;padding:24px 0;">Nothing logged today yet.</td></tr>
<?php endif; foreach($rows as $r): ?>
        <tr><td><?= date('g:i A', strtotime($r['created_at'])) ?></td><td><?= mat_cell($r['color'], $r['short_name'], $r['item'], $r['detail']) ?></td><td><?= e($r['source'] ?: '—') ?></td><td class="num"><?= number_format($r['kg'], 1) ?></td><td class="num">₱<?= number_format($r['kg'] * $r['buy_price'], 2) ?></td></tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
