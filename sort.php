<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'sort';
require_page($page);
$pageScripts = ['sort.js'];
include __DIR__ . '/includes/header.php';
$rows = $pdo->query("SELECT e.kg, e.source, e.created_at, c.short_name, c.color, c.buy_price
    FROM stock_entries e JOIN categories c ON c.id = e.category_id
    WHERE DATE(e.created_at) = CURDATE() ORDER BY e.created_at DESC, e.id DESC LIMIT 20")->fetchAll();
?>
<section class="panel active" id="panel-sort">
      <div class="card">
        <div class="cardHead"><div><h2>Add to storage</h2><div class="cardDesc">Weigh in the material and file it under a category</div></div></div>
        <form method="post" action="actions/sort_save.php">
          <?= csrf_field() ?>
          <div class="formRow">
            <div><label for="sortCat">Category</label><select id="sortCat" name="category"></select></div>
            <div><label for="sortKg">Weight (kg)</label><input type="number" id="sortKg" name="kg" step="0.01" min="0.01" placeholder="0.0" required></div>
          </div>
          <div class="formRow">
            <div><label for="sortSupplier">Brought in by (optional)</label><input type="text" id="sortSupplier" name="source" maxlength="100" placeholder="Walk-in supplier name"></div>
            <div><label for="sortNote">Note (optional)</label><input type="text" id="sortNote" name="note" maxlength="255" placeholder="e.g. mixed with light rust"></div>
          </div>
          <div class="previewBox"><span>Estimated value at current buying price</span><span class="amt" id="sortPreview">₱0.00</span></div>
          <button class="btnCta" type="submit" style="border-radius:8px;padding:11px 22px;">Add to storage</button>
        </form>
      </div>
      <br>
      <div class="card">
        <div class="cardHead"><h2>Logged today</h2></div>
        <div class="tableWrap scrollBox"><table>
          <thead><tr><th>Time</th><th>Category</th><th>Supplier</th><th class="num">Kg</th><th class="num">Est. Value</th></tr></thead>
          <tbody>
<?php if(!$rows): ?>
            <tr><td colspan="5" style="text-align:center;color:#999;padding:24px 0;">Nothing logged today yet.</td></tr>
<?php endif; foreach($rows as $r): ?>
            <tr><td><?= date('g:i A', strtotime($r['created_at'])) ?></td><td><span class="catChip"><i style="background:<?= e($r['color']) ?>"></i><?= e($r['short_name']) ?></span></td><td><?= e($r['source'] ?: '—') ?></td><td class="num"><?= number_format($r['kg'], 1) ?></td><td class="num">₱<?= number_format($r['kg'] * $r['buy_price'], 2) ?></td></tr>
<?php endforeach; ?>
          </tbody>
        </table></div>
      </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
