function openNav(){
  dgebi('navPanel').classList.add('open');
  dgebi('navScrim').classList.add('open');
}
function closeNav(){
  dgebi('navPanel').classList.remove('open');
  dgebi('navScrim').classList.remove('open');
}
function toggleNav(){
  if(dgebi('navPanel').classList.contains('open')){
    closeNav();
  } else {
    openNav();
  }
}

document.addEventListener('DOMContentLoaded', function(){
  dgebi('burgerBtn').addEventListener('click', toggleNav);
  dgebi('navScrim').addEventListener('click', closeNav);
});

document.addEventListener('DOMContentLoaded', function(){
  let toggle = dgebi('themeToggle');
  let menu = dgebi('profileMenu');

  toggle.checked = document.documentElement.dataset.theme === 'dark';
  toggle.addEventListener('change', function(){
    let theme = toggle.checked ? 'dark' : 'light';
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('theme', theme);
    redrawCharts();
  });

  dgebi('profileBtn').addEventListener('click', function(e){
    e.stopPropagation();
    this.setAttribute('aria-expanded', menu.classList.toggle('open'));
  });
  document.addEventListener('click', function(e){
    if(!menu.contains(e.target)) menu.classList.remove('open');
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') menu.classList.remove('open');
  });
});