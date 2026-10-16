function fillItems(){
  let list = ITEMS_OF[dgebi('mgCat').value] || [];
  dgebi('mgItem').innerHTML = list.map(function(i){
    return '<option value="' + i.id + '">' + esc(i.other ? 'Other (describe)' : i.name) + '</option>';
  }).join('');
  dgebi('mgItemWrap').classList.toggle('hidden', list.length < 2);
  syncDetail();
}

function syncDetail(){
  let it = ITEMS[dgebi('mgItem').value];
  let other = !!(it && it.other);
  dgebi('mgDetailWrap').classList.toggle('hidden', !other);
  dgebi('mgDetail').required = other;
  if(!other) dgebi('mgDetail').value = '';
  updateIntakePreview();
}

function updateIntakePreview(){
  let it = ITEMS[dgebi('mgItem').value];
  let kg = parseFloat(dgebi('mgKg').value) || 0;
  dgebi('mgPreview').textContent = '₱' + ((it ? it.buy : 0) * kg).toFixed(2);
}

function legendBuy(sell, off){
  return Math.round(sell / (1 + off / 100) * 100) / 100;
}

function money(n){
  return n.toLocaleString(undefined, { minimumFractionDigits:2, maximumFractionDigits:2 });
}

function refreshLegend(){
  let sums = {};
  document.querySelectorAll('#lgForm .lgRow').forEach(function(row){
    let buy = legendBuy(parseFloat(row.querySelector('.sellIn').value) || 0, parseFloat(row.querySelector('.offIn').value) || 0);
    let val = buy * parseFloat(row.dataset.stock);
    row.querySelector('.buyOut').value = buy.toFixed(2);
    row.querySelector('.vOut').textContent = money(val);
    sums[row.dataset.g] = (sums[row.dataset.g] || 0) + val;
  });
  document.querySelectorAll('#lgForm .lgGroup').forEach(function(g){
    g.querySelector('.gVal').textContent = money(sums[g.dataset.g] || 0);
  });
}

document.addEventListener('DOMContentLoaded', function(){
  dgebi('mgCat').innerHTML = CAT_ORDER.map(function(k){
    return '<option value="' + k + '">' + esc(CAT_META[k].label) + '</option>';
  }).join('');
  fillItems();
  dgebi('mgCat').addEventListener('change', fillItems);
  dgebi('mgItem').addEventListener('change', syncDetail);
  dgebi('mgKg').addEventListener('input', updateIntakePreview);

  let form = dgebi('lgForm');
  if(!form) return;
  form.addEventListener('input', function(e){
    if(e.target.classList.contains('sellIn') || e.target.classList.contains('offIn')) refreshLegend();
  });
  dgebi('lgApply').addEventListener('click', function(){
    let v = dgebi('lgAll').value;
    if(v === '') return;
    form.querySelectorAll('.offIn').forEach(function(i){ i.value = v; });
    refreshLegend();
  });
});
