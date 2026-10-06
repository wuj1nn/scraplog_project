function buildSortSelect(){
  dgebi('sortCat').innerHTML = CAT_ORDER.map(function(k){
    return '<option value="' + k + '" data-price="' + CAT_META[k].buy + '">' + CAT_META[k].label + '</option>';
  }).join('');
}

function updateSortPreview(){
  let sel = dgebi('sortCat');
  let price = parseFloat(sel.options[sel.selectedIndex].dataset.price);
  let kg = parseFloat(dgebi('sortKg').value) || 0;
  dgebi('sortPreview').textContent = '₱' + (price * kg).toFixed(2);
}

document.addEventListener('DOMContentLoaded', function(){
  buildSortSelect();
  updateSortPreview();
  dgebi('sortCat').addEventListener('change', updateSortPreview);
  dgebi('sortKg').addEventListener('input', updateSortPreview);
});
