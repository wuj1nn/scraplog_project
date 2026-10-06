let currentCategory = 'all', currentTimeframe = 'day', currentChartType = 'line';

function setTimeframe(tf){
  currentTimeframe = tf;
  document.querySelectorAll('#timeframeSeg button').forEach(function(b){ b.classList.remove('active'); });
  document.querySelector('#timeframeSeg button[data-tf="' + tf + '"]').classList.add('active');
  renderAnalytics();
}
function setChartType(type){
  currentChartType = type;
  document.querySelectorAll('#chartToggle button').forEach(function(b){ b.classList.remove('active'); });
  document.querySelector('#chartToggle button[data-type="' + type + '"]').classList.add('active');
  renderAnalytics();
}
function onAnalyticsControlsChange(){
  currentCategory = dgebi('catFilter').value;
  renderAnalytics();
}
function timeframeLabel(tf){
  return { day:'today', week:'this week', month:'this month', year:'this year' }[tf];
}

function renderAnalytics(){
  let wrap = dgebi('analyticsCanvasWrap');
  let tableStage = dgebi('analyticsTableStage');
  let legend = dgebi('analyticsLegend');
  let titleEl = dgebi('analyticsTitle');
  let descEl = dgebi('analyticsDesc');
  legend.innerHTML = '';

  if(currentChartType === 'table'){
    wrap.classList.add('hidden');
    tableStage.classList.add('active');
    titleEl.textContent = 'Raw breakdown';
    descEl.textContent = 'In / out / net movement for the selected period';
    let keys = currentCategory === 'all' ? CAT_ORDER : [currentCategory];
    dgebi('analyticsTableBody').innerHTML = keys.map(function(k){
      let c = CAT_META[k];
      let inArr = seriesFor(k, currentTimeframe, 'in');
      let soldArr = seriesFor(k, currentTimeframe, 'sold');
      let totalIn = inArr.reduce(function(a,b){ return a + b; }, 0);
      let totalSold = soldArr.reduce(function(a,b){ return a + b; }, 0);
      let net = totalIn - totalSold;
      let value = (c.stock * c.buy).toFixed(2);
      return '<tr><td><span class="catChip"><i style="background:' + c.color + '"></i>' + c.label + '</span></td><td class="num">' + totalIn.toFixed(1) + '</td><td class="num">' + totalSold.toFixed(1) + '</td><td class="num">' + (net >= 0 ? '+' : '') + net.toFixed(1) + '</td><td class="num">₱' + Number(value).toLocaleString(undefined,{minimumFractionDigits:2}) + '</td></tr>';
    }).join('');
    return;
  }

  tableStage.classList.remove('active');
  wrap.classList.remove('hidden');
  let canvas = dgebi('analyticsCanvas');

  if(currentChartType === 'bar'){
    titleEl.textContent = 'Comparative volume';
    descEl.textContent = currentCategory === 'all' ? 'Current stock (kg) across categories' : CAT_META[currentCategory].label + ' highlighted against other categories';
    drawBarChart(canvas, CAT_ORDER.map(function(k){ return CAT_META[k].short; }), CAT_ORDER.map(function(k){ return CAT_META[k].stock; }), CAT_ORDER.map(function(k){ return currentCategory === 'all' || currentCategory === k ? CAT_META[k].color : chartColors().empty; }));
  }

  if(currentChartType === 'line'){
    titleEl.textContent = 'Trend over time';
    let buckets = tfBuckets[currentTimeframe];
    if(currentCategory === 'all'){
      descEl.textContent = 'Total material sorted in vs. sold, ' + timeframeLabel(currentTimeframe);
      let inTotals = buckets.map(function(_, i){ return CAT_ORDER.reduce(function(sum, k){ return sum + seriesFor(k, currentTimeframe, 'in')[i]; }, 0); });
      let soldTotals = buckets.map(function(_, i){ return CAT_ORDER.reduce(function(sum, k){ return sum + seriesFor(k, currentTimeframe, 'sold')[i]; }, 0); });
      drawLineChart(canvas, buckets, [
        { label:'Sorted in (kg)', color:'#f4c430', data:inTotals },
        { label:'Sold (kg)', color:'#3f9e6c', data:soldTotals }
      ]);
    } else {
      let c = CAT_META[currentCategory];
      descEl.textContent = c.label + ': sorted in vs. sold, ' + timeframeLabel(currentTimeframe);
      drawLineChart(canvas, buckets, [
        { label:'Sorted in (kg)', color:c.color, data:seriesFor(currentCategory, currentTimeframe, 'in') },
        { label:'Sold (kg)', color:'#3f9e6c', data:seriesFor(currentCategory, currentTimeframe, 'sold') }
      ]);
    }
    legend.innerHTML =
      '<span><i style="background:' + (currentCategory === 'all' ? '#f4c430' : CAT_META[currentCategory].color) + '"></i>Sorted in (kg)</span>' +
      '<span><i style="background:#3f9e6c"></i>Sold (kg)</span>';
  }
}

window.pageRender = renderAnalytics;

document.addEventListener('DOMContentLoaded', function(){
  dgebi('catFilter').addEventListener('change', onAnalyticsControlsChange);
  dgebi('timeframeSeg').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setTimeframe(e.target.dataset.tf);
  });
  dgebi('chartToggle').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setChartType(e.target.dataset.type);
  });
  renderAnalytics();
});
