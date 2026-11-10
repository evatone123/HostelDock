const { chromium } = require('playwright');
const path = require('path');
(async () => {
  const b = await chromium.launch({ headless: true, channel: 'chrome', args: ['--hide-scrollbars'] });
  const p = await b.newPage({ viewport: { width: 1920, height: 1080 } });
  await p.goto('file:///' + path.resolve(__dirname, 'site', 'video.html').replace(/\\/g, '/'));
  await p.waitForFunction('window.__videoReady===true');
  const r = await p.evaluate(() => {
    const out = {};
    out.fonts = Array.from(document.fonts).map((f) => f.family + ':' + f.status)
      .filter((v, i, a) => a.indexOf(v) === i);
    out.bi = document.fonts.check('16px "Bootstrap Icons"');
    out.fa = document.fonts.check('16px "Font Awesome 6 Free"');
    out.sofia450 = document.fonts.check('450 16px "Sofia Sans"');
    const el = document.querySelector('.bi');
    out.biContent = el ? getComputedStyle(el, '::before').content : null;
    out.imgs = Array.from(document.images).map((i) => i.getAttribute('src') + ':' + i.naturalWidth);
    out.imgOk = Array.from(document.images).every((i) => i.complete && i.naturalWidth > 0);
    return out;
  });
  console.log(JSON.stringify(r, null, 1));
  await b.close();
})().catch((e) => { console.error(e); process.exit(1); });
