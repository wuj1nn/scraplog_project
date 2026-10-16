let CAT_META = {};
let CAT_ORDER = [];
let ITEMS = {};
let ITEMS_OF = {};
DB.categories.forEach(function(c){
  CAT_ORDER.push(c.code);
  CAT_META[c.code] = { label:c.label, short:c.short_name, color:c.color, stock:c.stock, cap:c.cap, value:c.value, proj:c.proj };
  ITEMS_OF[c.code] = [];
});
DB.items.forEach(function(i){
  ITEMS[i.id] = i;
  ITEMS_OF[i.cat].push(i);
});

let ACTIVITY = DB.activity;
let LOG = DB.log;

let tfBuckets = { day:['8a','10a','12p','2p','4p','6p'], week:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], month:['Wk1','Wk2','Wk3','Wk4'], year:['Q1','Q2','Q3','Q4'] };
let LOG_TYPE = { in:'in', sold:'out', removed:'rm' };

function parseTime(t){
  return new Date(t.replace(' ', 'T'));
}

function itemTag(it, detail){
  if(!it || it.name === CAT_META[it.cat].short) return '';
  return it.other && detail ? it.name + ': ' + detail : it.name;
}

function matCell(it, detail){
  let c = CAT_META[it.cat], tag = itemTag(it, detail);
  return '<span class="catChip"><i style="background:' + c.color + '"></i>' + esc(c.short) + (tag ? '<span class="catSub"> · ' + esc(tag) + '</span>' : '') + '</span>';
}

function bucketIndex(tf, d, now){
  if(tf === 'day'){
    if(d.toDateString() !== now.toDateString()) return -1;
    return Math.min(Math.max(Math.floor((d.getHours() - 8) / 2), 0), 5);
  }
  if(tf === 'week'){
    let monday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - ((now.getDay() + 6) % 7));
    let diff = Math.round((new Date(d.getFullYear(), d.getMonth(), d.getDate()) - monday) / 86400000);
    return diff >= 0 && diff < 7 ? diff : -1;
  }
  if(tf === 'month'){
    if(d.getFullYear() !== now.getFullYear() || d.getMonth() !== now.getMonth()) return -1;
    return Math.min(Math.floor((d.getDate() - 1) / 7), 3);
  }
  if(d.getFullYear() !== now.getFullYear()) return -1;
  return Math.floor(d.getMonth() / 3);
}

function sumBuckets(timeframe, type, field, pred){
  let arr = tfBuckets[timeframe].map(function(){ return 0; });
  let now = new Date();
  LOG.forEach(function(r){
    if(r.type !== type || !pred(r)) return;
    let i = bucketIndex(timeframe, parseTime(r.t), now);
    if(i >= 0) arr[i] += r[field];
  });
  let k = field === 'amt' ? 100 : 10;
  return arr.map(function(v){ return Math.round(v * k) / k; });
}

function seriesFor(key, timeframe, kind, item){
  return sumBuckets(timeframe, LOG_TYPE[kind], 'kg', function(r){ return r.code === key && (!item || r.item === item); });
}

function totalSeries(timeframe, kind){
  return sumBuckets(timeframe, LOG_TYPE[kind], 'kg', function(){ return true; });
}

function revenueSeries(timeframe){
  return sumBuckets(timeframe, 'out', 'amt', function(){ return true; });
}

function periodTotals(tf){
  let now = new Date(), t = {};
  LOG.forEach(function(r){
    if(bucketIndex(tf, parseTime(r.t), now) < 0) return;
    let o = t[r.item] || (t[r.item] = { in:0, out:0, rm:0, rev:0 });
    if(r.type === 'in') o.in += r.kg;
    else if(r.type === 'out'){ o.out += r.kg; o.rev += r.amt; }
    else o.rm += r.kg;
  });
  return t;
}
