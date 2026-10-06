let storageChartType = 'donut';
function setStorageChart(type){
  storageChartType = type;
  document.querySelectorAll('#storageSeg button').forEach(function(b){ b.classList.remove('active'); });
  document.querySelector('#storageSeg button[data-type="' + type + '"]').classList.add('active');
  renderStorage();
}
function renderStorage(){
  let donutView = dgebi('storageDonutView');
  let wrap = dgebi('storageCanvasWrap');
  let tableStage = dgebi('storageTableStage');
  let titleEl = dgebi('storageTitle');
  let descEl = dgebi('storageDesc');
  let total = stockTotal();
  let showValue = ROLE === 'admin';

  donutView.style.display = storageChartType === 'donut' ? 'block' : 'none';
  wrap.classList.toggle('hidden', storageChartType !== 'bar');
  tableStage.classList.toggle('active', storageChartType === 'table');

  if(storageChartType === 'table'){
    titleEl.textContent = 'Storage breakdown';
    descEl.textContent = showValue ? 'Capacity level and value per category' : 'Capacity level per category';
    dgebi('storageTableBody').innerHTML = CAT_ORDER.map(function(k){
      let c = CAT_META[k];
      let b = capBadge(c.cap);
      let value = (c.stock * c.buy).toFixed(2);
      return '<tr>' +
        '<td><div class="rowId"><div class="rowAvatar" style="background:' + c.color + '">' + c.short.slice(0,2).toUpperCase() + '</div><div class="txt"><b>' + c.label + '</b><span>' + c.stock + ' kg on hand</span></div></div></td>' +
        '<td><div style="display:flex;align-items:center;gap:10px;"><div class="capacityTrack"><div class="capacityFill" style="width:' + c.cap + '%;background:' + c.color + '"></div></div><span class="badge ' + b.cls + '">' + b.label + '</span></div></td>' +
        '<td class="num">' + c.stock + ' kg</td>' +
        (showValue ? '<td class="num">₱' + Number(value).toLocaleString(undefined,{minimumFractionDigits:2}) + '</td>' : '') +
        '</tr>';
    }).join('');
    return;
  }

  let values = CAT_ORDER.map(function(k){ return CAT_META[k].stock; });
  let colors = CAT_ORDER.map(function(k){ return CAT_META[k].color; });

  if(storageChartType === 'donut'){
    titleEl.textContent = 'Stock proportion by category';
    descEl.textContent = total.toLocaleString() + ' kg on hand across ' + CAT_ORDER.length + ' categories';
    drawDonutChart(dgebi('storageDonutChart'), values, colors);
    setText('storageDonutTotal', total.toLocaleString());
    renderDonutLegend('storageLegend', values, 'label', function(v){ return v + ' kg'; });
  }
  if(storageChartType === 'bar'){
    titleEl.textContent = 'Comparative storage volume';
    descEl.textContent = 'Current stock (kg) across categories';
    drawBarChart(dgebi('storageCanvas'), CAT_ORDER.map(function(k){ return CAT_META[k].short; }), values, colors);
  }
}
window.pageRender = renderStorage;

document.addEventListener('DOMContentLoaded', function(){
  dgebi('storageSeg').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setStorageChart(e.target.dataset.type);
  });
  renderStorage();
});

let historyRange = 30;

function fmtKg(n){
  return n.toLocaleString(undefined, { maximumFractionDigits:2 });
}

function fmtDate(d){
  return d.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) + ', ' +
    d.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' });
}

function historyRows(){
  let now = new Date();
  let start = historyRange === 1
    ? new Date(now.getFullYear(), now.getMonth(), now.getDate())
    : new Date(now.getTime() - historyRange * 86400000);
  let cat = dgebi('historyCat').value;

  let rows = LOG.filter(function(r){
    return r.type === 'in' && (cat === 'all' || r.code === cat) && parseTime(r.t) >= start;
  }).map(function(r){
    let c = CAT_META[r.code];
    return { date:parseTime(r.t), label:c.label, color:c.color, party:r.party, kg:r.kg };
  });

  let sorters = {
    recent:function(a, b){ return b.date - a.date; },
    oldest:function(a, b){ return a.date - b.date; },
    heavy:function(a, b){ return b.kg - a.kg; },
    light:function(a, b){ return a.kg - b.kg; },
    name:function(a, b){
      if(!a.party !== !b.party) return a.party ? -1 : 1;
      return a.party.localeCompare(b.party) || b.date - a.date;
    },
    type:function(a, b){ return a.label.localeCompare(b.label) || b.date - a.date; }
  };
  return rows.sort(sorters[dgebi('historySort').value]);
}

function renderHistory(){
  let rows = historyRows();
  let total = rows.reduce(function(a, r){ return a + r.kg; }, 0);
  dgebi('historyBody').innerHTML = rows.length ? rows.map(function(r){
    return '<tr><td>' + fmtDate(r.date) + '</td>' +
      '<td><span class="catChip"><i style="background:' + r.color + '"></i>' + esc(r.label) + '</span></td>' +
      '<td>' + (r.party ? esc(r.party) : '—') + '</td>' +
      '<td class="num">' + fmtKg(r.kg) + '</td></tr>';
  }).join('') : '<tr><td colspan="4" style="text-align:center;color:#999;padding:32px 0;">No entries for this filter.</td></tr>';
  setText('historyFoot', rows.length + (rows.length === 1 ? ' entry' : ' entries') + ' · ' + fmtKg(total) + ' kg total');
}

function openHistory(){
  dgebi('historyModal').classList.add('open');
  document.body.style.overflow = 'hidden';
  renderHistory();
}

function closeHistory(){
  dgebi('historyModal').classList.remove('open');
  document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function(){
  dgebi('historyCat').innerHTML = '<option value="all">All categories</option>' + CAT_ORDER.map(function(k){
    return '<option value="' + k + '">' + esc(CAT_META[k].label) + '</option>';
  }).join('');

  dgebi('historyOpen').addEventListener('click', openHistory);
  dgebi('historyClose').addEventListener('click', closeHistory);
  dgebi('historyModal').addEventListener('click', function(e){
    if(e.target === this) closeHistory();
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeHistory();
  });

  dgebi('historyRange').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    historyRange = Number(e.target.dataset.range);
    this.querySelectorAll('button').forEach(function(b){ b.classList.toggle('active', b === e.target); });
    renderHistory();
  });
  dgebi('historyCat').addEventListener('change', renderHistory);
  dgebi('historySort').addEventListener('change', renderHistory);
});