(function(){
  var root = document.documentElement, timer;
  var saved = null;
  try { saved = localStorage.getItem('theme'); } catch (e) {}
  root.dataset.theme = saved || 'light';

  window.setTheme = function(t){
    var apply = function(){
      root.dataset.theme = t;
      try { localStorage.setItem('theme', t); } catch (e) {}
    };
    var done = function(){ if(window.redrawCharts) window.redrawCharts(); };
    if (document.startViewTransition) document.startViewTransition(apply).finished.then(done);
    else { apply(); setTimeout(done, 220); }
  };
  
  document.addEventListener('DOMContentLoaded', function(){
    var box = document.getElementById('themeToggle');
    if(!box) return;
    box.checked = root.dataset.theme === 'dark';
    box.addEventListener('change', function(){ setTheme(box.checked ? 'dark' : 'light'); });
  });
})();
