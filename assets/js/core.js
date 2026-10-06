function dgebi(id){
  return document.getElementById(id);
}

let ROLE = document.body.dataset.role;

function setText(id, text){
  let el = dgebi(id);
  if(el) el.textContent = text;
}

function stockTotal(){
  return CAT_ORDER.reduce(function(a, k){ return a + CAT_META[k].stock; }, 0);
}

function projectedTotal(){
  return CAT_ORDER.reduce(function(a, k){ return a + CAT_META[k].stock * CAT_META[k].sell; }, 0);
}

function capBadge(cap){
  if(cap >= 60) return { cls:'high', label:'Near full' };
  if(cap >= 30) return { cls:'mid', label:'Moderate' };
  return { cls:'pos', label:'Low' };
}

function esc(s){
  return String(s).replace(/[&<>"']/g, function(ch){
    return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[ch];
  });
}
