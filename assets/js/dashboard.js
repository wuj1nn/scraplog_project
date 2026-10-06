function todayTotals(){
  let today = new Date().toDateString();
  let t = { kg:0, soldKg:0, sales:0 };
  LOG.forEach(function(r){
    if(parseTime(r.t).toDateString() !== today) return;
    if(r.type === 'in') t.kg += r.kg;
    else { t.sales++; t.soldKg += r.kg; }
  });
  return t;
}

function setRing(id, pct){
  let el = dgebi(id);
  if(el) el.style.setProperty('--pct', Math.max(0, Math.min(100, pct)) + '%');
}

function renderDashRings(){
  let total = stockTotal();
  let top = Math.max.apply(null, CAT_ORDER.map(function(k){ return CAT_META[k].stock; }));
  let avgCap = CAT_ORDER.reduce(function(a, k){ return a + CAT_META[k].cap; }, 0) / CAT_ORDER.length;
  let projected = projectedTotal();
  let value = DB.revenue + projected;
  let t = todayTotals();
  let movedToday = t.kg + t.soldKg;

  setRing('ringRevenue', value ? DB.revenue / value * 100 : 0);
  setRing('ringProjected', value ? projected / value * 100 : 0);
  setRing('ringStorage', avgCap);
  setRing('ringLargest', total ? top / total * 100 : 0);
  setRing('ringSorted', total ? t.kg / total * 100 : 0);
  setRing('ringSales', movedToday ? t.soldKg / movedToday * 100 : 0);
}

function renderDashStats(){
  let top = CAT_ORDER.slice().sort(function(a, b){ return CAT_META[b].stock - CAT_META[a].stock; })[0];
  let hasStock = CAT_META[top].stock > 0;
  let today = todayTotals();
  setText('statStorage', stockTotal().toLocaleString());
  setText('statLargest', hasStock ? CAT_META[top].short : '—');
  setText('statLargestKg', hasStock ? CAT_META[top].stock : 0);
  setText('statRevenue', '₱' + DB.revenue.toLocaleString(undefined,{maximumFractionDigits:0}));
  setText('statProjected', '₱' + projectedTotal().toLocaleString(undefined,{maximumFractionDigits:0}));
  setText('statSorted', today.kg.toFixed(1));
  setText('statSales', today.sales);
}

function renderDashActivity(){
  let showAmt = ROLE === 'admin';
  let body = dgebi('dashActivityBody');
  if(!ACTIVITY.length){
    body.innerHTML = '<tr><td colspan="' + (showAmt ? 5 : 4) + '" style="text-align:center;color:#999;padding:24px 0;">No activity yet. Sort some material or record a sale to get started.</td></tr>';
    return;
  }
  body.innerHTML = ACTIVITY.map(function(r){
    let c = CAT_META[r.code];
    let sold = r.type === 'out';
    return '<tr>' +
      '<td><div class="rowId"><div class="rowAvatar" style="background:' + c.color + '">' + c.short.slice(0,2).toUpperCase() + '</div>' +
      '<div class="txt"><b>' + c.label + '</b><span>Category</span></div></div></td>' +
      '<td><span class="badge ' + (sold ? 'high' : 'pos') + '">' + (sold ? 'Sold' : 'Sorted in') + '</span></td>' +
      '<td>' + (r.party ? esc(r.party) : '—') + '</td>' +
      '<td class="num">' + r.kg.toFixed(1) + '</td>' +
      (showAmt ? '<td class="num">' + (r.amt !== null ? '₱' + r.amt.toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2}) : '—') + '</td>' : '') +
      '</tr>';
  }).join('');
}

function renderDashLine(){
  if(!dgebi('dashLineChart')) return;
  let range = dgebi('dashTrendRange').value;
  drawLineChart(dgebi('dashLineChart'), tfBuckets[range], [
    { label:'Sorted in (kg)', color:'#f4c430', data:totalSeries(range, 'in') },
    { label:'Sold (kg)', color:'#3f9e6c', data:totalSeries(range, 'sold') }
  ]);
  dgebi('dashLineLegend').innerHTML =
    '<span><i style="background:#f4c430"></i>Sorted in (kg)</span>' +
    '<span><i style="background:#3f9e6c"></i>Sold (kg)</span>';
}

function renderDashDonut(){
  let data = CAT_ORDER.map(function(k){ return CAT_META[k].stock; });
  let colors = CAT_ORDER.map(function(k){ return CAT_META[k].color; });
  drawDonutChart(dgebi('dashDonutChart'), data, colors);
  setText('dashDonutTotal', stockTotal().toLocaleString());
  renderDonutLegend('dashDonutLegend', data, 'short', function(v){ return v + ' kg'; });
}

function renderDashToday(){
  if(!dgebi('todayIn')) return;
  let t = todayTotals();
  let net = t.kg - t.soldKg;
  setText('todayIn', t.kg.toFixed(1));
  setText('todaySold', t.soldKg.toFixed(1));
  setText('todayNet', (net > 0 ? '+' : '') + net.toFixed(1));
}

function renderDashboard(){
  renderDashStats();
  renderDashRings();
  renderDashActivity();
  renderDashLine();
  renderDashDonut();
  renderDashToday();
}
window.pageRender = renderDashboard;

document.addEventListener('DOMContentLoaded', function(){
  let range = dgebi('dashTrendRange');
  if(range) range.addEventListener('change', renderDashLine);
  renderDashboard();
});
