<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'remove';
require_page($page);
$pageScripts = ['remove.js'];
include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/data.php';
$cats = load_categories($pdo);
$recent = $pdo->query("SELECT r.kg, r.reason, r.note, r.price_per_kg, r.created_at, c.short_name, c.color, i.name AS item, u.name AS who
    FROM removals r JOIN categories c ON c.id = r.category_id JOIN items i ON i.id = r.item_id JOIN users u ON u.id = r.user_id
    ORDER BY r.created_at DESC, r.id DESC LIMIT 20")->fetchAll();
?>
<section class="panel active" id="panel-remove">
  <div class="card">
    <div class="cardHead">
      <div><h2>Remove from storage</h2><div class="cardDesc">Take out material that rusted away, got damaged, or can't be sold</div></div>
      <select class="chipSelect" id="rmCat">
        <option value="all">All categories</option>
<?php foreach($cats as $c): ?>
        <option value="<?= e($c['code']) ?>"><?= e($c['label']) ?></option>
<?php endforeach; ?>
      </select>
    </div>
    <div class="tableWrap scrollBox lgScroll"><table class="lgTable">
      <thead><tr><th>Material</th><th class="num">On hand</th><th class="num">Value</th><th class="num"></th></tr></thead>
      <tbody id="rmBody">
<?php $n = 0; foreach($cats as $c): foreach($c['items'] as $i): if($i['stock'] <= 0) continue; $n++; ?>
        <tr data-cat="<?= e($c['code']) ?>">
          <td><?= mat_cell($c['color'], $c['short_name'], $i['name']) ?></td>
          <td class="num"><?= $i['stock'] ?> kg</td>
          <td class="num">₱<?= number_format($i['stock'] * $i['buy'], 2) ?></td>
          <td class="num"><button type="button" class="btnOutline rmBtn" data-item="<?= $i['id'] ?>" data-name="<?= e(item_label($c['short_name'], $i['name'])) ?>" data-stock="<?= $i['stock'] ?>" data-buy="<?= $i['buy'] ?>">Remove</button></td>
        </tr>
<?php endforeach; endforeach; ?>
<?php if(!$n): ?>
        <tr><td colspan="4" style="text-align:center;color:#999;padding:24px 0;">Nothing in storage to remove.</td></tr>
<?php endif; ?>
        <tr id="rmEmpty" class="hidden"><td colspan="4" style="text-align:center;color:#999;padding:24px 0;">Nothing in storage for this category.</td></tr>
      </tbody>
    </table></div>
  </div>

  <div class="card gapTop">
    <div class="cardHead"><h2>Recent removals</h2></div>
    <div class="tableWrap scrollBox"><table>
      <thead><tr><th>Time</th><th>Material</th><th class="num">Kg</th><th>Reason</th><th>Details</th><th>By</th><th class="num">Value lost</th></tr></thead>
      <tbody>
<?php if(!$recent): ?>
        <tr><td colspan="7" style="text-align:center;color:#999;padding:24px 0;">Nothing removed yet.</td></tr>
<?php endif; foreach($recent as $r): ?>
        <tr><td><?= date('M j, g:i A', strtotime($r['created_at'])) ?></td><td><?= mat_cell($r['color'], $r['short_name'], $r['item']) ?></td><td class="num"><?= number_format($r['kg'], 1) ?></td><td><span class="badge rm"><?= e($r['reason']) ?></span></td><td><?= e($r['note'] ?: '—') ?></td><td><?= e($r['who']) ?></td><td class="num">₱<?= number_format($r['kg'] * $r['price_per_kg'], 2) ?></td></tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</section>

<div class="modalScrim" id="rmModal">
  <div class="modalBox small" role="dialog" aria-modal="true" aria-labelledby="rmTitle">
    <div class="modalHead">
      <div><h2 id="rmTitle">Remove from storage</h2><div class="cardDesc" id="rmName"></div></div>
      <button type="button" class="modalClose" id="rmClose" aria-label="Close">&times;</button>
    </div>
    <form method="post" action="actions/remove_save.php" class="modalForm">
      <?= csrf_field() ?>
      <input type="hidden" name="item" id="rmItem">
      <div class="rmInfo"><span>Currently on hand</span><b id="rmStock"></b></div>
      <div class="formRow">
        <div>
          <label for="rmKg">How much to remove (kg)</label>
          <input type="number" id="rmKg" name="kg" step="0.01" min="0.01" placeholder="0.0" required>
          <button type="button" class="linkBtn" id="rmAll">Remove all</button>
        </div>
        <div>
          <label for="rmReason">Why is it being removed?</label>
          <select id="rmReason" name="reason">
<?php foreach(removal_reasons() as $r): ?>
            <option value="<?= e($r) ?>"><?= $r === 'Other' ? 'Other (type it out)' : e($r) ?></option>
<?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="rmNoteRow">
        <label for="rmNote" id="rmNoteLbl">Details (optional)</label>
        <input type="text" id="rmNote" name="note" maxlength="255" placeholder="e.g. left outside during the rain">
      </div>
      <div class="previewBox"><span>Value written off (at buying price)</span><span class="amt" id="rmValue">₱0.00</span></div>
      <div class="modalActions">
        <button type="button" class="btnOutline" id="rmCancel">Cancel</button>
        <button type="submit" class="btnDanger">Confirm removal</button>
      </div>
    </form>
  </div>
</div>
<?php
include __DIR__ . '/includes/footer.php';
