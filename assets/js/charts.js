function chartColors(){
  let dark = document.documentElement.dataset.theme === 'dark';
  return dark
    ? { grid:'#33332f', text:'#8d8d88', empty:'#33332f' }
    : { grid:'#ececea', text:'#999', empty:'#e6e6e3' };
}

let chartSilent = false;
let tipEl = null;

function clamp01(t){
  return Math.max(0, Math.min(1, t));
}

function easeOut(t){
  return 1 - Math.pow(1 - t, 3);
}

function easeInOut(t){
  return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

function tipRow(color, name, value){
  return '<div style="display:flex;align-items:center;gap:8px;margin-top:3px">' +
    '<span style="width:8px;height:8px;border-radius:50%;background:' + color + '"></span>' +
    (name ? '<span style="opacity:.75">' + name + '</span>' : '') +
    '<b style="margin-left:auto;padding-left:14px">' + value + '</b></div>';
}

function showTip(e, html){
  if(!tipEl){
    tipEl = document.createElement('div');
    tipEl.style.cssText = 'position:fixed;left:0;top:0;pointer-events:none;z-index:9999;padding:9px 12px;border-radius:10px;font:12px Segoe UI;background:#2a2a28;color:#f2f2ef;border:1px solid #444;box-shadow:0 6px 20px rgba(0,0,0,.35);opacity:0;transition:opacity .12s;white-space:nowrap';
    document.body.appendChild(tipEl);
  }
  tipEl.innerHTML = html;
  let w = tipEl.offsetWidth, h = tipEl.offsetHeight;
  let x = e.clientX + 14, y = e.clientY + 14;
  if(x + w > window.innerWidth - 8) x = e.clientX - w - 14;
  if(y + h > window.innerHeight - 8) y = e.clientY - h - 14;
  tipEl.style.transform = 'translate(' + x + 'px,' + y + 'px)';
  tipEl.style.opacity = '1';
}

function hideTip(){
  if(tipEl) tipEl.style.opacity = '0';
}

function bindHover(canvas, hit, repaint){
  canvas._hover = null;
  canvas._hit = hit;
  canvas._repaint = repaint;
  hideTip();
  if(canvas._bound) return;
  canvas._bound = true;
  canvas.addEventListener('mousemove', function(e){
    let r = canvas.getBoundingClientRect();
    let h = canvas._hit(e.clientX - r.left, e.clientY - r.top);
    let key = h ? h.key : null;
    if(key !== canvas._hover){
      canvas._hover = key;
      if(canvas._raf) cancelAnimationFrame(canvas._raf);
      canvas._repaint(1);
    }
    canvas.style.cursor = h ? 'pointer' : 'default';
    if(h) showTip(e, h.html); else hideTip();
  });
  canvas.addEventListener('mouseleave', function(){
    hideTip();
    if(canvas._hover === null) return;
    canvas._hover = null;
    canvas._repaint(1);
  });
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

function drawLineChart(canvas, labels, series, fmt){
  if(canvas.parentElement.clientWidth === 0) return;
  fmt = fmt || String;
  let pad = { left:34, right:8, top:10, bottom:22 };
  bindHover(canvas, function(x, y){
    let w = canvas.getBoundingClientRect().width;
    let plotW = w - pad.left - pad.right;
    if(x < pad.left - 12 || x > w - pad.right + 12) return null;
    let stepX = labels.length > 1 ? plotW / (labels.length - 1) : 0;
    let i = stepX ? Math.round((x - pad.left) / stepX) : 0;
    i = Math.max(0, Math.min(labels.length - 1, i));
    let html = '<div style="font-weight:700">' + labels[i] + '</div>' + series.map(function(s){
      return tipRow(s.color, s.name || s.label || '', fmt(s.data[i]));
    }).join('');
    return { key:i, html:html };
  }, function(t){ paintLine(canvas, labels, series, t); });
  animateChart(canvas, 1100, function(t){ paintLine(canvas, labels, series, t); });
}

function paintLine(canvas, labels, series, t){
  let f = fitCanvas(canvas);
  let ctx = f.ctx;
  let hover = canvas._hover;
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

  if(hover !== null && hover !== undefined){
    let hx = pad.left + stepX * hover;
    ctx.save();
    ctx.strokeStyle = chartColors().text;
    ctx.globalAlpha = 0.5;
    ctx.setLineDash([4, 4]);
    ctx.beginPath();
    ctx.moveTo(hx, pad.top);
    ctx.lineTo(hx, pad.top + plotH);
    ctx.stroke();
    ctx.restore();
    series.forEach(function(s){
      let hy = pad.top + plotH - (plotH * s.data[hover] / maxVal);
      ctx.save();
      ctx.shadowColor = s.color;
      ctx.shadowBlur = 14;
      ctx.beginPath();
      ctx.arc(hx, hy, 6, 0, Math.PI * 2);
      ctx.fillStyle = s.color;
      ctx.fill();
      ctx.restore();
      ctx.beginPath();
      ctx.arc(hx, hy, 2.5, 0, Math.PI * 2);
      ctx.fillStyle = '#fff';
      ctx.fill();
    });
  }

  if(allVals.every(function(v){ return v === 0; })) noDataNote(ctx, f.w, f.h);
}

function drawBarChart(canvas, labels, data, colors, fmt){
  if(canvas.parentElement.clientWidth === 0) return;
  fmt = fmt || String;
  let pad = { left:34, right:8, top:10, bottom:22 };
  bindHover(canvas, function(x, y){
    let r = canvas.getBoundingClientRect();
    let slot = (r.width - pad.left - pad.right) / data.length;
    let i = Math.floor((x - pad.left) / slot);
    if(i < 0 || i >= data.length || y < pad.top || y > r.height - pad.bottom) return null;
    let html = '<div style="font-weight:700">' + labels[i] + '</div>' + tipRow(colors[i], '', fmt(data[i]));
    return { key:i, html:html };
  }, function(t){ paintBars(canvas, labels, data, colors, t); });
  animateChart(canvas, 900, function(t){ paintBars(canvas, labels, data, colors, t); });
}

function paintBars(canvas, labels, data, colors, t){
  let f = fitCanvas(canvas);
  let ctx = f.ctx;
  let hover = canvas._hover;
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
      let on = hover === i;
      ctx.save();
      ctx.globalAlpha = hover === null || hover === undefined || on ? 1 : 0.3;
      if(on){
        ctx.shadowColor = colors[i];
        ctx.shadowBlur = 16;
      }
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
      ctx.restore();
    }
    ctx.fillStyle = chartColors().text;
    ctx.fillText(labels[i], x + barW / 2, f.h - 6);
  });
  if(data.every(function(v){ return v === 0; })) noDataNote(ctx, f.w, f.h);
}

function drawDonutChart(canvas, data, colors, labels, fmt){
  if(canvas.parentElement.clientWidth === 0) return;
  fmt = fmt || String;
  labels = labels || (typeof CAT_ORDER !== 'undefined' ? CAT_ORDER : data.map(function(_, i){ return 'Item ' + (i + 1); }));
  let total = data.reduce(function(a, b){ return a + b; }, 0);
  bindHover(canvas, function(x, y){
    let r = canvas.getBoundingClientRect();
    let radius = Math.min(r.width, r.height) / 2 - 10;
    let dx = x - r.width / 2, dy = y - r.height / 2;
    let d = Math.sqrt(dx * dx + dy * dy);
    if(!total || d < radius * 0.66 || d > radius + 4) return null;
    let a = (Math.atan2(dy, dx) + Math.PI * 2.5) % (Math.PI * 2);
    let acc = 0;
    for(let i = 0; i < data.length; i++){
      acc += data[i] / total * Math.PI * 2;
      if(a < acc){
        let pct = Math.round(data[i] / total * 100);
        let html = '<div style="font-weight:700">' + labels[i] + '</div>' + tipRow(colors[i], pct + '%', fmt(data[i]));
        return { key:i, html:html };
      }
    }
    return null;
  }, function(t){ paintDonut(canvas, data, colors, t); });
  animateChart(canvas, 1000, function(t){ paintDonut(canvas, data, colors, t); });
}

function paintDonut(canvas, data, colors, t){
  let f = fitCanvas(canvas);
  let ctx = f.ctx;
  let hover = canvas._hover;
  let cx = f.w / 2, cy = f.h / 2;
  let radius = Math.min(f.w, f.h) / 2 - 10;
  let inner = radius * 0.66;
  let total = data.reduce(function(a, b){ return a + b; }, 0);
  if(total === 0){
    data = [1];
    colors = [chartColors().empty];
    total = 1;
    hover = null;
  }

  let sweep = easeInOut(t) * Math.PI * 2;
  let drawn = 0;
  let start = -Math.PI / 2;
  let parts = [];
  data.forEach(function(v, i){
    let angle = (v / total) * Math.PI * 2;
    let part = Math.min(angle, Math.max(sweep - drawn, 0));
    if(part > 0) parts.push({ i:i, from:start, to:start + part });
    drawn += angle;
    start += angle;
  });

  function slice(p, r){
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.arc(cx, cy, r, p.from, p.to);
    ctx.closePath();
    ctx.fillStyle = colors[p.i];
    ctx.fill();
  }

  let dim = hover === null || hover === undefined ? 1 : 0.3;
  ctx.globalAlpha = dim;
  parts.forEach(function(p){ if(p.i !== hover) slice(p, radius); });
  ctx.globalAlpha = 1;
  parts.forEach(function(p){
    if(p.i !== hover) return;
    ctx.save();
    ctx.shadowColor = colors[p.i];
    ctx.shadowBlur = 18;
    slice(p, radius + 4);
    ctx.restore();
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