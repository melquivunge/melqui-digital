/**
 * Mobile navigation toggle.
 *
 * The markup ships open-by-default so the menu still works with JavaScript
 * disabled; this script only adds the collapsing behaviour.
 */
(function () {
  var toggle = document.querySelector('[data-nav-toggle]');
  var nav = document.getElementById('site-nav');

  if (!toggle || !nav) {
    return;
  }

  toggle.hidden = false;
  document.documentElement.classList.add('has-js');

  var label = toggle.querySelector('[data-label-open]');

  function setOpen(open) {
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    nav.dataset.open = open ? 'true' : 'false';

    if (label) {
      label.textContent = open ? label.dataset.labelClose : label.dataset.labelOpen;
    }
  }

  setOpen(false);

  toggle.addEventListener('click', function () {
    setOpen(toggle.getAttribute('aria-expanded') !== 'true');
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
      setOpen(false);
      toggle.focus();
    }
  });

  // A tap outside the panel closes it.
  document.addEventListener('click', function (event) {
    if (toggle.getAttribute('aria-expanded') !== 'true') {
      return;
    }

    if (!nav.contains(event.target) && !toggle.contains(event.target)) {
      setOpen(false);
    }
  });
})();
