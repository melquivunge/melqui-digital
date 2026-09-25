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

/**
 * Project gallery lightbox. Image URLs come from the server-rendered
 * data-src attributes (attachment large size), never from free text.
 */
(function () {
  var root = document.querySelector('[data-gallery]');

  if (!root) {
    return;
  }

  var dialog = root.querySelector('[data-gallery-dialog]');
  var frame = dialog ? dialog.querySelector('[data-gallery-img]') : null;

  if (!dialog || !frame) {
    return;
  }

  function closeLightbox() {
    if (dialog.open) {
      dialog.close();
    }

    frame.removeAttribute('src');
    frame.alt = '';
  }

  root.addEventListener('click', function (event) {
    var trigger = event.target.closest('[data-gallery-open]');

    if (!trigger || !root.contains(trigger)) {
      return;
    }

    var src = trigger.getAttribute('data-src') || '';
    var alt = trigger.getAttribute('data-alt') || '';
    var parsed;

    try {
      parsed = new URL(src, window.location.origin);
    } catch (error) {
      return;
    }

    if (
      (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') ||
      parsed.origin !== window.location.origin
    ) {
      return;
    }

    frame.src = parsed.href;
    frame.alt = alt;
    dialog.showModal();
  });

  var closer = dialog.querySelector('[data-gallery-close]');

  if (closer) {
    closer.addEventListener('click', closeLightbox);
  }

  dialog.addEventListener('click', function (event) {
    if (event.target === dialog) {
      closeLightbox();
    }
  });

  dialog.addEventListener('close', function () {
    frame.removeAttribute('src');
    frame.alt = '';
  });
})();
