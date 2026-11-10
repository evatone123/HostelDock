# HostelDock — /brag-slim launch video plan

**Run:** 2026-10-06-184116 → `brag-output/`
**Input:** project (Laravel + Livewire app, `C:\wamp64\www\studentroombookings`)
**Format:** landscape 1920×1080, 30fps, **21.0s** (630 frames)
**Tone:** `default` — punchy, playful, clean, soft transitions

## What it is

HostelDock is a Ghanaian student accommodation marketplace: students browse verified
hostels near their campus, pay a one-time GHS 70 booking-access token, pick a room,
and pay with Paystack or MTN MoMo.

- **Who for:** university students in Ghana (KNUST, KSTU, …) hunting for a room.
- **What it does for them:** replaces the scramble of accommodation season with one
  search → one token → one booking.
- **What sets it apart:** one payment unlocks booking in *any* hostel; MoMo + Paystack
  payout; verified listings with real distance-to-campus data.
- **Most specific/funniest claim:** "Pay a one-time booking access fee of GHS70.00.
  Once paid, you can book a room in any hostel." — a cover charge for the whole country.
- **One-line share caption:** see `share-copy.txt`.

## Visual identity (taken straight from the code)

- Canvas Cream `#F3F0EE`, Ink `#141413`, Signal Orange `#CF4500`, orbit orange `#F37338`,
  Clay `#9A3A0A`. Sofia Sans (self-hosted), weight 450 body / 500 headlines, −2% tracking.
- The site's own gestures: floating white pill nav, eyebrow dot labels, circular photo
  portrait with orbit arcs + satellite CTAs, ink-black 20px pill buttons, big radii only.
- **Reuse:** the real `style.css` / `docuforge-design.css` / `mastercard-design.css`,
  Bootstrap icon fonts, `logo.png`, `about/slider-imgs.jpg`, and hostel photography from
  `public/uploads/hostels` + `public/storage/thumbnails`. Real copy only.

## Data used (all from the repo)

- Hero: "Love where you stay" / "HostelDock" / "Browse verified hostels near your campus…".
- Search panel: Hostel Name, Institution, Room Type, Gender, Min/Max Price (GHS), Sort By.
- Listings: CELIA ROYALE HOSTEL (5.0, GHS3,600, 600M Walk), DK APPIAH HOSTEL
  (5.0, GHS3,500, 10 Minutes Drive), DESTINY VIEW HOSTEL (4.0, GHS3,500, 8KM to Campus) —
  all KNUST KUMASI CAMPUS, from `HostelSeeder`.
- Token: `BookingToken::PRICE_GHS = 70.00`, Paystack, real card copy from `token-buy`.
- Booking: room options "Four in a room" GHS3,600.00 / "Three in a room" GHS4,140.00
  (derived from seeder multipliers × Celia Royale starting price), "Confirm Booking".
- Payment: "Pay Online", badges `Card / Bank` (Paystack) and `MTN` with the real
  "*170#" approval-prompt line from `booking-confirmation`.
- Outro: "Booking Received!", then footer claim "Find a room that feels like home."

## Storyboard (21.0s)

| # | Time | Scene | On screen |
|---|------|-------|-----------|
| 1 | 0.0–3.2 | **Hook** | Eyebrow "• LOVE WHERE YOU STAY", `logo.png` punches in, circular hero portrait pops with orbit arcs + two satellites. Sub: "Verified student hostels in Ghana." |
| 2 | 3.2–8.0 | **Reveal — search** | Headline "Browse verified hostels near your campus." Real filter panel animates (typing hostel name → Institution flips to KNUST KUMASI CAMPUS → Gender → Sort). Panel lifts away, three real listing cards stagger in with photos, ratings, distances, GHS prices, View Detail. |
| 3 | 8.0–12.3 | **Highlight 1 — the token** | Type: "One token. Any hostel." Real *Unlock Booking Access* card: "Pay a one-time booking access fee of GHS70.00…" → simulated Paystack click → green "You have an active booking token" state. |
| 4 | 12.3–17.1 | **Highlight 2 — book & pay** | Real *Book Your Room* card: room category + room options, radio selects, ink "Confirm Booking" presses. Text A: "Reserve the room you love in minutes." Then payment card swaps in: "Pay Online" with `Card / Bank` + `MTN` badges and the real *170# prompt line. Text B: "Pay with Paystack or MTN MoMo." |
| 5 | 17.1–21.0 | **Punchline / outro** | "Booking Received!" + reference pops with green check → card lifts away → final lockup: logo, "Find a room that feels like home.", ink pill "Reserve a Room Today", `hosteldock.com`. |

**Transitions:** fast lift-and-fade out (~0.3s), then in (~0.45s) through the cream
background — never a crossfade over two busy layouts.

**Readability:** every line settles ≥0.3s/word from the moment it is fully on screen;
text is the only thing that holds still while UI moves around it.

## Music cue guidance

112 BPM, warm modern-electronic, key of F♯ minor (`F♯m – D – A – E`), sub bass + plucked
arpeggio + soft kick/hat, mixed low under the picture. Accents land on scene boundaries
at 3.2 / 8.0 / 12.3 / 17.1s: riser into 0.0 downbeat, whoosh on each transition, a soft
chime on the token activation (≈9.0s) and on "Booking Received!" (≈17.6s), button clicks
sits softly beneath the music. Tail resolves on the final lockup and fades by 21.0s.
