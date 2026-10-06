let earningsChartType = 'donut';
function setEarningsChart(type){
  earningsChartType = type;
  document.querySelectorAll('#earningsSeg button').forEach(function(b){ b.classList.remove('active'); });
  document.querySelector('#earningsSeg button[data-type="' + type + '"]').classList.add('active');
  dgebi('earningsDonutView').style.display = type === 'donut' ? 'block' : 'none';
  dgebi('earningsBarWrap').classList.toggle('hidden', type !== 'bar');
  dgebi('earningsTableStage').classList.toggle('active', type === 'table');
  if(type === 'donut') renderEarningsDonut();
  if(type === 'bar') renderEarningsBar();
}
function renderEarningsDonut(){
  let values = CAT_ORDER.map(function(k){ return CAT_META[k].stock * CAT_META[k].sell; });
  let colors = CAT_ORDER.map(function(k){ return CAT_META[k].color; });
  drawDonutChart(dgebi('earningsDonutChart'), values, colors);
  setText('earningsTotal', Math.round(projectedTotal()).toLocaleString());
  renderDonutLegend('earningsLegend', values, 'label', function(v){ return '₱' + v.toLocaleString(undefined,{maximumFractionDigits:0}); });
}
function renderEarningsBar(){
  drawBarChart(dgebi('earningsBarChart'), CAT_ORDER.map(function(k){ return CAT_META[k].short; }), CAT_ORDER.map(function(k){ return Math.round(CAT_META[k].stock * CAT_META[k].sell); }), CAT_ORDER.map(function(k){ return CAT_META[k].color; }));
}
function renderEarningsTable(){
  dgebi('earningsTableBody').innerHTML = CAT_ORDER.map(function(k){
    let c = CAT_META[k];
    let value = c.stock * c.sell;
    return '<tr><td><span class="catChip"><i style="background:' + c.color + '"></i>' + c.label + '</span></td><td class="num">' + c.stock + ' kg</td><td class="num">₱' + c.sell.toFixed(2) + '</td><td class="num">₱' + value.toLocaleString(undefined,{minimumFractionDigits:2}) + '</td></tr>';
  }).join('');
}

function renderEarnings(){
  renderEarningsDonut();
  renderEarningsTable();
  if(earningsChartType === 'bar') renderEarningsBar();
}
window.pageRender = renderEarnings;

document.addEventListener('DOMContentLoaded', function(){
  dgebi('earningsSeg').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setEarningsChart(e.target.dataset.type);
  });
  renderEarnings();
});
