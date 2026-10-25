document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  toggle?.addEventListener('click', () => {
    const open = nav.classList.toggle('nav-open'); toggle.setAttribute('aria-expanded', String(open));
  });
  document.querySelectorAll('[data-confirm]').forEach(el => el.addEventListener('click', event => {
    if (!confirm(el.dataset.confirm || 'Are you sure?')) event.preventDefault();
  }));
  document.querySelectorAll('.toast').forEach(toast => setTimeout(() => toast.classList.add('toast-hide'), 4200));
  document.querySelectorAll('[data-reorder]').forEach((list, index) => {
    let dragged;
    list.querySelectorAll('[draggable="true"]').forEach(item => {
      item.addEventListener('dragstart', () => { dragged = item; item.classList.add('dragging'); });
      item.addEventListener('dragend', () => item.classList.remove('dragging'));
      item.addEventListener('dragover', event => { event.preventDefault(); if (dragged && dragged !== item) list.insertBefore(dragged, item); });
    });
  });
});
