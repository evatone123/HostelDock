/* HostelDock launch video — every frame is a pure function of time. */
(function () {
  'use strict';

  var FPS = 30;
  var DUR = 21.0;

  var SCENES = [
    { id: 's1', a: 0.0,  b: 3.2 },
    { id: 's2', a: 3.2,  b: 8.0 },
    { id: 's3', a: 8.0,  b: 12.3 },
    { id: 's4', a: 12.3, b: 17.1 },
    { id: 's5', a: 17.1, b: 21.0 }
  ];

  function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }
  function P(t, a, d) { return clamp01((t - a) / d); }
  function eo(x) { return 1 - Math.pow(1 - x, 3); }
  function eo5(x) { return 1 - Math.pow(1 - x, 5); }
  function back(x) {
    var c1 = 1.15, c3 = c1 + 1;
    var v = 1 + c3 * Math.pow(x - 1, 3) + c1 * Math.pow(x - 1, 2);
    return v < 0 ? 0 : v;
  }
  function lerp(a, b, x) { return a + (b - a) * x; }

  function el(id) { return document.getElementById(id); }

  function put(node, opacity, y, scale) {
    if (!node) return;
    node.style.opacity = opacity;
    var s = (scale === undefined) ? '' : ' scale(' + scale.toFixed(4) + ')';
    node.style.transform = 'translate3d(0,' + (y || 0).toFixed(2) + 'px,0)' + s;
  }

  var rectCache = {};
  function centerOf(id) {
    if (!rectCache[id]) {
      var r = el(id).getBoundingClientRect();
      rectCache[id] = { x: r.left + r.width / 2, y: r.top + r.height / 2 };
    }
    return rectCache[id];
  }

  /* ---------- scene wrappers: out, then in — never a muddy crossfade ---------- */
  function wrapScene(scene, t) {
    var node = el(scene.id);
    if (t < scene.a - 0.0001 || t >= scene.b) {
      node.style.opacity = 0;
      node.style.visibility = 'hidden';
      node.style.transform = 'translate3d(0,0,0)';
      return false;
    }
    var enterDur = 0.42;
    var e = eo(P(t, scene.a - 0.05, enterDur));
    var last = scene.b >= DUR - 0.001;
    var x = last ? 0 : P(t, scene.b - 0.30, 0.30);
    var y = (1 - e) * 26 - x * 34;
    var s = 1 - x * 0.02;
    node.style.visibility = 'visible';
    node.style.opacity = (e * (1 - x)).toFixed(4);
    node.style.transform = 'translate3d(0,' + y.toFixed(2) + 'px,0) scale(' + s.toFixed(4) + ')';
    return true;
  }

  /* ---------- scene 1 ---------- */
  function scene1(t) {
    put(el('s1eyebrow'), P(t, 0.10, 0.45), (1 - eo(P(t, 0.10, 0.45))) * 18);

    var lg = P(t, 0.30, 0.60);
    put(el('s1logo'), eo(lg), 0, 0.90 + 0.10 * back(lg));

    var st = P(t, 0.50, 0.70);
    put(el('s1stage'), eo(st), (1 - eo(st)) * 26, 0.88 + 0.12 * eo(st));

    var arc = eo(P(t, 0.72, 0.85));
    el('s1orbit').style.setProperty('--arc', (-55 * (1 - arc)).toFixed(2) + 'deg');

    var s1 = back(P(t, 1.05, 0.45));
    put(el('s1sat1'), clamp01(P(t, 1.05, 0.30)), 0, 0.4 + 0.6 * s1);
    var s2 = back(P(t, 1.28, 0.45));
    put(el('s1sat2'), clamp01(P(t, 1.28, 0.30)), 0, 0.4 + 0.6 * s2);

    var sb = P(t, 0.95, 0.55);
    put(el('s1sub'), eo(sb), (1 - eo(sb)) * 16);
  }

  /* ---------- scene 2 ---------- */
  var TYPE_TEXT = 'celia royale';
  function scene2(t) {
    var head = P(t, 3.35, 0.55);
    put(el('s2head'), eo(head), (1 - eo(head)) * 26);

    var inP = eo(P(t, 3.50, 0.55));
    var outP = eo(P(t, 5.45, 0.40));
    put(el('s2form'), inP * (1 - outP), (1 - inP) * 44 - outP * 56, 1 - outP * 0.03);

    var chars = Math.floor(P(t, 4.05, 0.85) * TYPE_TEXT.length + 0.0001);
    var input = el('s2name');
    if (input.value.length !== chars) input.value = TYPE_TEXT.slice(0, chars);

    el('s2inst').value = t >= 4.95 ? 'knust-kumasi-campus' : '';
    el('s2type').value = t >= 5.15 ? 'four' : '';
    el('s2gender').value = t >= 5.30 ? 'Male' : '';
    el('s2sort').value = t >= 5.40 ? 'price_asc' : '';

    var cards = document.querySelectorAll('.s2-card');
    for (var i = 0; i < cards.length; i++) {
      var cp = eo(P(t, 5.85 + i * 0.13, 0.55));
      var node = cards[i];
      node.style.opacity = cp;
      node.style.transform = 'translate3d(0,' + ((1 - cp) * 70).toFixed(2) + 'px,0) scale(' + (0.97 + 0.03 * cp).toFixed(4) + ')';
    }
  }

  /* ---------- scene 3 ---------- */
  function scene3(t) {
    var l1 = eo(P(t, 8.15, 0.50));
    put(el('s3line1'), l1, (1 - l1) * 30);
    var l2 = eo(P(t, 8.38, 0.50));
    put(el('s3line2'), l2, (1 - l2) * 30);
    var nt = eo(P(t, 8.70, 0.55));
    put(el('s3note'), nt, (1 - nt) * 18);

    var inC = eo(P(t, 8.25, 0.55));
    var outC = eo(P(t, 9.05, 0.35));
    put(el('s3locked'), inC * (1 - outC), (1 - inC) * 46 - outC * 34, (0.97 + 0.03 * inC) * (1 - outC * 0.03));

    var inA = eo(P(t, 9.30, 0.55));
    put(el('s3active'), inA, (1 - inA) * 40, 0.96 + 0.04 * inA);

    /* click on "Pay GHS70.00 via Paystack" */
    var press = 1 - 0.05 * Math.sin(Math.PI * clamp01(P(t, 8.97, 0.20)));
    el('s3btn').style.transform = 'scale(' + press.toFixed(4) + ')';
    pointer(t, 8.55, 9.75, 8.60, 8.95, centerOf('s3btn'), { x: 1610, y: 880 }, 9.00);
  }

  /* ---------- scene 4 ---------- */
  function scene4(t) {
    var aIn = eo(P(t, 12.45, 0.50));
    var aOut = eo(P(t, 14.25, 0.35));
    put(el('s4headA'), aIn * (1 - aOut), (1 - aIn) * 24 - aOut * 22);

    var bIn = eo(P(t, 14.50, 0.55));
    put(el('s4headB'), bIn, (1 - bIn) * 24);

    var bk = eo(P(t, 12.50, 0.55));
    put(el('s4book'), bk, (1 - bk) * 54);

    var smIn = eo(P(t, 12.65, 0.55));
    var smOut = eo(P(t, 14.30, 0.35));
    put(el('s4summary'), smIn * (1 - smOut), (1 - smIn) * 54 - smOut * 40);

    var py = eo(P(t, 14.60, 0.55));
    put(el('s4pay'), py, (1 - py) * 54);

    var on = t >= 13.15;
    el('s4r1').checked = on;
    el('s4opt1').classList.toggle('is-on', on);

    var press = 1 - 0.05 * Math.sin(Math.PI * clamp01(P(t, 13.90, 0.20)));
    el('s4confirm').style.transform = 'scale(' + press.toFixed(4) + ')';

    var toast = eo(P(t, 15.70, 0.45));
    put(el('s4toast'), toast, (1 - toast) * 14);

    pointer(t, 13.35, 14.60, 13.40, 13.85, centerOf('s4confirm'), { x: 1600, y: 960 }, 13.90);
  }

  /* ---------- scene 5 ---------- */
  function scene5(t) {
    var ci = eo(P(t, 17.20, 0.50));
    var co = eo(P(t, 18.15, 0.35));
    put(el('s5conf'), ci * (1 - co), (1 - ci) * 34 - co * 40, (0.95 + 0.05 * ci) * (1 - co * 0.03));
    var pop = back(P(t, 17.45, 0.55));
    var icon = document.querySelector('.s5-icon');
    if (icon) icon.style.transform = 'scale(' + (0.7 + 0.3 * pop).toFixed(4) + ')';

    var lg = eo(P(t, 18.45, 0.55));
    put(el('s5logo'), lg, (1 - lg) * 22, 0.94 + 0.06 * lg);
    var hd = eo(P(t, 18.55, 0.55));
    put(el('s5head'), hd, (1 - hd) * 26);
    var ct = eo(P(t, 19.20, 0.55));
    put(el('s5cta'), ct, (1 - ct) * 20);
  }

  /* ---------- simulated pointer ---------- */
  var cursorNode = null;
  function pointer(t, appear, gone, moveA, moveB, target, from, clickAt) {
    if (!cursorNode) cursorNode = el('cursor');
    if (t < appear || t > gone) {
      cursorNode.style.opacity = 0;
      return;
    }
    var fade = Math.min(P(t, appear, 0.18), 1 - P(t, gone - 0.22, 0.22));
    var m = eo(P(t, moveA, moveB - moveA));
    var x = lerp(from.x, target.x, m);
    var y = lerp(from.y, target.y, m);
    var s = 1 - 0.16 * Math.sin(Math.PI * clamp01(P(t, clickAt - 0.06, 0.24)));
    cursorNode.style.opacity = clamp01(fade);
    cursorNode.style.transform = 'translate3d(' + (x - 23).toFixed(2) + 'px,' + (y - 23).toFixed(2) + 'px,0) scale(' + s.toFixed(4) + ')';
  }

  function seek(t) {
    t = Math.max(0, Math.min(DUR - 0.0001, t));
    for (var i = 0; i < SCENES.length; i++) {
      var sc = SCENES[i];
      if (wrapScene(sc, t)) {
        if (sc.id === 's1') scene1(t);
        else if (sc.id === 's2') scene2(t);
        else if (sc.id === 's3') scene3(t);
        else if (sc.id === 's4') scene4(t);
        else if (sc.id === 's5') scene5(t);
      }
    }
  }

  window.seek = seek;
  window.__videoReady = false;
  window.addEventListener('load', function () {
    var imgs = Array.prototype.slice.call(document.images);
    var fonts = document.fonts ? document.fonts.ready : Promise.resolve();
    Promise.all([fonts].concat(imgs.map(function (i) {
      return i.complete ? Promise.resolve() : new Promise(function (r) {
        i.addEventListener('load', r); i.addEventListener('error', r);
      });
    }))).then(function () {
      rectCache = {};
      seek(0);
      window.__videoReady = true;
    });
  });
})();
