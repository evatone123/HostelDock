const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const SITE = 'file:///' + path.resolve(__dirname, 'site', 'video.html').replace(/\\/g, '/');
const FRAMES = path.resolve(__dirname, 'frames');
const FPS = 30;
const DUR = 21.0;
const N = Math.round(DUR * FPS);

const CHECK_TIMES = [
  0.5, 1.4, 2.2, 2.95, 3.5, 4.2, 4.8, 5.3, 6.1, 6.6, 7.4, 7.85,
  8.5, 9.1, 9.6, 10.6, 11.9, 12.7, 13.4, 13.95, 14.5, 15.1, 15.8, 16.6,
  17.4, 17.9, 18.6, 19.4, 20.2, 20.8
];

async function open() {
  const launchOptions = {
    headless: true,
    args: ['--hide-scrollbars', '--force-device-scale-factor=1', '--font-render-hinting=none']
  };
  let browser;
  try {
    browser = await chromium.launch({ ...launchOptions, channel: 'chrome' });
  } catch (e) {
    console.log('channel chrome failed, trying bundled chromium:', e.message.split('\n')[0]);
    browser = await chromium.launch(launchOptions);
  }
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });
  await page.goto(SITE, { waitUntil: 'load' });
  await page.waitForFunction('window.__videoReady === true', null, { timeout: 30000 });
  return { browser, page };
}

async function layoutCheck(page) {
  const report = await page.evaluate((times) => {
    const out = [];
    function visible(node) {
      let n = node;
      while (n && n !== document.body) {
        const cs = getComputedStyle(n);
        if (parseFloat(cs.opacity) < 0.06 || cs.visibility === 'hidden' || cs.display === 'none') return false;
        n = n.parentElement;
      }
      return true;
    }
    function parseRGB(c) {
      const m = c.match(/rgba?\(([^)]+)\)/);
      if (!m) return null;
      const p = m[1].split(',').map(Number);
      return { r: p[0], g: p[1], b: p[2], a: p.length > 3 ? p[3] : 1 };
    }
    function lum(c) {
      const f = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
      return 0.2126 * f(c.r) + 0.7152 * f(c.g) + 0.0722 * f(c.b);
    }
    function bgOf(node) {
      let n = node, acc = null;
      while (n && n !== document.documentElement) {
        const cs = getComputedStyle(n);
        const c = parseRGB(cs.backgroundColor);
        if (c && c.a > 0.85) return c;
        n = n.parentElement;
      }
      return acc || { r: 243, g: 240, b: 238, a: 1 };
    }
    for (const t of times) {
      window.seek(t);
      const issues = [];
      const texts = [];
      document.querySelectorAll('.scene *').forEach((node) => {
        const txt = node.textContent.trim();
        const hasOwn = Array.from(node.childNodes).some((n) => n.nodeType === 3 && n.textContent.trim());
        if (!hasOwn || !visible(node)) return;
        const r = node.getBoundingClientRect();
        if (r.width < 2 || r.height < 2) return;
        const scene = node.closest('.scene').id;
        texts.push({ node, t: txt.slice(0, 46), x: r.x, y: r.y, w: r.width, h: r.height, scene });

        if (!node.classList.contains("mc-watermark") && (r.x < -2 || r.y < -2 || r.x + r.width > 1922 || r.y + r.height > 1082)) {
          issues.push(`OUT-OF-BOUNDS [${scene}] "${txt.slice(0, 40)}"`);
        }
        // clipped / overflowing text
        if (node.scrollWidth > node.clientWidth + 2 && getComputedStyle(node).overflow !== 'visible') {
          issues.push(`CLIPPED-X [${scene}] "${txt.slice(0, 40)}"`);
        }
        // contrast
        const cs = getComputedStyle(node);
        const fg = parseRGB(cs.color);
        const bg = bgOf(node);
        if (fg && fg.a > 0.5) {
          const l1 = lum(fg), l2 = lum(bg);
          const ratio = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
          const size = parseFloat(cs.fontSize);
          const bold = parseInt(cs.fontWeight, 10) >= 600;
          const need = (size >= 24 || (size >= 18.66 && bold)) ? 3 : 4.5;
          if (ratio < need) {
            issues.push(`LOW-CONTRAST ${ratio.toFixed(2)}<${need} [${scene}] "${txt.slice(0, 34)}" ${cs.color} on rgb(${bg.r},${bg.g},${bg.b})`);
          }
        }
        // overflow beyond parent box
        const p = node.parentElement;
        if (p) {
          const pr = p.getBoundingClientRect();
          if (r.right > pr.right + 8 || r.left < pr.left - 8) {
            if (!p.classList.contains('scene')) {
              issues.push(`OVERFLOWS-PARENT [${scene}] "${txt.slice(0, 34)}"`);
            }
          }
        }
      });
      for (let i = 0; i < texts.length; i++) {
        for (let j = i + 1; j < texts.length; j++) {
          const a = texts[i], b = texts[j];
          if (a.scene !== b.scene) continue;
          if (a.node.contains(b.node) || b.node.contains(a.node)) continue;
          const ox = Math.min(a.x + a.w, b.x + b.w) - Math.max(a.x, b.x);
          const oy = Math.min(a.y + a.h, b.y + b.h) - Math.max(a.y, b.y);
          if (ox > 6 && oy > 6 && ox * oy > 140) {
            issues.push(`OVERLAP [${a.scene}] "${a.t}" x "${b.t}" (${(ox * oy).toFixed(0)}px)`);
          }
        }
      }
      if (issues.length) out.push({ t, issues });
    }
    return out;
  }, CHECK_TIMES);

  if (!report.length) {
    console.log('LAYOUT CHECK: clean');
  } else {
    console.log('LAYOUT CHECK: issues');
    for (const r of report) {
      console.log(`  t=${r.t}s`);
      r.issues.forEach((i) => console.log('    - ' + i));
    }
  }
  return report;
}

async function main() {
  const mode = process.argv[2] || 'check';
  const { browser, page } = await open();

  if (mode === 'check') {
    await layoutCheck(page);
    await browser.close();
    return;
  }

  if (mode === 'stills') {
    const times = process.argv.slice(3).map(Number);
    fs.mkdirSync(path.resolve(__dirname, 'stills'), { recursive: true });
    for (const t of times) {
      await page.evaluate((tt) => window.seek(tt), t);
      await page.screenshot({ path: path.resolve(__dirname, 'stills', `t${t.toFixed(2)}.png`) });
    }
    console.log('stills written:', times.join(', '));
    await browser.close();
    return;
  }

  // full render
  fs.mkdirSync(FRAMES, { recursive: true });
  const from = Number(process.argv[3] || 0);
  const to = Number(process.argv[4] || N);
  const t0 = Date.now();
  for (let i = from; i < to; i++) {
    await page.evaluate((tt) => window.seek(tt), i / FPS);
    await page.screenshot({ path: path.join(FRAMES, `f${String(i).padStart(4, '0')}.png`) });
    if (i % 60 === 0) console.log(`frame ${i}/${N}  ${((Date.now() - t0) / 1000).toFixed(1)}s`);
  }
  console.log(`rendered ${to - from} frames in ${((Date.now() - t0) / 1000).toFixed(1)}s`);
  await browser.close();
}

main().catch((e) => { console.error(e); process.exit(1); });
