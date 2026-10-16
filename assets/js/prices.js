let pbMode = 'buy';

function pbCalc(){
  let it = ITEMS[dgebi('pbItem').value];
  let kg = parseFloat(dgebi('pbKg').value) || 0;
  let rate = it ? (pbMode === 'buy' ? it.buy : it.sell) : 0;
  setText('pbRate', peso(rate, 2) + ' / kg');
  setText('pbTotal', peso(rate * kg, 2));
}

document.addEventListener('DOMContentLoaded', function(){
  dgebi('pbItem').innerHTML = CAT_ORDER.map(function(k){
    return '<optgroup label="' + esc(CAT_META[k].label) + '">' + ITEMS_OF[k].map(function(i){
      return '<option value="' + i.id + '">' + esc(i.other ? 'Other items' : i.name) + '</option>';
    }).join('') + '</optgroup>';
  }).join('');
  dgebi('pbMode').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    pbMode = e.target.dataset.mode;
    this.querySelectorAll('button').forEach(function(b){ b.classList.toggle('active', b === e.target); });
    pbCalc();
  });
  dgebi('pbItem').addEventListener('change', pbCalc);
  dgebi('pbKg').addEventListener('input', pbCalc);
  pbCalc();
});
