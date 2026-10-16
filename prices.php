<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'prices';
require_page($page);
$pageScripts = ['prices.js'];
include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/data.php';
$cats = load_categories($pdo);
?>
<section class="panel active" id="panel-prices">
  <div class="split">
    <div class="card">
      <div class="cardHead"><div><h2>Price estimator</h2><div class="cardDesc">See what a load is worth at today's prices</div></div></div>
      <form onsubmit="return false">
        <div class="seg pbSeg" id="pbMode">
          <button type="button" class="active" data-mode="buy">Sell to us</button>
          <button type="button" data-mode="sell">Buy from us</button>
        </div>
        <div class="formGrid">
          <div><label for="pbItem">Material</label><select id="pbItem"></select></div>
          <div><label for="pbKg">Weight (kg)</label><input type="number" id="pbKg" step="0.01" min="0" placeholder="0.0"></div>
        </div>
        <div class="previewBox"><span id="pbRate">₱0.00 / kg</span><span class="amt" id="pbTotal">₱0.00</span></div>
      </form>
    </div>

    <div class="card">
      <div class="cardHead"><div><h2>Today's prices</h2><div class="cardDesc">Price per kilo for every material we handle</div></div></div>
      <div class="tableWrap scrollBox lgScroll"><table class="lgTable">
        <thead><tr><th>Material</th><th class="num">We buy at</th><th class="num">We sell at</th><th class="num">Availability</th></tr></thead>
        <tbody>
<?php foreach($cats as $c): $multi = count($c['items']) > 1; ?>
<?php if($multi): ?>
          <tr class="lgGroup"><td colspan="4"><span class="catChip"><i style="background:<?= e($c['color']) ?>"></i><?= e($c['label']) ?></span></td></tr>
<?php endif; ?>
<?php foreach($c['items'] as $i): ?>
          <tr class="lgRow<?= $multi ? ' lgSub' : '' ?>">
            <td><?php if($multi): ?><?= e($i['other'] ? 'Other items' : $i['name']) ?><?php else: ?><span class="catChip"><i style="background:<?= e($c['color']) ?>"></i><?= e($c['label']) ?></span><?php endif; ?></td>
            <td class="num">₱<?= number_format($i['buy'], 2) ?></td>
            <td class="num">₱<?= number_format($i['sell'], 2) ?></td>
            <td class="num"><span class="badge <?= $i['stock'] > 0 ? 'pos' : 'high' ?>"><?= $i['stock'] > 0 ? 'In stock' : 'Out of stock' ?></span></td>
          </tr>
<?php endforeach; endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
