<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'sale';
require_page($page);
$pageScripts = ['sale.js'];
include __DIR__ . '/includes/header.php';
$rows = $pdo->query("SELECT s.kg, s.price_per_kg, s.buyer, s.created_at, c.short_name, c.color
    FROM sales s JOIN categories c ON c.id = s.category_id
    ORDER BY s.created_at DESC, s.id DESC LIMIT 20")->fetchAll();
?>
<section class="panel active" id="panel-sale">
      <div class="card">
        <div class="cardHead"><div><h2>Record a sale</h2><div class="cardDesc">Pick the material and quantity sold — stock and revenue update together</div></div></div>
        <form method="post" action="actions/sale_save.php">
          <?= csrf_field() ?>
          <div class="formRow">
            <div><label for="saleCat">Material</label><select id="saleCat" name="category"></select></div>
            <div><label for="saleKg">Quantity (kg)</label><input type="number" id="saleKg" name="kg" step="0.01" min="0.01" placeholder="0.0" required></div>
          </div>
          <div class="formRow">
            <div><label for="saleBuyer">Sold to (optional)</label><input type="text" id="saleBuyer" name="buyer" maxlength="100" placeholder="e.g. City Recyclers Inc."></div>
            <div><label>Remaining stock after sale</label><input type="text" id="saleRemaining" value="—" disabled></div>
          </div>
          <div class="previewBox"><span>Revenue for this sale</span><span class="amt" id="salePreview">₱0.00</span></div>
          <button class="btnCta" type="submit" style="border-radius:8px;padding:11px 22px;">Confirm sale</button>
        </form>
      </div>
      <br>
      <div class="card">
        <div class="cardHead"><h2>Recent sales</h2></div>
        <div class="tableWrap scrollBox"><table>
          <thead><tr><th>Time</th><th>Category</th><th>Buyer</th><th class="num">Kg</th><th class="num">Revenue</th></tr></thead>
          <tbody>
<?php if(!$rows): ?>
            <tr><td colspan="5" style="text-align:center;color:#999;padding:24px 0;">No sales recorded yet.</td></tr>
<?php endif; foreach($rows as $r): ?>
            <tr><td><?= date('M j, g:i A', strtotime($r['created_at'])) ?></td><td><span class="catChip"><i style="background:<?= e($r['color']) ?>"></i><?= e($r['short_name']) ?></span></td><td><?= e($r['buyer'] ?: '—') ?></td><td class="num"><?= number_format($r['kg'], 1) ?></td><td class="num">₱<?= number_format($r['kg'] * $r['price_per_kg'], 2) ?></td></tr>
<?php endforeach; ?>
          </tbody>
        </table></div>
      </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
