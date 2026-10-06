function chartColors(){
  let dark = document.documentElement.dataset.theme === 'dark';
  return dark
    ? { grid:'#33332f', text:'#8d8d88', empty:'#33332f' }
    : { grid:'#ececea', text:'#999', empty:'#e6e6e3' };
}

let chartSilent = false;

function clamp01(t){
  return Math.max(0, Math.min(1, t));
}

function easeOut(t){
  return 1 - Math.pow(1 - t, 3);
}

function easeInOut(t){
  return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

function animateChart(canvas, duration, paint){
  if(canvas._raf) cancelAnimationFrame(canvas._raf);
  if(chartSilent){
    paint(1);
    return;
  }
  let start = null;
  function frame(now){
    if(start === null) start = now;
    let t = clamp01((now - start) / duration);
    paint(t);
    if(t < 1) canvas._raf = requestAnimationFrame(frame);
  }
  paint(0);
  canvas._raf = requestAnimationFrame(frame);
}

function fitCanvas(canvas){
  let box = canvas.parentElement.getBoundingClientRect();
  let dpr = window.devicePixelRatio || 1;
  canvas.style.width = box.width + 'px';
  canvas.style.height = box.height + 'px';
  canvas.width = Math.max(box.width, 10) * dpr;
  canvas.height = Math.max(box.height, 10) * dpr;
  let ctx = canvas.getContext('2d');
  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  ctx.clearRect(0, 0, box.width, box.height);
  return { ctx:ctx, w:box.width, h:box.height };
}

function noDataNote(ctx, w, h){
  ctx.fillStyle = chartColors().text;
  ctx.font = '13px Segoe UI';
  ctx.textAlign = 'center';
  ctx.fillText('No data yet', w / 2, h / 2);
}

function drawGrid(ctx, f, pad, plotH, maxVal){
  let c = chartColors();
  ctx.strokeStyle = c.grid;
  ctx.lineWidth = 1;
  ctx.font = '10px Segoe UI';
  ctx.fillStyle = c.text;
  ctx.textAlign = 'right';
  for(let i = 0; i <= 4; i++){
    let y = pad.top + plotH - (plotH * i / 4);
    ctx.beginPath();
    ctx.moveTo(pad.left, y);
    ctx.lineTo(f.w - pad.right, y);
    ctx.stroke();
    ctx.fillText(Math.round(maxVal * i / 4), pad.left - 6, y + 3);
  }
}

function drawLineChart(canvas, labels, series){
  if(canvas.parentElement.clientWidth === 0) return;
  animateChart(canvas, 1100, function(t){ paintLine(canvas, labels, series, t); });
}

function paintLine(canvas, labels, series, t){
  let f = fitCanvas(canvas);
  let ctx = f.ctx;
  let pad = { left:34, right:8, top:10, bottom:22 };
  let plotW = f.w - pad.left - pad.right;
  let plotH = f.h - pad.top - pad.bottom;
  let allVals = [];
  series.forEach(function(s){ allVals = allVals.concat(s.data); });
  let maxVal = Math.max.apply(null, allVals.concat([1])) * 1.15;

  drawGrid(ctx, f, pad, plotH, maxVal);

  ctx.fillStyle = chartColors().text;
  ctx.textAlign = 'center';
  let stepX = labels.length > 1 ? plotW / (labels.length - 1) : 0;
  labels.forEach(function(lab, i){
    ctx.fillText(lab, pad.left + stepX * i, f.h - 6);
  });

  let reveal = easeInOut(t);
  ctx.save();
  ctx.beginPath();
  ctx.rect(0, 0, reveal >= 1 ? f.w : pad.left + plotW * reveal, f.h);
  ctx.clip();

  series.forEach(function(s){
    ctx.beginPath();
    s.data.forEach(function(v, i){
      let x = pad.left + stepX * i;
      let y = pad.top + plotH - (plotH * v / maxVal);
      if(i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    });
    ctx.lineTo(pad.left + stepX * (s.data.length - 1), pad.top + plotH);
    ctx.lineTo(pad.left, pad.top + plotH);
    ctx.closePath();
    ctx.fillStyle = s.color + '26';
    ctx.fill();

    ctx.beginPath();
    s.data.forEach(function(v, i){
      let x = pad.left + stepX * i;
      let y = pad.top + plotH - (plotH * v / maxVal);
      if(i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    });
    ctx.strokeStyle = s.color;
    ctx.lineWidth = 2;
    ctx.stroke();

    s.data.forEach(function(v, i){
      let x = pad.left + stepX * i;
      let y = pad.top + plotH - (plotH * v / maxVal);
      ctx.beginPath();
      ctx.arc(x, y, 3, 0, Math.PI * 2);
      ctx.fillStyle = s.color;
      ctx.fill();
    });
  });
  ctx.restore();

  if(allVals.every(function(v){ return v === 0; })) noDataNote(ctx, f.w, f.h);
}

function drawBarChart(canvas, labels, data, colors){
  if(canvas.parentElement.clientWidth === 0) return;
  animateChart(canvas, 900, function(t){ paintBars(canvas, labels, data, colors, t); });
}

function paintBars(canvas, labels, data, colors, t){
  let f = fitCanvas(canvas);
  let ctx = f.ctx;
  let pad = { left:34, right:8, top:10, bottom:22 };
  let plotW = f.w - pad.left - pad.right;
  let plotH = f.h - pad.top - pad.bottom;
  let maxVal = Math.max.apply(null, data.concat([1])) * 1.15;
  let base = pad.top + plotH;

  drawGrid(ctx, f, pad, plotH, maxVal);

  let slot = plotW / data.length;
  let barW = Math.min(slot * 0.5, 40);
  ctx.textAlign = 'center';
  data.forEach(function(v, i){
    let local = easeOut(clamp01((t - (i / data.length) * 0.4) / 0.6));
    let barH = plotH * v / maxVal * local;
    let x = pad.left + slot * i + (slot - barW) / 2;
    let y = base - barH;
    if(barH >= 0.5){
      let r = Math.min(4, barH / 2, barW / 2);
      ctx.fillStyle = colors[i];
      ctx.beginPath();
      ctx.moveTo(x, y + r);
      ctx.arcTo(x, y, x + r, y, r);
      ctx.lineTo(x + barW - r, y);
      ctx.arcTo(x + barW, y, x + barW, y + r, r);
      ctx.lineTo(x + barW, base);
      ctx.lineTo(x, base);
      ctx.closePath();
      ctx.fill();
    }
    ctx.fillStyle = chartColors().text;
    ctx.fillText(labels[i], x + barW / 2, f.h - 6);
  });
  if(data.every(function(v){ return v === 0; })) noDataNote(ctx, f.w, f.h);
}

function drawDonutChart(canvas, data, colors){
  if(canvas.parentElement.clientWidth === 0) return;
  animateChart(canvas, 1000, function(t){ paintDonut(canvas, data, colors, t); });
}

function paintDonut(canvas, data, colors, t){
  let f = fitCanvas(canvas);
  let ctx = f.ctx;
  let cx = f.w / 2, cy = f.h / 2;
  let radius = Math.min(f.w, f.h) / 2 - 3;
  let inner = radius * 0.66;
  let total = data.reduce(function(a, b){ return a + b; }, 0);
  if(total === 0){
    data = [1];
    colors = [chartColors().empty];
    total = 1;
  }

  let sweep = easeInOut(t) * Math.PI * 2;
  let drawn = 0;
  let start = -Math.PI / 2;
  data.forEach(function(v, i){
    let angle = (v / total) * Math.PI * 2;
    let part = Math.min(angle, Math.max(sweep - drawn, 0));
    let from = start;
    drawn += angle;
    start += angle;
    if(part <= 0) return;
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.arc(cx, cy, radius, from, from + part);
    ctx.closePath();
    ctx.fillStyle = colors[i];
    ctx.fill();
  });

  ctx.globalCompositeOperation = 'destination-out';
  ctx.beginPath();
  ctx.arc(cx, cy, inner, 0, Math.PI * 2);
  ctx.fill();
  ctx.globalCompositeOperation = 'source-over';
}

function renderDonutLegend(id, values, labelKey, fmt){
  let total = values.reduce(function(a, b){ return a + b; }, 0);
  dgebi(id).innerHTML = CAT_ORDER.map(function(k, i){
    let c = CAT_META[k];
    let pct = total ? ((values[i] / total) * 100).toFixed(0) : '0';
    return '<div class="row"><span class="swatch" style="background:' + c.color + '"></span>' + c[labelKey] + '<span class="amt">' + fmt(values[i]) + ' · ' + pct + '%</span></div>';
  }).join('');
}

function redrawCharts(){
  chartSilent = true;
  if(window.pageRender) window.pageRender();
  chartSilent = false;
}

let resizeTimer;
window.addEventListener('resize', function(){
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(redrawCharts, 150);
});