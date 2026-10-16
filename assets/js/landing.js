let root = document.documentElement;
let saved = null;
try { saved = localStorage.getItem('theme'); } catch (e) {}
root.dataset.theme = saved || 'light';

document.addEventListener('DOMContentLoaded', function(){
  let box = document.getElementById('lpTheme');
  box.checked = root.dataset.theme === 'dark';
  box.addEventListener('change', function(){
    let t = box.checked ? 'dark' : 'light';
    let apply = function(){
      root.dataset.theme = t;
      try { localStorage.setItem('theme', t); } catch (e) {}
    };
    if (document.startViewTransition) document.startViewTransition(apply);
    else apply();
  });
});