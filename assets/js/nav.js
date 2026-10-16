function openNav(){
  dgebi('navPanel').classList.add('open');
  dgebi('navScrim').classList.add('open');
  dgebi('burgerBtn').setAttribute('aria-expanded', 'true');
}
function closeNav(){
  dgebi('navPanel').classList.remove('open');
  dgebi('navScrim').classList.remove('open');
  dgebi('burgerBtn').setAttribute('aria-expanded', 'false');
}
function toggleNav(){
  if(dgebi('navPanel').classList.contains('open')){
    closeNav();
  } else {
    openNav();
  }
}

document.addEventListener('DOMContentLoaded', function(){
  let menu = dgebi('profileMenu');

  dgebi('burgerBtn').addEventListener('click', toggleNav);
  dgebi('navScrim').addEventListener('click', closeNav);
  dgebi('navPanel').addEventListener('click', function(e){
    if(!e.target.closest('a, button')) toggleNav();
  });

  dgebi('profileBtn').addEventListener('click', function(e){
    e.stopPropagation();
    this.setAttribute('aria-expanded', menu.classList.toggle('open'));
  });
  document.addEventListener('click', function(e){
    if(!menu.contains(e.target)) menu.classList.remove('open');
  });
  document.addEventListener('keydown', function(e){
    if(e.key !== 'Escape') return;
    menu.classList.remove('open');
    closeNav();
  });
});
