let currentCategory = 'all', currentItem = 0, currentTimeframe = 'day', currentChartType = 'line';
let rptTf = 'day', rptMode = 'both', rptPaper = 'A4';
let TF_NAME = { day:'Daily', week:'Weekly', month:'Monthly', year:'Yearly' };
let PAPER_SIZE = { A4:'A4', Letter:'letter', Long:'8.5in 13in' };

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
function fillItemFilter(){
  let list = currentCategory === 'all' ? [] : ITEMS_OF[currentCategory];
  currentItem = 0;
  dgebi('itemGroup').classList.toggle('hidden', list.length < 2);
  dgebi('itemFilter').innerHTML = '<option value="0">All items</option>' + (list.length < 2 ? '' : list.map(function(i){
    return '<option value="' + i.id + '">' + esc(i.other ? 'Other' : i.name) + '</option>';
  }).join(''));
}
function onAnalyticsControlsChange(){
  currentCategory = dgebi('catFilter').value;
  fillItemFilter();
  renderAnalytics();
}
function timeframeLabel(tf){
  return { day:'today', week:'this week', month:'this month', year:'this year' }[tf];
}

function sumTotals(list, totals){
  let o = { in:0, out:0, rm:0, rev:0 };
  list.forEach(function(i){
    let t = totals[i.id];
    if(!t) return;
    o.in += t.in;
    o.out += t.out;
    o.rm += t.rm;
    o.rev += t.rev;
  });
  return o;
}

function tableRows(){
  let totals = periodTotals(currentTimeframe), rows = [];
  let cats = currentCategory === 'all' ? CAT_ORDER : [currentCategory];
  cats.forEach(function(k){
    let c = CAT_META[k], list = ITEMS_OF[k];
    if(currentCategory !== 'all' && list.length > 1){
      list.filter(function(i){ return !currentItem || i.id === currentItem; }).forEach(function(i){
        rows.push({ cell:matCell(i), t:sumTotals([i], totals), value:i.stock * i.buy });
      });
    } else {
      rows.push({ cell:'<span class="catChip"><i style="background:' + c.color + '"></i>' + esc(c.label) + '</span>', t:sumTotals(list, totals), value:c.value });
    }
  });
  return rows;
}

function seriesSet(kind){
  if(currentCategory === 'all') return totalSeries(currentTimeframe, kind);
  return seriesFor(currentCategory, currentTimeframe, kind, currentItem);
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
    descEl.textContent = 'In / out / removed / net movement for the selected period';
    dgebi('analyticsTableBody').innerHTML = tableRows().map(function(r){
      let net = r.t.in - r.t.out - r.t.rm;
      return '<tr><td>' + r.cell + '</td><td class="num">' + r.t.in.toFixed(1) + '</td><td class="num">' + r.t.out.toFixed(1) + '</td><td class="num">' + r.t.rm.toFixed(1) + '</td><td class="num">' + (net >= 0 ? '+' : '') + net.toFixed(1) + '</td><td class="num">' + peso(r.value, 2) + '</td></tr>';
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
    let inColor = currentCategory === 'all' ? '#f4c430' : CAT_META[currentCategory].color;
    let subject = 'Total material';
    if(currentCategory !== 'all'){
      let it = ITEMS[currentItem];
      subject = CAT_META[currentCategory].label + (it ? ' · ' + (it.other ? 'Other' : it.name) : '');
    }
    descEl.textContent = subject + ': sorted in, sold and removed, ' + timeframeLabel(currentTimeframe);
    drawLineChart(canvas, tfBuckets[currentTimeframe], [
      { label:'Sorted in (kg)', color:inColor, data:seriesSet('in') },
      { label:'Sold (kg)', color:'#3f9e6c', data:seriesSet('sold') },
      { label:'Removed (kg)', color:'#c1442f', data:seriesSet('removed') }
    ]);
    legend.innerHTML =
      '<span><i style="background:' + inColor + '"></i>Sorted in (kg)</span>' +
      '<span><i style="background:#3f9e6c"></i>Sold (kg)</span>' +
      '<span><i style="background:#c1442f"></i>Removed (kg)</span>';
  }
}

function periodLabel(tf){
  let n = new Date(), o = { month:'short', day:'numeric', year:'numeric' };
  if(tf === 'day') return n.toLocaleDateString('en-US', { weekday:'long', month:'long', day:'numeric', year:'numeric' });
  if(tf === 'week'){
    let mon = new Date(n.getFullYear(), n.getMonth(), n.getDate() - ((n.getDay() + 6) % 7));
    let sun = new Date(mon.getFullYear(), mon.getMonth(), mon.getDate() + 6);
    return mon.toLocaleDateString('en-US', o) + ' – ' + sun.toLocaleDateString('en-US', o);
  }
  if(tf === 'month') return n.toLocaleDateString('en-US', { month:'long', year:'numeric' });
  return String(n.getFullYear());
}

function k1(n){
  return Number(n).toLocaleString(undefined, { minimumFractionDigits:1, maximumFractionDigits:1 });
}

function rptBuild(){
  let totals = periodTotals(rptTf);
  let cols = { both:['in', 'out', 'rev', 'rm'], out:['out', 'rev'], in:['in'] }[rptMode];
  let head = { in:'Sorted in (kg)', out:'Sold (kg)', rev:'Sales', rm:'Removed (kg)' };
  let all = ['in', 'out', 'rev', 'rm'];
  let sum = { in:0, out:0, rev:0, rm:0 };
  let body = '';

  function has(o){
    return cols.some(function(c){ return o[c] > 0; });
  }
  function cells(o){
    return cols.map(function(c){ return '<td class="num">' + (c === 'rev' ? peso(o[c], 2) : k1(o[c])) + '</td>'; }).join('');
  }

  CAT_ORDER.forEach(function(k){
    let kids = ITEMS_OF[k].map(function(i){ return { it:i, t:totals[i.id] }; }).filter(function(x){ return x.t && has(x.t); });
    if(!kids.length) return;
    let ct = { in:0, out:0, rev:0, rm:0 };
    kids.forEach(function(x){ all.forEach(function(f){ ct[f] += x.t[f]; }); });
    all.forEach(function(f){ sum[f] += ct[f]; });
    body += '<tr class="rCat"><td>' + esc(CAT_META[k].label) + '</td>' + cells(ct) + '</tr>';
    if(ITEMS_OF[k].length > 1){
      kids.forEach(function(x){
        body += '<tr class="rItem"><td>' + esc(x.it.other ? 'Other' : x.it.name) + '</td>' + cells(x.t) + '</tr>';
      });
    }
  });

  let kind = { both:'Intake & Sales Report', out:'Sales Report', in:'Storage Intake Report' }[rptMode];
  let tiles = cols.map(function(c){
    return '<div class="rptTile"><span>' + head[c] + '</span><b>' + (c === 'rev' ? peso(sum[c], 2) : k1(sum[c])) + '</b></div>';
  }).join('');
  let printed = new Date().toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit' });

  return '<div class="rptHead"><div class="rptBrand">Scrap<span>Log</span></div><div class="rptMeta">Junkshop Inventory and Sales Analytics</div></div>' +
    '<h2 class="rptName">' + TF_NAME[rptTf] + ' ' + kind + '</h2>' +
    '<div class="rptPeriod">' + esc(periodLabel(rptTf)) + '</div>' +
    '<div class="rptSum">' + tiles + '</div>' +
    (body
      ? '<table class="rptTable"><thead><tr><th>Material</th>' + cols.map(function(c){ return '<th class="num">' + head[c] + '</th>'; }).join('') + '</tr></thead><tbody>' + body +
        '<tr class="rTot"><td>Total</td>' + cells(sum) + '</tr></tbody></table>'
      : '<p class="rptEmpty">No activity was recorded for this period.</p>') +
    '<div class="rptFoot">Prepared by ' + esc(document.body.dataset.user) + ' · Printed ' + esc(printed) + '</div>';
}

function renderReport(){
  let paper = dgebi('rptPaperEl');
  paper.dataset.size = rptPaper;
  paper.innerHTML = rptBuild();
}

function openReport(){
  rptTf = currentTimeframe;
  document.querySelectorAll('#rptTf button').forEach(function(b){ b.classList.toggle('active', b.dataset.tf === rptTf); });
  renderReport();
  dgebi('rptModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeReport(){
  dgebi('rptModal').classList.remove('open');
  document.body.style.overflow = '';
}

function printReport(){
  let old = dgebi('printArea');
  if(old) old.remove();
  let area = document.createElement('div');
  area.id = 'printArea';
  area.innerHTML = dgebi('rptPaperEl').outerHTML;
  document.body.appendChild(area);
  let st = dgebi('pageSizeStyle');
  if(!st){
    st = document.createElement('style');
    st.id = 'pageSizeStyle';
    document.head.appendChild(st);
  }
  st.textContent = '@page{size:' + PAPER_SIZE[rptPaper] + ';margin:12mm;}';
  document.body.classList.add('printing');
  window.print();
}

window.addEventListener('afterprint', function(){
  document.body.classList.remove('printing');
  let area = dgebi('printArea');
  if(area) area.remove();
});

window.pageRender = renderAnalytics;

document.addEventListener('DOMContentLoaded', function(){
  dgebi('catFilter').addEventListener('change', onAnalyticsControlsChange);
  dgebi('itemFilter').addEventListener('change', function(){
    currentItem = Number(this.value);
    renderAnalytics();
  });
  dgebi('timeframeSeg').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setTimeframe(e.target.dataset.tf);
  });
  dgebi('chartToggle').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    setChartType(e.target.dataset.type);
  });

  dgebi('printOpen').addEventListener('click', openReport);
  dgebi('rptClose').addEventListener('click', closeReport);
  dgebi('rptModal').addEventListener('click', function(e){
    if(e.target === this) closeReport();
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeReport();
  });
  dgebi('rptTf').addEventListener('click', function(e){
    if(e.target.tagName !== 'BUTTON') return;
    rptTf = e.target.dataset.tf;
    this.querySelectorAll('button').forEach(function(b){ b.classList.toggle('active', b === e.target); });
    renderReport();
  });
  dgebi('rptMode').addEventListener('change', function(){
    rptMode = this.value;
    renderReport();
  });
  dgebi('rptPaper').addEventListener('change', function(){
    rptPaper = this.value;
    renderReport();
  });
  dgebi('rptPrint').addEventListener('click', printReport);

  renderAnalytics();
});
