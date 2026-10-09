let themeKey = 'theme';
let root = document.documentElement;
let saved = null;
try { saved = localStorage.getItem(themeKey); } catch (e) {}
root.dataset.theme = saved || 'light';

document.addEventListener('DOMContentLoaded', function(){
  let box = document.getElementById('lpTheme');
  box.checked = root.dataset.theme === 'dark';
  box.addEventListener('change', function(){
    let t = box.checked ? 'dark' : 'light';
    root.dataset.theme = t;
    try { localStorage.setItem(themeKey, t); } catch (e) {}
  });
});