/* PayHankey — shared interactions */
(function () {
  'use strict';

  /* ---- Sticky nav state + mobile toggle ---- */
  var nav = document.querySelector('.nav');
  if (nav) {
    var onScroll = function () { nav.classList.toggle('is-scrolled', window.scrollY > 8); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    var toggle = nav.querySelector('.nav__toggle');
    if (toggle) toggle.addEventListener('click', function () { nav.classList.toggle('is-open'); });
    nav.querySelectorAll('.nav__links a').forEach(function (a) {
      a.addEventListener('click', function () { nav.classList.remove('is-open'); });
    });
  }

  /* ---- Reveal on scroll ---- */
  var reveals = document.querySelectorAll('.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.12 });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  /* ---- FAQ accordion ---- */
  document.querySelectorAll('.faq__q').forEach(function (q) {
    q.addEventListener('click', function () {
      var item = q.closest('.faq__item');
      var a = item.querySelector('.faq__a');
      var open = item.classList.contains('is-open');
      if (open) { item.classList.remove('is-open'); a.style.maxHeight = null; }
      else { item.classList.add('is-open'); a.style.maxHeight = a.scrollHeight + 'px'; }
    });
  });

  /* ---- Animated count-up ---- */
  function animateCount(el) {
    var target = parseFloat(el.getAttribute('data-count'));
    var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
    var prefix = el.getAttribute('data-prefix') || '';
    var suffix = el.getAttribute('data-suffix') || '';
    var dur = 1500, start = null;
    function step(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      var val = target * eased;
      el.textContent = prefix + val.toLocaleString('en-US', {
        minimumFractionDigits: decimals, maximumFractionDigits: decimals
      }) + suffix;
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
  var counters = document.querySelectorAll('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { animateCount(e.target); cio.unobserve(e.target); } });
    }, { threshold: 0.5 });
    counters.forEach(function (c) { cio.observe(c); });
  } else { counters.forEach(animateCount); }

  /* ---- Hero balance ticker + live payout feed ---- */
  var balAmt = document.querySelector('[data-balance]');
  if (balAmt) {
    var bal = 1284.50;
    setInterval(function () {
      bal += Math.random() * 0.9 + 0.1;
      var whole = Math.floor(bal);
      var cents = Math.round((bal - whole) * 100).toString().padStart(2, '0');
      balAmt.innerHTML = '$' + whole.toLocaleString('en-US') + '<span class="cents">.' + cents + '</span>';
    }, 2600);
  }
  var feed = document.querySelector('[data-feed]');
  if (feed) {
    var names = [
      ['Amara O.', 'AO', 'a video', 1.20],
      ['Kwame B.', 'KB', 'a post', 0.85],
      ['Zinhle M.', 'ZM', 'a quiz', 0.60],
      ['Tunde A.', 'TA', 'a teaser', 1.05],
      ['Fatima D.', 'FD', 'comments', 0.45],
      ['Chidi N.', 'CN', 'a referral', 2.00],
      ['Lerato K.', 'LK', 'views', 1.75],
      ['Yusuf I.', 'YI', 'a post', 0.95]
    ];
    function feedItem(n) {
      return '<div class="feed__item"><div class="feed__av">' + n[1] + '</div>' +
        '<div class="feed__txt"><b>' + n[0] + '</b> earned from ' + n[2] + '</div>' +
        '<div class="feed__amt">+$' + n[3].toFixed(2) + '</div></div>';
    }
    var i = 0;
    feed.innerHTML = feedItem(names[0]) + feedItem(names[1]) + feedItem(names[2]);
    setInterval(function () {
      i = (i + 1) % names.length;
      var node = document.createElement('div');
      node.innerHTML = feedItem(names[i]);
      var item = node.firstChild;
      item.style.opacity = '0';
      feed.insertBefore(item, feed.firstChild);
      requestAnimationFrame(function () { item.style.transition = 'opacity .4s'; item.style.opacity = '1'; });
      if (feed.children.length > 3) feed.removeChild(feed.lastChild);
    }, 2600);
  }

  /* ---- Password show/hide ---- */
  document.querySelectorAll('.field__toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = btn.parentElement.querySelector('input');
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.classList.toggle('is-on');
    });
  });

  /* ---- Password strength ---- */
  var pw = document.querySelector('[data-strength]');
  if (pw) {
    var meter = document.querySelector('.pw-meter');
    var label = document.querySelector('.pw-label');
    var words = ['Too weak', 'Weak', 'Fair', 'Good', 'Strong'];
    pw.addEventListener('input', function () {
      var v = pw.value, s = 0;
      if (v.length >= 8) s++;
      if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
      if (/\d/.test(v)) s++;
      if (/[^A-Za-z0-9]/.test(v)) s++;
      if (v.length === 0) s = 0;
      meter.className = 'pw-meter s' + s;
      label.textContent = v.length ? 'Password strength: ' + words[s] : 'Use 8+ characters with a number and symbol.';
    });
  }

  /* ---- Leaderboard filter (visual only) ---- */
  document.querySelectorAll('.lb-filters button').forEach(function (b) {
    b.addEventListener('click', function () {
      b.parentElement.querySelectorAll('button').forEach(function (x) { x.classList.remove('is-active'); });
      b.classList.add('is-active');
    });
  });

  /* ---- Blog category filter ---- */
  var chips = document.querySelectorAll('.blog-cats .chip');
  if (chips.length) {
    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        chips.forEach(function (c) { c.classList.remove('is-active'); });
        chip.classList.add('is-active');
        var cat = chip.getAttribute('data-cat');
        document.querySelectorAll('[data-post-cat]').forEach(function (card) {
          var show = cat === 'all' || card.getAttribute('data-post-cat') === cat;
          card.style.display = show ? '' : 'none';
        });
      });
    });
  }

  /* ---- Contact form (front-end demo handling) ---- */
  document.querySelectorAll('[data-demo-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var note = form.querySelector('[data-form-note]');
      if (note) { note.hidden = false; note.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
      form.reset();
    });
  });
})();
