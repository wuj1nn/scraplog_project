document.addEventListener('DOMContentLoaded', function(){
  let modal = dgebi('rmModal'), kg = dgebi('rmKg'), reason = dgebi('rmReason'), cur = { stock:0, buy:0 };

  function value(){
    dgebi('rmValue').textContent = '₱' + (cur.buy * (parseFloat(kg.value) || 0)).toFixed(2);
  }
  function syncReason(){
    let other = reason.value === 'Other';
    dgebi('rmNote').required = other;
    setText('rmNoteLbl', other ? 'Why is it being removed?' : 'Details (optional)');
  }
  function open(btn){
    cur = { stock:parseFloat(btn.dataset.stock), buy:parseFloat(btn.dataset.buy) };
    dgebi('rmItem').value = btn.dataset.item;
    setText('rmName', btn.dataset.name);
    setText('rmStock', cur.stock + ' kg');
    kg.value = '';
    kg.max = cur.stock;
    reason.selectedIndex = 0;
    dgebi('rmNote').value = '';
    syncReason();
    value();
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    kg.focus();
  }
  function close(){
    modal.classList.remove('open');
    document.body.style.overflow = '';
  }

  dgebi('rmBody').addEventListener('click', function(e){
    let btn = e.target.closest('.rmBtn');
    if(btn) open(btn);
  });
  dgebi('rmCat').addEventListener('change', function(){
    let shown = 0;
    document.querySelectorAll('#rmBody tr[data-cat]').forEach(function(tr){
      let on = this.value === 'all' || tr.dataset.cat === this.value;
      tr.classList.toggle('hidden', !on);
      if(on) shown++;
    }, this);
    dgebi('rmEmpty').classList.toggle('hidden', shown > 0);
  });
  dgebi('rmAll').addEventListener('click', function(){
    kg.value = cur.stock;
    value();
  });
  kg.addEventListener('input', value);
  reason.addEventListener('change', syncReason);
  dgebi('rmClose').addEventListener('click', close);
  dgebi('rmCancel').addEventListener('click', close);
  modal.addEventListener('click', function(e){
    if(e.target === modal) close();
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') close();
  });
});
