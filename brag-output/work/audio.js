/* HostelDock launch video â€” original score, synthesized to music.wav.
   112 BPM, F# minor (F#m - D - A - E), whooshes on scene cuts. */
'use strict';
const fs = require('fs');

const SR = 44100;
const DUR = 21.0;
const N = Math.round(SR * DUR);
const BPM = 112;
const BEAT = 60 / BPM;          // 0.535714
const BAR = BEAT * 4;           // 2.142857
const CUTS = [3.2, 8.0, 12.3, 17.1];

const dry = [new Float64Array(N), new Float64Array(N)];   // pad/bass/drums
const wet = [new Float64Array(N), new Float64Array(N)];   // plucks/chimes/clicks (delay bus)

const midi = (m) => 440 * Math.pow(2, (m - 69) / 12);
const clamp = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);

function add(bus, start, dur, fn) {
  const i0 = Math.max(0, Math.round(start * SR));
  const i1 = Math.min(N, Math.round((start + dur) * SR));
  for (let i = i0; i < i1; i++) {
    const lt = (i - i0) / SR;
    const [l, r] = fn(lt);
    bus[0][i] += l;
    bus[1][i] += r;
  }
}

const panOf = (p) => [Math.cos((p + 1) * Math.PI / 4), Math.sin((p + 1) * Math.PI / 4)];

/* ---------- voices ---------- */

// plucked string: sine stack with fast exponential decay
function pluck(bus, t, note, amp, pan, tau) {
  const f = midi(note);
  const [pl, pr] = panOf(pan);
  add(bus, t, Math.min(1.4, tau * 5), (lt) => {
    const e = Math.exp(-lt / tau);
    const click = Math.exp(-lt / 0.006) * 0.4;
    const s =
      Math.sin(2 * Math.PI * f * lt) +
      0.34 * Math.sin(4 * Math.PI * f * lt) +
      0.16 * Math.sin(6 * Math.PI * f * lt) +
      click * Math.sin(18 * Math.PI * f * lt);
    const v = s * e * amp;
    return [v * pl, v * pr];
  });
}

// warm pad chord: detuned sines, slow attack, gentle release
function pad(bus, t, dur, notes, amp) {
  const voices = [];
  for (const n of notes) voices.push({ f: midi(n), d: 1 + (Math.random() - 0.5) * 0.004 });
  add(bus, t, dur, (lt) => {
    const atk = clamp(lt / 0.45);
    const rel = clamp((dur - lt) / 0.55);
    const env = Math.min(atk, rel);
    let s = 0;
    for (const v of voices) {
      s += Math.sin(2 * Math.PI * v.f * v.d * lt) * 0.55 +
           0.22 * Math.sin(4 * Math.PI * v.f * lt) +
           0.08 * Math.sin(6 * Math.PI * v.f * lt);
    }
    s = (s / voices.length) * env * amp;
    return [s, s];
  });
}

// sub bass with soft saturation
function bass(bus, t, dur, note, amp) {
  const f = midi(note);
  add(bus, t, dur, (lt) => {
    const e = Math.min(1, lt / 0.02) * clamp((dur - lt) / 0.12);
    const s = Math.tanh(Math.sin(2 * Math.PI * f * lt) * 1.6) * e * amp;
    return [s, s];
  });
}

function kick(bus, t, amp) {
  add(bus, t, 0.42, (lt) => {
    const f = 118 * Math.exp(-lt / 0.045) + 44;
    const e = Math.exp(-lt / 0.14);
    const click = Math.exp(-lt / 0.004) * 0.5;
    const s = (Math.sin(2 * Math.PI * (118 * lt * Math.exp(-lt / 0.05) + 44 * lt)) * e + click) * amp;
    return [s, s];
  });
}

function hat(bus, t, amp, pan, bright) {
  const [pl, pr] = panOf(pan);
  let hp = 0, prev = 0;
  add(bus, t, 0.09, (lt) => {
    const n = Math.random() * 2 - 1;
    hp = 0.72 * (hp + n - prev);
    prev = n;
    const e = Math.exp(-lt / (bright ? 0.014 : 0.03));
    const v = hp * e * amp;
    return [v * pl, v * pr];
  });
}

function clap(bus, t, amp) {
  add(bus, t, 0.26, (lt) => {
    const n = Math.random() * 2 - 1;
    const body = Math.exp(-lt / 0.075);
    const burst = Math.exp(-Math.abs(lt - 0.012) / 0.006) + Math.exp(-Math.abs(lt - 0.03) / 0.006);
    const v = (n * body + n * burst * 0.5) * amp * 0.5;
    return [v * 1.0, v * 0.94];
  });
}

// transition whoosh: band of noise swelling into the cut
function whoosh(bus, t, amp) {
  const dur = 0.7;
  let lp = 0, lp2 = 0;
  add(bus, t, dur, (lt) => {
    const n = Math.random() * 2 - 1;
    const cut = 0.04 + 0.5 * Math.pow(clamp(lt / 0.42), 1.5) * (lt < 0.42 ? 1 : Math.exp(-(lt - 0.42) / 0.18));
    lp += (n - lp) * cut;
    lp2 += (lp - lp2) * cut;
    const env = lt < 0.32 ? Math.pow(lt / 0.32, 1.6) : Math.exp(-(lt - 0.32) / 0.16);
    const v = (lp - lp2) * 5.5 * env * amp;
    return [v * 1.05, v * 0.95];
  });
}

// low impact under each cut
function impact(bus, t, amp) {
  add(bus, t, 0.9, (lt) => {
    const f = 70 * Math.exp(-lt / 0.12) + 34;
    const e = Math.exp(-lt / 0.24);
    const s = Math.sin(2 * Math.PI * f * lt) * e * amp;
    return [s, s];
  });
}

// confirmation chime
function chime(bus, t, notes, amp) {
  for (let k = 0; k < notes.length; k++) {
    const f = midi(notes[k]);
    const delay = k * 0.085;
    add(bus, t + delay, 1.6, (lt) => {
      const e = Math.exp(-lt / (0.45 + k * 0.12));
      const shimmer = 1 + 0.004 * Math.sin(2 * Math.PI * 5.5 * lt);
      const s = (Math.sin(2 * Math.PI * f * shimmer * lt) +
                 0.3 * Math.sin(4 * Math.PI * f * lt) +
                 0.12 * Math.sin(6 * Math.PI * f * lt)) * e * amp * (1 - k * 0.15);
      return [s, s * 0.98];
    });
  }
}

// soft UI click
function click(bus, t, amp) {
  add(bus, t, 0.12, (lt) => {
    const e = Math.exp(-lt / 0.012);
    const s = (Math.sin(2 * Math.PI * 1240 * lt) * 0.7 + (Math.random() * 2 - 1) * 0.5) * e * amp;
    return [s, s];
  });
}

/* ---------- arrangement ---------- */

// chords: [root note (bass), chord tones]
const CH = [
  { at: 0.000, bass: 42, tones: [54, 57, 61] },   // F#m
  { at: 2.143, bass: 38, tones: [50, 54, 57] },   // D
  { at: 4.286, bass: 45, tones: [57, 61, 64] },   // A
  { at: 6.429, bass: 40, tones: [52, 56, 59] },   // E
  { at: 8.571, bass: 42, tones: [54, 57, 61] },   // F#m
  { at: 10.714, bass: 38, tones: [50, 54, 57] },  // D
  { at: 12.857, bass: 45, tones: [57, 61, 64] },  // A
  { at: 15.000, bass: 40, tones: [52, 56, 59] },  // E
  { at: 17.143, bass: 42, tones: [54, 57, 61] }   // F#m â€” outro holds
];

function chordAt(t) {
  let c = CH[0];
  for (const k of CH) if (t >= k.at - 1e-6) c = k;
  return c;
}

// 1. pad through the whole piece
for (let i = 0; i < CH.length; i++) {
  const c = CH[i];
  const end = i + 1 < CH.length ? CH[i + 1].at : DUR;
  const dur = end - c.at;
  const amp = 0.155 * (c.at < 3.2 ? 0.8 : 1);
  pad(dry, c.at, dur + 0.35, c.tones, amp);
}

// 2. sustained root bed so the groove never leaves a hole
for (let i = 0; i < CH.length; i++) {
  const c = CH[i];
  const end = i + 1 < CH.length ? CH[i + 1].at : DUR;
  const dur = end - c.at;
  if (dur < 0.2) continue;
  const amp = c.at < 3.2 ? 0.10 : c.at >= 17.143 ? 0.16 : 0.19;
  bass(dry, c.at, dur + 0.2, c.bass - 12, amp);
}

// 3. sub bass hits on the beat, from the first cut
for (let k = 0; k * BEAT < DUR; k++) {
  const t = k * BEAT;
  if (t < 3.15 || t > 20.6) continue;
  const c = chordAt(t);
  const onDownbeat = k % 4 === 0 || k % 4 === 2;
  if (!onDownbeat) continue;
  const amp = t > 17.1 ? 0.26 : 0.34;
  bass(dry, t, BEAT * (onDownbeat ? 0.92 : 0.5), c.bass - 12, amp);
  if (t >= 12.3 && t < 17.1 && k % 4 === 3) bass(dry, t, BEAT * 0.4, c.bass - 12, 0.22);
}

// 3. kick: beats 1 & 3 of every bar, from the first cut until the outro
for (let k = 0; k * BEAT < DUR; k++) {
  const t = k * BEAT;
  if (t < 3.15 || t > 19.6) continue;
  if (k % 4 !== 0 && k % 4 !== 2) continue;
  const amp = t < 8 ? 0.30 : t < 17.1 ? 0.38 : 0.24;
  kick(dry, t, amp);
}

// 4. hats: 8ths, from bar 2 to the outro
for (let k = 0; k * BEAT / 2 < DUR; k++) {
  const t = k * BEAT / 2;
  if (t < 4.2 || t > 19.4) continue;
  const accent = k % 2 === 0;
  let amp = accent ? 0.052 : 0.032;
  if (t > 17.1) amp *= 0.55;
  hat(dry, t, amp, accent ? 0.25 : -0.3, accent);
}

// 5. claps on 2 & 4, only in the "book & pay" section and late scene 5
for (let k = 0; k * BEAT < DUR; k++) {
  const t = k * BEAT;
  if (!((t >= 12.85 && t < 17.1) || (t >= 17.14 && t < 19.0))) continue;
  if (k % 4 !== 1 && k % 4 !== 3) continue;
  clap(dry, t, t < 17.1 ? 0.20 : 0.14);
}

// 6. pluck arp: 8ths cycling the chord tones, from the top
for (let k = 0; k * BEAT / 2 < DUR - 0.4; k++) {
  const t = k * BEAT / 2;
  if (t > 20.2) break;
  const c = chordAt(t);
  const idx = k % c.tones.length;
  const note = c.tones[idx] + (k % 8 >= 4 ? 12 : 0);
  let amp = 0.16;
  if (t < 3.2) amp = 0.10;
  else if (t < 8) amp = 0.15;
  else if (t < 17.1) amp = 0.185;
  else amp = 0.10;
  pluck(wet, t, note, amp, (k % 2 ? 0.3 : -0.3), t > 17.1 ? 0.42 : 0.22);
}

// 7. transition hits on every scene cut
for (const cut of CUTS) {
  whoosh(dry, cut - 0.34, 0.5);
  impact(dry, cut, 0.30);
}

// 8. story beats
click(wet, 9.0, 0.22);            // pay button
chime(wet, 9.2, [66, 73], 0.30);  // token unlocked
click(wet, 13.9, 0.22);           // confirm booking
click(wet, 15.6, 0.14);           // MoMo prompt
chime(wet, 17.6, [69, 78], 0.34); // booking received
chime(wet, 18.5, [73], 0.16);     // sparkle under the lockup

/* ---------- delay on the sparkle bus ---------- */
const DEL = Math.round(0.2 * SR);
const out = [new Float64Array(N), new Float64Array(N)];
for (let ch = 0; ch < 2; ch++) {
  const buf = new Float64Array(N);
  for (let i = 0; i < N; i++) {
    const d = i >= DEL ? buf[i - DEL] : 0;
    buf[i] = wet[ch][i] + d * 0.42;
    out[ch][i] = dry[ch][i] + wet[ch][i] * 0.86 + d * 0.34;
  }
}

/* ---------- master ---------- */
const fadeIn = 0.5, fadeOutStart = 20.35;
let peak = 0;
for (let i = 0; i < N; i++) {
  const t = i / SR;
  let g = Math.min(1, t / fadeIn);
  if (t > fadeOutStart) g *= Math.max(0, 1 - (t - fadeOutStart) / (DUR - fadeOutStart));
  for (let ch = 0; ch < 2; ch++) out[ch][i] = Math.tanh(out[ch][i] * 1.12) * g;
  peak = Math.max(peak, Math.abs(out[0][i]), Math.abs(out[1][i]));
}
const norm = 0.89 / Math.max(peak, 1e-6);

/* ---------- WAV ---------- */
const bytes = Buffer.alloc(44 + N * 4);
bytes.write('RIFF', 0);
bytes.writeUInt32LE(36 + N * 4, 4);
bytes.write('WAVE', 8);
bytes.write('fmt ', 12);
bytes.writeUInt32LE(16, 16);
bytes.writeUInt16LE(1, 20);
bytes.writeUInt16LE(2, 22);
bytes.writeUInt32LE(SR, 24);
bytes.writeUInt32LE(SR * 4, 28);
bytes.writeUInt16LE(4, 32);
bytes.writeUInt16LE(16, 34);
bytes.write('data', 36);
bytes.writeUInt32LE(N * 4, 40);
for (let i = 0; i < N; i++) {
  for (let ch = 0; ch < 2; ch++) {
    const v = Math.max(-1, Math.min(1, out[ch][i] * norm));
    bytes.writeInt16LE(Math.round(v * 32767), 44 + i * 4 + ch * 2);
  }
}
fs.writeFileSync(process.argv[2] || 'music.wav', bytes);
console.log('wrote', (process.argv[2] || 'music.wav'), (bytes.length / 1e6).toFixed(1) + ' MB', 'peak=' + peak.toFixed(3));

