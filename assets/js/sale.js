function buildSaleSelect(){
  dgebi('saleCat').innerHTML = CAT_ORDER.map(function(k){
    return '<option value="' + k + '" data-price="' + CAT_META[k].sell + '" data-stock="' + CAT_META[k].stock + '">' + CAT_META[k].label + ' — ' + CAT_META[k].stock + ' kg on hand</option>';
  }).join('');
}

function updateSalePreview(){
  let sel = dgebi('saleCat');
  let opt = sel.options[sel.selectedIndex];
  let price = parseFloat(opt.dataset.price);
  let stock = parseFloat(opt.dataset.stock);
  let kg = parseFloat(dgebi('saleKg').value) || 0;
  dgebi('salePreview').textContent = '₱' + (price * kg).toFixed(2);
  dgebi('saleRemaining').value = Math.max(stock - kg, 0).toFixed(1) + ' kg';
}

document.addEventListener('DOMContentLoaded', function(){
  buildSaleSelect();
  updateSalePreview();
  dgebi('saleCat').addEventListener('change', updateSalePreview);
  dgebi('saleKg').addEventListener('input', updateSalePreview);
});
