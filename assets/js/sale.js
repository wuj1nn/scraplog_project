function buildSale(){
  let opts = '', rows = '';
  CAT_ORDER.forEach(function(k){
    let list = ITEMS_OF[k].filter(function(i){ return i.stock > 0; });
    if(!list.length) return;
    opts += '<optgroup label="' + esc(CAT_META[k].label) + '">' + list.map(function(i){
      return '<option value="' + i.id + '">' + esc(i.other ? 'Other' : i.name) + ' — ' + i.stock + ' kg on hand</option>';
    }).join('') + '</optgroup>';
    rows += list.map(function(i){
      return '<tr class="pick" data-item="' + i.id + '"><td>' + matCell(i) + '</td><td class="num">' + peso(i.sell, 2) + '</td><td class="num">' + i.stock + ' kg</td></tr>';
    }).join('');
  });
  dgebi('saleItem').innerHTML = opts || '<option value="">Nothing in storage yet</option>';
  dgebi('saleBtn').disabled = !opts;
  dgebi('saleLegendBody').innerHTML = rows || '<tr><td colspan="3" style="text-align:center;color:#999;padding:24px 0;">Nothing in storage yet.</td></tr>';
}

function updateSalePreview(){
  let sel = dgebi('saleItem');
  let it = ITEMS[sel.value];
  let kg = parseFloat(dgebi('saleKg').value) || 0;
  dgebi('salePreview').textContent = '₱' + ((it ? it.sell : 0) * kg).toFixed(2);
  dgebi('saleRemaining').value = it ? Math.max(it.stock - kg, 0).toFixed(1) + ' kg' : '—';
  document.querySelectorAll('#saleLegendBody tr').forEach(function(tr){
    tr.classList.toggle('on', tr.dataset.item === sel.value);
  });
}

document.addEventListener('DOMContentLoaded', function(){
  buildSale();
  updateSalePreview();
  dgebi('saleItem').addEventListener('change', updateSalePreview);
  dgebi('saleKg').addEventListener('input', updateSalePreview);
  dgebi('saleLegendBody').addEventListener('click', function(e){
    let tr = e.target.closest('tr[data-item]');
    if(!tr) return;
    dgebi('saleItem').value = tr.dataset.item;
    updateSalePreview();
    dgebi('saleKg').focus();
  });
});
