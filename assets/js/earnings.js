let earningsChartType = 'donut', earningsTf = 'month';
let WHEN = { day:'today', week:'this week', month:'this month', year:'this year' };

function setEarningsTf(tf){
  earningsTf = tf;
  document.querySelectorAll('#earningsTf button').forEach(function(b){ b.classList.toggle('active', b.dataset.tf === tf); });
  renderEarnings();
}
function setEarningsChart(type){
  earningsChartType = type;
  document.querySelectorAll('#earningsSeg button').forEach(function(b){ b.classList.toggle('active', b.dataset.type === type); });
  dgebi('earningsDonutView').style.display = type === 'donut' ? 'block' : 'none';
  dgebi('earningsBarWrap').classList.toggle('hidden', type !== 'bar');
  dgebi('earningsTableStage').classList.toggle('active', type === 'table');
  renderEarnings();
}

function earnedByCat(){
  let now = new Date(), t = {};
  CAT_ORDER.forEach(function(k){ t[k] = { rev:0, kg:0 }; });
  LOG.forEach(function(r){
    if(r.type !== 'out' || bucketIndex(earningsTf, parseTime(r.t), now) < 0) return;
    t[r.code].rev += r.amt;
    t[r.code].kg += r.kg;
  });
  return t;
}

function renderEarnings(){
  let t = earnedByCat();
  let when = WHEN[earningsTf];
  let revs = CAT_ORDER.map(function(k){ return t[k].rev; });
  let colors = CAT_ORDER.map(function(k){ return CAT_META[k].color; });
  let labels = CAT_ORDER.map(function(k){ return CAT_META[k].label; });
  let total = revs.reduce(function(a, b){ return a + b; }, 0);
  let soldKg = CAT_ORDER.reduce(function(a, k){ return a + t[k].kg; }, 0);
  let proj = projectedTotal();
  let stock = stockTotal();

  setText('statEarned', peso(total));
  setText('lblEarned', 'Earned ' + when);
  setText('statSoldKg', fmtKg(soldKg));
  setText('lblSold', 'Sold ' + when);
  setText('statProjected', peso(proj));
  setRing('ringEarned', total + proj ? total / (total + proj) * 100 : 0);
  setRing('ringSold', soldKg + stock ? soldKg / (soldKg + stock) * 100 : 0);
  setRing('ringProjected', total + proj ? proj / (total + proj) * 100 : 0);
  setText('earningsDesc', 'Sales recorded ' + when + '. The table also shows the projected value of the stock still on hand.');
  setText('earningsTrendDesc', 'Revenue from sales ' + when);

  drawDonutChart(dgebi('earningsDonutChart'), revs, colors, labels, function(v){ return peso(v, 2); });
  setText('earningsTotal', Math.round(total).toLocaleString());
  renderDonutLegend('earningsLegend', revs, 'label', function(v){ return peso(v); });

  drawBarChart(dgebi('earningsBarChart'), CAT_ORDER.map(function(k){ return CAT_META[k].short; }), revs.map(function(v){ return Math.round(v); }), colors, function(v){ return peso(v); });

  dgebi('earningsTableBody').innerHTML = CAT_ORDER.map(function(k){
    let c = CAT_META[k];
    return '<tr><td><span class="catChip"><i style="background:' + c.color + '"></i>' + esc(c.label) + '</span></td><td class="num">' + fmtKg(t[k].kg) + '</td><td class="num">' + peso(t[k].rev, 2) + '</td><td class="num">' + c.stock + ' kg</td><td class="num">' + peso(c.proj, 2) + '</td></tr>';
  }).join('');

  let buckets = tfBuckets[earningsTf];
  drawBarChart(dgebi('earningsTrendChart'), buckets, revenueSeries(earningsTf), buckets.map(function(){ return '#f4c430'; }), function(v){ return peso(v, 2); });
}
window.pageRender = renderEarnings;

document.addEventListener('DOMContentLoaded', function(){
  dgebi('earningsTf').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setEarningsTf(e.target.dataset.tf);
  });
  dgebi('earningsSeg').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setEarningsChart(e.target.dataset.type);
  });
  renderEarnings();
});
