let CAT_META = {};
let CAT_ORDER = [];
DB.categories.forEach(function(c){
  CAT_ORDER.push(c.code);
  CAT_META[c.code] = { label:c.label, short:c.short_name, color:c.color, buy:c.buy, sell:c.sell, stock:c.stock, cap:c.cap };
});

let ACTIVITY = DB.activity;
let LOG = DB.log;

let tfBuckets = { day:['8a','10a','12p','2p','4p','6p'], week:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], month:['Wk1','Wk2','Wk3','Wk4'], year:['Q1','Q2','Q3','Q4'] };

function parseTime(t){
  return new Date(t.replace(' ', 'T'));
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

function seriesFor(key, timeframe, kind){
  let type = kind === 'sold' ? 'out' : 'in';
  let arr = tfBuckets[timeframe].map(function(){ return 0; });
  let now = new Date();
  LOG.forEach(function(r){
    if(r.code !== key || r.type !== type) return;
    let i = bucketIndex(timeframe, parseTime(r.t), now);
    if(i >= 0) arr[i] += r.kg;
  });
  return arr.map(function(v){ return Math.round(v * 10) / 10; });
}

function totalSeries(timeframe, kind){
  return tfBuckets[timeframe].map(function(_, i){
    let sum = CAT_ORDER.reduce(function(a, k){ return a + seriesFor(k, timeframe, kind)[i]; }, 0);
    return Math.round(sum * 10) / 10;
  });
}
