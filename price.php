<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'price';
require_page($page);
$pageScripts = [];
include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/data.php';
$cats = load_categories($pdo);
?>
<section class="panel active" id="panel-price">
      <div class="card">
        <div class="cardHead"><div><h2>Price legend</h2><div class="cardDesc">Default buying &amp; selling prices per kilo — applied to every future valuation</div></div></div>
        <form method="post" action="actions/price_save.php">
          <?= csrf_field() ?>
          <div class="tableWrap"><table>
            <thead><tr><th>Category</th><th class="num">Buy / kg</th><th class="num">Sell / kg</th><th class="num">On hand</th><th class="num">Value</th></tr></thead>
            <tbody>
<?php foreach($cats as $c): ?>
              <tr>
                <td><span class="catChip"><i style="background:<?= e($c['color']) ?>"></i><?= e($c['label']) ?></span></td>
                <td class="num"><input type="number" name="buy[<?= e($c['code']) ?>]" value="<?= number_format($c['buy'], 2, '.', '') ?>" step="0.01" min="0" style="width:88px;text-align:right;padding:6px 8px;"></td>
                <td class="num"><input type="number" name="sell[<?= e($c['code']) ?>]" value="<?= number_format($c['sell'], 2, '.', '') ?>" step="0.01" min="0" style="width:88px;text-align:right;padding:6px 8px;"></td>
                <td class="num"><?= $c['stock'] ?> kg</td>
                <td class="num">₱<?= number_format($c['buy'] * $c['stock'], 2) ?></td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table></div>
          <div style="margin-top:16px;"><button class="btnCta" type="submit" style="border-radius:8px;padding:11px 22px;">Save price changes</button></div>
        </form>
      </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
