<?php
declare(strict_types=1);

$faviconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" role="img" aria-label="Na jakim VOD">
  <defs>
    <radialGradient id="sphere" cx="28%" cy="20%" r="78%">
      <stop offset="0%" stop-color="#f7f1e8"/>
      <stop offset="18%" stop-color="#ffd3a2"/>
      <stop offset="42%" stop-color="#ff8a3d"/>
      <stop offset="68%" stop-color="#6fe1d2"/>
      <stop offset="100%" stop-color="#10293d"/>
    </radialGradient>
    <radialGradient id="glow" cx="42%" cy="36%" r="60%">
      <stop offset="0%" stop-color="#6fe1d2" stop-opacity="0.5"/>
      <stop offset="55%" stop-color="#ff8a3d" stop-opacity="0.24"/>
      <stop offset="100%" stop-color="#07101a" stop-opacity="0"/>
    </radialGradient>
    <linearGradient id="ring" x1="12" y1="12" x2="52" y2="52">
      <stop offset="0%" stop-color="#ffd3a2"/>
      <stop offset="50%" stop-color="#6fe1d2"/>
      <stop offset="100%" stop-color="#ff8a3d"/>
    </linearGradient>
  </defs>
  <circle cx="32" cy="32" r="28" fill="url(#glow)"/>
  <circle cx="32" cy="32" r="23.5" fill="url(#sphere)"/>
  <circle cx="32" cy="32" r="24.5" fill="none" stroke="url(#ring)" stroke-width="1.4" opacity="0.75"/>
  <ellipse cx="24" cy="20" rx="8.2" ry="5.2" fill="#ffffff" opacity="0.44" transform="rotate(-24 24 20)"/>
  <path d="M49 40.5c-4.4 9-15.1 12.7-24.3 8.4 7.1.8 15.7-1.9 22-9.1.8-.9 2.8-.3 2.3.7Z" fill="#07101a" opacity="0.26"/>
</svg>
SVG;

$faviconHref = 'data:image/svg+xml,' . rawurlencode($faviconSvg);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="app-build" content="2026-05-08-home-news-fast-load-direct-filmweb">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

  <title>Na jakim VOD?</title>

  <meta
    name="description"
    content="Wyszukiwarka filmów i seriali z informacją, na jakich platformach VOD są dostępne."
  >

  <link
    rel="icon"
    type="image/svg+xml"
    href="<?= htmlspecialchars($faviconHref, ENT_QUOTES, 'UTF-8') ?>"
  >

  <style>
    :root {
      --bg: #081626;
      --panel: rgba(255, 248, 236, 0.1);
      --line: rgba(255, 255, 255, 0.12);
      --text: #f7f1e8;
      --muted: #c8c0b3;
      --accent: #ff8a3d;
      --accent-soft: #ffd3a2;
      --teal: #6fe1d2;
      --shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
      --radius: 28px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: "Trebuchet MS", "Segoe UI", sans-serif;
      color: var(--text);
      background:
        radial-gradient(circle at top left, rgba(255, 138, 61, 0.25), transparent 28%),
        radial-gradient(circle at top right, rgba(111, 225, 210, 0.18), transparent 30%),
        linear-gradient(145deg, #07101a 0%, #0b2233 54%, #10293d 100%);
    }

    body::before,
    body::after {
      content: "";
      position: fixed;
      z-index: -1;
      filter: blur(18px);
      pointer-events: none;
    }

    body::before {
      top: 6rem;
      right: 4rem;
      width: 18rem;
      height: 18rem;
      background: rgba(255, 138, 61, 0.18);
      border-radius: 44% 56% 52% 48%;
    }

    body::after {
      bottom: 3rem;
      left: 2rem;
      width: 14rem;
      height: 14rem;
      background: rgba(111, 225, 210, 0.14);
      border-radius: 58% 42% 46% 54%;
    }

    .shell {
      width: min(1180px, calc(100% - 2rem));
      margin: 0 auto;
      padding: 2rem 0 3rem;
    }

    .hero {
      position: relative;
      overflow: hidden;
      padding: 2rem;
      border: 1px solid var(--line);
      border-radius: calc(var(--radius) + 8px);
      background: linear-gradient(
        180deg,
        rgba(255, 248, 236, 0.12),
        rgba(255, 248, 236, 0.05)
      );
      backdrop-filter: blur(14px);
      box-shadow: var(--shadow);
    }

    .hero::after {
      content: "";
      position: absolute;
      top: -3rem;
      right: -3rem;
      width: 12rem;
      height: 12rem;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255, 138, 61, 0.26), transparent 68%);
    }

    h1 {
      margin: 1rem 0 0.8rem;
      max-width: none;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(2rem, 4.6vw, 5rem);
      line-height: 0.94;
      letter-spacing: -0.04em;
      white-space: nowrap;
      text-align: center;
    }

    .search-panel {
      margin-top: 1.4rem;
      padding: 1rem;
      border-radius: var(--radius);
      background: rgba(6, 18, 31, 0.58);
      border: 1px solid var(--line);
    }

    .search-grid {
      display: grid;
      gap: 0.9rem;
      grid-template-columns: minmax(0, 1fr) 160px 160px;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--accent-soft);
    }

    input,
    select,
    button {
      width: 100%;
      border: 0;
      border-radius: 18px;
      font: inherit;
    }

    input,
    select {
      padding: 1rem;
      background: rgba(255, 255, 255, 0.92);
      color: #081626;
      box-shadow: inset 0 0 0 1px rgba(8, 22, 38, 0.08);
    }

    select {
      appearance: none;
    }

    input:focus,
    select:focus {
      outline: 3px solid rgba(111, 225, 210, 0.45);
      outline-offset: 2px;
    }

    button {
      align-self: end;
      padding: 1rem 1.1rem;
      color: #081626;
      background: linear-gradient(135deg, var(--accent) 0%, #ffc26d 100%);
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
      box-shadow: 0 14px 28px rgba(255, 138, 61, 0.26);
    }

    button:hover {
      transform: translateY(-1px);
      box-shadow: 0 18px 34px rgba(255, 138, 61, 0.3);
    }

    button.secondary {
      background: linear-gradient(135deg, #6fe1d2 0%, #a4f7dd 100%);
      box-shadow: 0 14px 28px rgba(111, 225, 210, 0.22);
    }

    button.ghost {
      color: var(--text);
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: none;
    }

    button.ghost:hover {
      transform: none;
      box-shadow: none;
      background: rgba(255, 255, 255, 0.12);
    }

    .random-bar {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 160px;
      gap: 0.9rem;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .random-bar[hidden] {
      display: none;
    }

    .random-status {
      grid-column: 1 / -1;
      margin: 0.15rem 0 0;
      min-height: 56px;
      display: flex;
      align-items: center;
    }

    .random-status.hidden {
      display: none;
    }


    .callout {
      margin-top: 1.3rem;
      padding: 1rem 1.1rem;
      border-radius: 20px;
      border: 1px solid var(--line);
      background: rgba(255, 255, 255, 0.06);
      line-height: 1.6;
    }

    .callout.error {
      border-color: rgba(255, 138, 61, 0.4);
      background: rgba(255, 138, 61, 0.08);
    }

    .callout.info {
      border-color: rgba(111, 225, 210, 0.3);
      background: rgba(111, 225, 210, 0.08);
    }

    .callout.hidden,
    .status.hidden,
    .results.hidden {
      display: none;
    }

    .status {
      margin-top: 1.4rem;
      padding: 1rem 1.1rem;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid var(--line);
      color: var(--muted);
    }

    .status.is-loading {
      position: relative;
      display: flex;
      align-items: center;
      gap: 0.7rem;
      overflow: hidden;
      color: var(--text);
      font-weight: 700;
    }

    .status.is-loading::after {
      content: "";
      position: absolute;
      inset: 0;
      z-index: 0;
      transform: translateX(-120%);
      background: linear-gradient(
        90deg,
        transparent,
        rgba(111, 225, 210, 0.12),
        rgba(255, 138, 61, 0.1),
        transparent
      );
      animation: statusShimmer 1.6s ease-in-out infinite;
      pointer-events: none;
    }

    .status-loader-spinner {
      position: relative;
      z-index: 1;
      width: 1rem;
      height: 1rem;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.25);
      border-top-color: var(--teal);
      border-right-color: var(--accent);
      animation: spin 0.7s linear infinite;
      flex: 0 0 auto;
    }

    .loading-status-text {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: baseline;
      gap: 0.18rem;
    }

    .loading-status-dots {
      display: inline-flex;
      gap: 0.12rem;
      min-width: 1.1rem;
    }

    .loading-status-dot {
      animation: loadingDot 1.15s ease-in-out infinite;
    }

    .loading-status-dot:nth-child(2) {
      animation-delay: 0.16s;
    }

    .loading-status-dot:nth-child(3) {
      animation-delay: 0.32s;
    }

    .home-toggle-wrap {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      grid-auto-flow: row;
      align-items: start;
      gap: 0.8rem 1rem;
      margin: 1.15rem 0 0;
      animation: revealButton 0.45s ease both;
    }

    .home-toggle-wrap.hidden {
      display: none;
    }

    .random-shortcut {
      display: grid;
      gap: 0.8rem;
      min-width: 0;
      align-self: start;
    }

    .random-shortcut .home-toggle-button {
      min-height: 58px;
    }

    .random-shortcut .random-bar {
      grid-template-columns: 1fr;
      gap: 0.85rem;
      margin: 0;
      padding: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 22px;
      background:
        linear-gradient(180deg, rgba(255, 248, 236, 0.08), rgba(255, 248, 236, 0.04)),
        rgba(255, 255, 255, 0.05);
      box-shadow: 0 16px 42px rgba(0, 0, 0, 0.18);
      animation: revealSections 0.24s ease both;
    }

    .random-shortcut .random-bar[hidden] {
      display: none;
    }

    .random-shortcut .random-bar label {
      text-align: left;
    }

    .random-shortcut .random-bar button {
      min-height: 54px;
    }

    .home-toggle-button {
      min-width: 0;
      min-height: 58px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      width: 100%;
      overflow: hidden;
      color: var(--text);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 22px;
      background:
        linear-gradient(180deg, rgba(255, 248, 236, 0.08), rgba(255, 248, 236, 0.04)),
        rgba(255, 255, 255, 0.05);
      box-shadow: 0 16px 42px rgba(0, 0, 0, 0.18);
      letter-spacing: 0.02em;
      animation: none;
    }

    .home-toggle-button:hover {
      transform: translateY(-1px);
      background:
        linear-gradient(180deg, rgba(255, 248, 236, 0.12), rgba(255, 248, 236, 0.06)),
        rgba(255, 255, 255, 0.08);
      box-shadow: 0 18px 46px rgba(0, 0, 0, 0.22);
    }

    .home-toggle-button::after {
      content: "";
      width: 0.75rem;
      height: 0.75rem;
      border-right: 2px solid currentColor;
      border-bottom: 2px solid currentColor;
      transform: rotate(45deg) translateY(-2px);
      transition: transform 0.22s ease;
      flex: 0 0 auto;
    }

    .home-toggle-button[aria-expanded="true"]::after {
      transform: rotate(-135deg) translateY(-1px);
    }

    .home-toggle-button.is-loading {
      opacity: 0.95;
    }

    .calendar-panel {
      display: grid;
      gap: 1rem;
    }

    .calendar-controls {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 0.8rem;
    }

    .calendar-controls button {
      min-height: 54px;
    }

    .calendar-grid {
      position: relative;
      display: grid;
      gap: 1.15rem;
    }

    .calendar-grid.is-loading-news {
      padding-top: 3.25rem;
    }

    .calendar-month {
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 24px;
      background:
        linear-gradient(180deg, rgba(255, 248, 236, 0.08), rgba(255, 248, 236, 0.035)),
        rgba(255, 255, 255, 0.035);
    }

    .calendar-month-title {
      margin: 0;
      padding: 1rem 1rem 0.85rem;
      color: var(--accent-soft);
      font-size: 1rem;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    .calendar-scroll {
      overflow-x: auto;
    }

    .calendar-weekdays,
    .calendar-month-grid {
      min-width: 760px;
    }

    .calendar-weekdays {
      display: grid;
      grid-template-columns: repeat(7, minmax(0, 1fr));
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(6, 18, 31, 0.28);
    }

    .calendar-weekday {
      padding: 0.65rem 0.45rem;
      color: var(--muted);
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-align: center;
      text-transform: uppercase;
    }

    .calendar-month-grid {
      display: grid;
      grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .calendar-cell {
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 0.45rem;
      min-height: 132px;
      padding: 0.65rem;
      border-right: 1px solid rgba(255, 255, 255, 0.075);
      border-bottom: 1px solid rgba(255, 255, 255, 0.075);
      background: rgba(255, 255, 255, 0.035);
    }

    .calendar-cell:nth-child(7n) {
      border-right: 0;
    }

    .calendar-cell.is-outside {
      background: rgba(255, 255, 255, 0.018);
      color: rgba(255, 255, 255, 0.36);
    }

    .calendar-cell.is-today {
      box-shadow: inset 0 0 0 2px rgba(111, 225, 210, 0.58);
      background: rgba(255, 255, 255, 0.045);
    }

    .calendar-cell-date {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 1.85rem;
      height: 1.85rem;
      border-radius: 999px;
      color: var(--accent-soft);
      font-size: 0.82rem;
      font-weight: 800;
      line-height: 1;
      background: rgba(255, 255, 255, 0.06);
    }

    .calendar-cell.is-today .calendar-cell-date {
      color: #081626;
      background: var(--teal);
    }

    .calendar-cell.is-outside .calendar-cell-date {
      color: rgba(255, 255, 255, 0.34);
      background: rgba(255, 255, 255, 0.035);
    }

    .calendar-cell-items {
      display: grid;
      gap: 0.38rem;
      min-width: 0;
    }

    .calendar-premiere-chip {
      display: grid;
      gap: 0.12rem;
      min-width: 0;
      padding: 0.48rem 0.52rem;
      border-radius: 13px;
      color: var(--text);
      text-decoration: none;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.08);
      transition: transform 0.16s ease, background 0.16s ease, border-color 0.16s ease;
    }

    .calendar-premiere-chip:hover {
      transform: translateY(-1px);
      color: var(--text);
      background: rgba(255, 255, 255, 0.11);
      border-color: rgba(111, 225, 210, 0.28);
    }

    .calendar-premiere-type {
      color: var(--teal);
      font-size: 0.62rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      line-height: 1.1;
      text-transform: uppercase;
    }

    .calendar-premiere-chip-tv .calendar-premiere-type {
      color: var(--accent-soft);
    }

    .calendar-premiere-title {
      min-width: 0;
      color: var(--text);
      font-size: 0.78rem;
      font-weight: 700;
      line-height: 1.18;
      overflow-wrap: anywhere;
    }

    .calendar-premiere-more {
      appearance: none;
      display: inline-flex;
      align-items: center;
      justify-content: flex-start;
      width: 100%;
      min-height: auto;
      margin: 0.08rem 0 0;
      padding: 0.28rem 0.5rem;
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 10px;
      color: var(--muted);
      background: rgba(8, 22, 38, 0.24);
      box-shadow: none;
      font-size: 0.72rem;
      font-weight: 800;
      line-height: 1.3;
      text-align: left;
      cursor: pointer;
      transform: none;
    }

    .calendar-premiere-more:hover,
    .calendar-premiere-more:focus-visible {
      color: var(--text);
      border-color: rgba(111, 225, 210, 0.24);
      background: rgba(111, 225, 210, 0.1);
      box-shadow: none;
      transform: none;
    }

    .calendar-day-dialog-backdrop {
      position: fixed;
      inset: 0;
      z-index: 20;
      display: grid;
      place-items: center;
      padding: 1rem;
      background: rgba(3, 10, 18, 0.68);
      backdrop-filter: blur(10px);
    }

    .calendar-day-dialog {
      display: grid;
      gap: 0.9rem;
      width: min(100%, 520px);
      max-height: min(76vh, 680px);
      overflow: auto;
      padding: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 20px;
      background: rgba(21, 43, 58, 0.96);
      box-shadow: 0 24px 70px rgba(0, 0, 0, 0.42);
    }

    .calendar-day-dialog-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1rem;
    }

    .calendar-day-dialog-kicker {
      margin: 0 0 0.2rem;
      color: var(--accent-soft);
      font-size: 0.72rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .calendar-day-dialog-title {
      margin: 0;
      font-size: 1.2rem;
      line-height: 1.2;
    }

    .calendar-day-dialog-close {
      width: 2.4rem;
      min-height: 2.4rem;
      padding: 0;
      flex: 0 0 auto;
      border-radius: 999px;
      font-size: 1.4rem;
      line-height: 1;
    }

    .calendar-day-dialog-list {
      display: grid;
      gap: 0.48rem;
    }

    .calendar-load-more-wrap {
      display: flex;
      justify-content: center;
      margin-top: 0.2rem;
    }

    .calendar-load-more-wrap button {
      width: auto;
      min-width: 240px;
    }

    .calendar-load-more-wrap button.is-loading {
      pointer-events: none;
      opacity: 0.9;
    }

    .calendar-loader {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      min-height: 132px;
      padding: 1.1rem;
      overflow: hidden;
      border-radius: 22px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background:
        linear-gradient(180deg, rgba(255, 248, 236, 0.08), rgba(255, 248, 236, 0.025)),
        rgba(6, 18, 31, 0.58);
      color: var(--text);
      text-align: center;
      box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.08),
        0 18px 46px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(14px);
    }

    .calendar-loader::before {
      content: "";
      position: absolute;
      inset: -55%;
      z-index: 0;
      background:
        radial-gradient(circle at 25% 30%, rgba(111, 225, 210, 0.16), transparent 34%),
        radial-gradient(circle at 72% 42%, rgba(255, 138, 61, 0.13), transparent 36%);
      animation: calendarGlassGlow 3.2s ease-in-out infinite;
      pointer-events: none;
    }

    .calendar-loader::after {
      content: "";
      position: absolute;
      inset: 0;
      z-index: 0;
      transform: translateX(-125%);
      background: linear-gradient(
        100deg,
        transparent,
        rgba(255, 255, 255, 0.08),
        rgba(111, 225, 210, 0.08),
        rgba(255, 138, 61, 0.06),
        transparent
      );
      animation: calendarGlassShimmer 1.7s ease-in-out infinite;
      pointer-events: none;
    }

    .calendar-loader > span {
      position: relative;
      z-index: 1;
    }

    .calendar-loader-spinner {
      width: 1.05rem;
      height: 1.05rem;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.24);
      border-top-color: var(--teal);
      border-right-color: var(--accent);
      animation: spin 0.75s linear infinite;
      flex: 0 0 auto;
    }
    .home-sections {
      display: grid;
      gap: 1rem;
      margin-top: 1rem;
      animation: revealSections 0.45s ease both;
    }

    .home-sections.hidden {
      display: none;
    }

    .shelf {
      padding: 1rem;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: linear-gradient(
        180deg,
        rgba(255, 248, 236, 0.11),
        rgba(255, 248, 236, 0.05)
      );
      box-shadow: var(--shadow);
    }

    .shelf-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 0.9rem;
    }

    .section-title {
      margin: 0;
      font-size: 1.15rem;
    }

    .section-note {
      color: var(--accent-soft);
      font-size: 0.8rem;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      text-align: right;
      line-height: 1.35;
    }

    .mini-grid {
      position: relative;
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: 0.65rem;
      align-items: stretch;
      width: 100%;
    }

    .mini-grid.is-loading-news {
      padding-top: 3.25rem;
    }

    .mini-card {
      display: grid;
      grid-template-rows: auto 1fr;
      gap: 0.55rem;
      min-height: 100%;
      padding: 0.65rem;
      border-radius: 18px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(255, 255, 255, 0.05);
    }

    .mini-poster,
    .mini-poster-fallback {
      width: 100%;
      aspect-ratio: 2 / 3;
      border-radius: 16px;
      overflow: hidden;
    }

    .mini-poster {
      display: block;
      object-fit: cover;
      background: #102235;
    }

    .mini-poster.is-loading-poster {
      background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.08),
        rgba(255, 255, 255, 0.17),
        rgba(255, 255, 255, 0.08)
      );
      animation: posterPulse 1.45s ease-in-out infinite;
    }

    .mini-poster-fallback {
      position: relative;
      display: grid;
      place-items: center;
      padding: 0.8rem;
      background: rgba(255, 255, 255, 0.08);
      color: var(--muted);
      text-align: center;
      font-size: 0.82rem;
    }

    .mini-poster-fallback.is-loading-poster {
      isolation: isolate;
      background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.08),
        rgba(255, 255, 255, 0.16),
        rgba(255, 255, 255, 0.08)
      );
      color: var(--accent-soft);
      animation: posterPulse 1.45s ease-in-out infinite;
    }

    .mini-poster-fallback.is-loading-poster::after {
      content: "";
      position: absolute;
      inset: 0;
      z-index: -1;
      transform: translateX(-120%);
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.14),
        transparent
      );
      animation: shimmer 1.35s ease-in-out infinite;
    }

    .mini-body {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      min-height: 0;
    }

    .mini-title {
      margin: 0;
      font-size: 0.9rem;
      line-height: 1.2;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      overflow: hidden;
      min-height: 2.2em;
    }

    .mini-alt-title {
      margin: -0.05rem 0 0;
      color: var(--accent-soft);
      font-size: 0.74rem;
      line-height: 1.3;
      min-height: 1.3em;
    }

    .mini-link {
      display: inline-flex;
      width: fit-content;
      color: var(--teal);
      text-decoration: none;
      font-size: 0.8rem;
      font-weight: 700;
    }

    .mini-link:hover {
      text-decoration: underline;
    }

    .filmweb-link {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      width: fit-content;
    }

    .filmweb-link::before {
      content: "";
      display: inline-block;
      width: 1.45rem;
      height: 1.45rem;
      flex: 0 0 auto;
      border-radius: 0;
      background-color: transparent;
      background-image: url("https://www.icons101.com/icon_ico/id_74320/filmweb.ico");
      background-repeat: no-repeat;
      background-position: center;
      background-size: contain;
    }

    .mini-filmweb-logo {
      margin-top: auto;
      padding-top: 0.6rem;
      line-height: 0;
      align-self: flex-start;
    }

    .mini-filmweb-logo:hover {
      transform: translateY(-1px) scale(1.04);
      text-decoration: none;
    }

    .mini-filmweb-logo::before {
      width: 1.75rem;
      height: 1.75rem;
      filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.22));
    }

    .mini-filmweb-logo.is-loading-filmweb,
    .mini-filmweb-logo.is-disabled-filmweb {
      opacity: 0.45;
      pointer-events: none;
      filter: grayscale(1);
    }

    .card-poster-column {
      display: grid;
      align-content: start;
      justify-items: center;
      gap: 0.48rem;
    }

    .card-filmweb-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: auto;
      min-height: 0;
      padding: 0;
      border: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
      text-decoration: none;
      line-height: 0;
    }

    .card-filmweb-button:hover {
      transform: translateY(-1px) scale(1.04);
      background: transparent;
      box-shadow: none;
      text-decoration: none;
    }

    .card-filmweb-button.is-loading-filmweb,
    .card-filmweb-button.is-disabled-filmweb {
      opacity: 0.45;
      pointer-events: none;
      filter: grayscale(1);
    }

    .card-filmweb-button::before {
      width: 2.25rem;
      height: 2.25rem;
      filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.24));
    }

    .mini-genre {
      margin: 0;
      color: var(--muted);
      font-size: 0.76rem;
      line-height: 1.35;
      min-height: 2.05em;
    }

    .mini-note {
      margin: 0;
      color: var(--muted);
      font-size: 0.76rem;
      line-height: 1.35;
    }

    .mini-card .description-block {
      margin: 0.35rem 0 0;
      gap: 0.5rem;
    }

    .mini-card .description-toggle {
      padding: 0.58rem 0.75rem;
      border-radius: 14px;
      font-size: 0.78rem;
    }

    .mini-card .overview {
      font-size: 0.78rem;
      line-height: 1.45;
    }

    .mini-loading {
      display: grid;
      place-items: center;
      min-height: 220px;
      padding: 1rem;
      border-radius: 18px;
      border: 1px dashed rgba(255, 255, 255, 0.12);
      color: var(--muted);
      text-align: center;
      background: rgba(255, 255, 255, 0.04);
    }

    .news-loader {
      display: contents;
    }

    .news-loader-text {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.7rem;
      margin: 0;
      min-height: 42px;
      color: var(--muted);
      text-align: center;
      z-index: 2;
    }

    .news-loader-spinner {
      width: 1rem;
      height: 1rem;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.22);
      border-top-color: var(--teal);
      border-right-color: var(--accent);
      animation: spin 0.75s linear infinite;
      flex: 0 0 auto;
    }

    .news-loader-card {
      position: relative;
      display: grid;
      gap: 0.55rem;
      padding: 0.65rem;
      border-radius: 18px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(255, 255, 255, 0.05);
      animation: loaderFloat 1.8s ease-in-out infinite;
      animation-delay: calc(var(--loader-index, 0) * 90ms);
    }

    .news-loader-card::after {
      content: "";
      position: absolute;
      inset: 0;
      transform: translateX(-120%);
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.12),
        transparent
      );
      animation: shimmer 1.4s ease-in-out infinite;
      animation-delay: calc(var(--loader-index, 0) * 90ms);
      pointer-events: none;
    }

    .news-loader-poster,
    .news-loader-line {
      border-radius: 14px;
      background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.08),
        rgba(255, 255, 255, 0.16),
        rgba(255, 255, 255, 0.08)
      );
    }

    .news-loader-poster {
      width: 100%;
      aspect-ratio: 2 / 3;
    }

    .news-loader-line {
      height: 0.72rem;
    }

    .news-loader-line.short {
      width: 58%;
    }

    .news-loader-line.medium {
      width: 78%;
    }

    .shelf-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 0.8rem;
      margin-top: 1rem;
    }

    .shelf-actions button {
      width: auto;
      min-width: 220px;
    }

    .shelf-actions button.is-loading {
      pointer-events: none;
      opacity: 0.9;
    }

    .results {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(275px, 1fr));
      gap: 1rem;
      margin-top: 1.4rem;
    }

    .results.single-result {
      grid-template-columns: minmax(275px, 340px);
      justify-content: start;
    }

    .results-actions {
      grid-column: 1 / -1;
      display: flex;
      justify-content: center;
    }

    .results-actions button {
      width: auto;
      min-width: 220px;
    }

    .results-actions button.is-loading {
      pointer-events: none;
      opacity: 0.9;
    }

    .results-actions button.is-loading::before,
    .shelf-actions button.is-loading::before {
      content: "";
      display: inline-block;
      width: 0.9rem;
      height: 0.9rem;
      margin-right: 0.55rem;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.45);
      border-top-color: rgba(255, 255, 255, 0.95);
      animation: spin 0.7s linear infinite;
      vertical-align: -0.15rem;
      flex: 0 0 auto;
    }

    .card {
      display: grid;
      grid-template-columns: 88px minmax(0, 1fr);
      width: 100%;
      gap: 0.95rem;
      padding: 0.85rem;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: linear-gradient(
        180deg,
        rgba(255, 248, 236, 0.11),
        rgba(255, 248, 236, 0.05)
      );
      box-shadow: var(--shadow);
      animation: rise 0.55s ease both;
      animation-delay: calc(var(--index, 0) * 80ms);
    }

    .results.has-rendered .card {
      animation: none;
    }

    .card-content {
      min-width: 0;
      overflow: hidden;
    }

    .poster,
    .poster-fallback {
      width: 100%;
      aspect-ratio: 2 / 3;
      border-radius: 16px;
      overflow: hidden;
    }

    .poster {
      display: block;
      background: #102235;
      object-fit: cover;
    }

    .poster-fallback {
      display: grid;
      place-items: center;
      padding: 1rem;
      text-align: center;
      background:
        linear-gradient(160deg, rgba(111, 225, 210, 0.24), rgba(255, 138, 61, 0.24)),
        #112338;
      color: rgba(255, 255, 255, 0.78);
      font-size: 0.86rem;
    }

    .card-top {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-bottom: 0.55rem;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 0.34rem 0.65rem;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: var(--muted);
      font-size: 0.78rem;
    }

    .pill.highlight {
      color: #081626;
      background: var(--teal);
      border-color: transparent;
      font-weight: 700;
    }

    h2 {
      margin: 0;
      font-size: 1.25rem;
      line-height: 1.2;
    }

    .alt-title {
      margin: 0.35rem 0 0;
      color: var(--accent-soft);
      font-size: 0.92rem;
      line-height: 1.4;
    }

    .overview {
      margin: 0.55rem 0 0;
      color: var(--muted);
      line-height: 1.55;
    }

    .genre-line {
      margin: 0.5rem 0 0;
      color: var(--accent-soft);
      font-size: 0.84rem;
      line-height: 1.45;
    }

    .description-block {
      display: grid;
      justify-items: start;
      gap: 0.7rem;
      margin: 0.8rem 0 0.9rem;
    }

    .description-block .overview {
      display: none;
      margin-top: 0;
      padding-top: 0.75rem;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .description-block .overview.is-expanded {
      display: block;
    }

    .description-toggle {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: auto;
      max-width: 100%;
      margin-top: 0;
      padding: 0.72rem 0.95rem;
      border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: rgba(255, 255, 255, 0.08);
      color: var(--text);
      box-shadow: none;
      font-size: 0.86rem;
      font-weight: 700;
      letter-spacing: 0.01em;
      white-space: nowrap;
    }

    .description-toggle:hover {
      transform: none;
      box-shadow: none;
      background: rgba(255, 255, 255, 0.12);
    }

    .groups {
      display: grid;
      gap: 0.62rem;
      width: 100%;
      min-width: 0;
      margin-top: 0.05rem;
    }

    .group-title {
      margin: 0 0 0.34rem;
      color: var(--accent-soft);
      font-size: 0.76rem;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    .provider-list {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-start;
      gap: 0.36rem;
      width: 100%;
      min-width: 0;
      overflow: hidden;
    }

    .provider-loading {
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      padding: 0.8rem 0.95rem;
      background: rgba(255, 255, 255, 0.06);
      color: var(--accent-soft);
    }

    .provider-loading::after {
      content: "";
      position: absolute;
      inset: 0;
      transform: translateX(-120%);
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.12),
        transparent
      );
      animation: shimmer 1.35s ease-in-out infinite;
      pointer-events: none;
    }

    .provider {
      display: inline-flex;
      align-items: center;
      gap: 0.36rem;
      flex: 0 1 auto;
      max-width: 100%;
      min-width: 0;
      min-height: 31px;
      padding: 0.24rem 0.48rem 0.24rem 0.3rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.08);
      color: var(--text);
      font-size: 0.78rem;
      line-height: 1.15;
      text-decoration: none;
    }

    .provider img {
      width: 22px;
      height: 22px;
      border-radius: 7px;
      object-fit: contain;
      background: rgba(255, 255, 255, 0.16);
      flex: 0 0 auto;
    }

    .provider span {
      min-width: 0;
      max-width: 100%;
      white-space: normal;
      overflow-wrap: anywhere;
      word-break: normal;
    }

    .provider-link {
      display: inline-flex;
      margin-top: 0.95rem;
      color: var(--teal);
      text-decoration: none;
      font-size: 0.92rem;
      font-weight: 700;
    }

    .provider-link:hover {
      text-decoration: underline;
    }

    @keyframes rise {
      from {
        opacity: 0;
        transform: translateY(14px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }

    @keyframes shimmer {
      from {
        transform: translateX(-120%);
      }

      to {
        transform: translateX(120%);
      }
    }

    @keyframes statusShimmer {
      from {
        transform: translateX(-120%);
      }

      to {
        transform: translateX(120%);
      }
    }

    @keyframes loadingDot {
      0%,
      80%,
      100% {
        opacity: 0.25;
        transform: translateY(0);
      }

      40% {
        opacity: 1;
        transform: translateY(-3px);
      }
    }

    @keyframes loaderFloat {
      0%,
      100% {
        transform: translateY(0);
        opacity: 0.72;
      }

      50% {
        transform: translateY(-4px);
        opacity: 1;
      }
    }

    @keyframes posterPulse {
      0%,
      100% {
        opacity: 0.72;
      }

      50% {
        opacity: 1;
      }
    }

    @keyframes revealButton {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes revealSections {
      from {
        opacity: 0;
        transform: translateY(12px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes calendarGlassShimmer {
      from {
        transform: translateX(-125%);
      }

      to {
        transform: translateX(125%);
      }
    }

    @keyframes calendarGlassGlow {
      0%,
      100% {
        transform: rotate(0deg) scale(1);
        opacity: 0.75;
      }

      50% {
        transform: rotate(8deg) scale(1.04);
        opacity: 1;
      }
    }
    /* random-panel-layout-polish:start */
    .home-toggle-wrap {
      align-items: start;
    }

    .random-shortcut {
      display: contents;
    }

    .home-toggle-wrap .random-bar {
      grid-column: 1 / -1;
      width: 100%;
      max-width: none;
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 160px;
      align-items: end;
      gap: 0.9rem;
      margin: 0;
      padding: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 24px;
      background:
        linear-gradient(180deg, rgba(255, 248, 236, 0.08), rgba(255, 248, 236, 0.04)),
        rgba(255, 255, 255, 0.05);
      box-shadow: 0 16px 42px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(14px);
      animation: revealSections 0.24s ease both;
    }

    .home-toggle-wrap .random-bar[hidden] {
      display: none;
    }

    .home-toggle-wrap .random-bar label {
      text-align: left;
    }

    .home-toggle-wrap .random-bar button {
      min-height: 54px;
    }

    @media (max-width: 860px) {
      .home-toggle-wrap .random-bar {
        grid-template-columns: 1fr;
      }
    }
    /* random-panel-layout-polish:end */
    @media (prefers-reduced-motion: reduce) {
      .news-loader-spinner,
      .news-loader-card,
      .news-loader-card::after,
      .mini-poster.is-loading-poster,
      .mini-poster-fallback.is-loading-poster,
      .mini-poster-fallback.is-loading-poster::after,
      .provider-loading::after,
      .results-actions button.is-loading::before,
      .shelf-actions button.is-loading::before,
      .status-loader-spinner,
      .status.is-loading::after,
      .loading-status-dot,
      .calendar-loader::before,
      .calendar-loader::after,
      .calendar-loader-spinner,
      .home-toggle-wrap,
      .home-sections,
      .card {
        animation: none;
      }
    }

    @media (max-width: 860px) {
      .search-grid,
      .random-bar,
      .home-toggle-wrap {
        grid-template-columns: 1fr;
      }

      .mini-grid {
        grid-template-columns: repeat(auto-fill, minmax(128px, 1fr));
      }
    }

    @media (max-width: 640px) {
      .shell {
        width: min(100% - 1rem, 1180px);
        padding-top: 1rem;
      }

      .hero {
        padding: 1.2rem;
        border-radius: 24px;
      }

      h1 {
        white-space: normal;
      }

      .shelf-head {
        align-items: flex-start;
        flex-direction: column;
      }

      .section-note {
        text-align: left;
      }

      .card {
        grid-template-columns: 1fr;
      }

      .card-content {
        width: 100%;
      }

      .poster,
      .poster-fallback {
        width: min(180px, 48%);
      }

      .card-poster-column {
        justify-items: start;
      }

      .card-filmweb-button {
        width: min(180px, 48%);
        justify-content: center;
      }

      .provider-list {
        overflow: visible;
      }

      .provider {
        max-width: 100%;
      }

    }
  </style>
</head>

<body>
  <main class="shell">
    <section class="hero">
      <h1 id="site-title" style="cursor: pointer;">Na jakim VOD to jest?</h1>

      <form id="search-form" class="search-panel">
        <div class="search-grid">
          <div>
            <label for="q">Tytuł filmu lub serialu</label>
            <input
              id="q"
              name="q"
              type="text"
              placeholder="np. Fallout, Diuna, Severance"
              required
            >
          </div>

          <div>
            <label>&nbsp;</label>
            <button type="submit">Szukaj</button>
          </div>

          <div>
            <label>&nbsp;</label>
            <button id="clear-results" class="ghost" type="button">Wyczyść</button>
          </div>
        </div>
      </form>

      <div id="message-box" class="callout hidden" aria-live="polite"></div>
      <div id="status-box" class="status hidden" aria-live="polite"></div>
    </section>

    <div id="home-toggle-wrap" class="home-toggle-wrap">
      <button
        id="toggle-home-news"
        class="home-toggle-button"
        type="button"
        aria-expanded="false"
        aria-controls="home-sections"
      >
        Pokaż nowości
      </button>
      <button
        id="toggle-release-calendar"
        class="home-toggle-button"
        type="button"
        aria-expanded="false"
        aria-controls="release-calendar"
      >
        Kalendarz premier
      </button>
      <div class="random-shortcut">
  <button
          id="toggle-random-panel"
          class="home-toggle-button"
          type="button"
          aria-expanded="false"
          aria-controls="random-bar"
        >
          Rozwiń losowanie
        </button>



        <div id="random-bar" class="random-bar random-shortcut-panel" hidden>
          <div>
            <label for="random-platform">Losuj z platformy</label>
            <select id="random-platform">
              <option value="all">Dowolna platforma</option>
              <option value="netflix">Netflix</option>
              <option value="hbomax">HBO Max</option>
            </select>
          </div>

          <div>
            <label for="random-type">Typ</label>
            <select id="random-type">
              <option value="all">Film lub serial</option>
              <option value="movie">Film</option>
              <option value="tv">Serial</option>
            </select>
          </div>

          <div>
            <label>&nbsp;</label>
            <button id="random-pick" class="secondary" type="button">Losuj</button>
          </div>

          <div
            id="random-status-box"
            class="status random-status hidden"
            aria-live="polite"
          ></div>
        </div>
      </div>
</div>

    <section id="home-sections" class="home-sections hidden">
      <section class="shelf">
        <div class="shelf-head">
          <h2 class="section-title">Nowości na Netflix</h2>
          <span class="section-note">Ostatnie 14 dni • wyświetlane: tylko filmy</span>
        </div>

        <div id="netflix-shelf" class="mini-grid"></div>
      </section>

      <section class="shelf">
        <div class="shelf-head">
          <h2 class="section-title">Nowości na HBO Max</h2>
          <span class="section-note">Ostatnie 14 dni • wyświetlane: tylko filmy</span>
        </div>

        <div id="hbomax-shelf" class="mini-grid"></div>
      </section>
    </section>

    <section id="release-calendar" class="home-sections hidden">
      <section class="shelf calendar-panel">
        <div class="shelf-head">
          <h2 class="section-title">Kalendarz premier</h2>
          <span id="release-calendar-note" class="section-note">Nadchodzące 45 dni</span>
        </div>

        <div class="calendar-controls">
          <button
            id="show-all-premieres"
            class="secondary"
            type="button"
            data-role="load-premieres"
            data-kind="all"
          >
            Pokaż filmy i seriale
          </button>

          <button
            id="show-movie-premieres"
            class="ghost"
            type="button"
            data-role="load-premieres"
            data-kind="movie"
          >
            Pokaż premiery filmów
          </button>

          <button
            id="show-tv-premieres"
            class="ghost"
            type="button"
            data-role="load-premieres"
            data-kind="tv"
          >
            Pokaż premiery seriali
          </button>
        </div>

        <div id="release-calendar-grid" class="calendar-grid">
          <div class="mini-loading">Wybierz typ premier do wyświetlenia.</div>
        </div>
      </section>
    </section>

    <section
      id="results"
      class="results hidden"
      aria-label="Wyniki wyszukiwania"
    ></section>
  </main>

  <script>
    const LOCAL_API_ENDPOINT = "tmdb-proxy.php";
    const FILMWEB_API_ENDPOINT = "filmweb-proxy.php";
    const UPFLIX_API_ENDPOINT = "upflix-proxy.php";
    const TMDB_IMAGE_BASE = "https://image.tmdb.org/t/p";
    const DEFAULT_REGION = "PL";
    const DEFAULT_LANGUAGE = "pl-PL";
    const RESULTS_BATCH_SIZE = 6;
    const SEARCH_PAGE_LIMIT = 2;
    const HOME_GRID_SIZE = 6;
    const HOME_SHOW_MORE_STEP = 6;
    const CALENDAR_DAYS_AHEAD = 45;
    const CALENDAR_PAGE_LIMIT = 6;
    const CALENDAR_GRID_SIZE = 80;
    const SMALL_COLLECTION_INSERT_LIMIT = 12;
    const RANDOM_MAX_PAGES = 20;
    const MAX_TYPO_DISTANCE = 2;

    const PLATFORM_FILTERS = {
      all: {
        label: "Dowolna platforma",
        providerId: null,
      },
      netflix: {
        label: "Netflix",
        providerId: 8,
      },
      hbomax: {
        label: "HBO Max",
        providerId: 1899,
      },
    };

    const form = document.getElementById("search-form");
    const queryInput = document.getElementById("q");
    const resultsEl = document.getElementById("results");
    const messageBox = document.getElementById("message-box");
    const statusBox = document.getElementById("status-box");
    const clearResultsButton = document.getElementById("clear-results");
    const toggleRandomPanelButton = document.getElementById("toggle-random-panel");
    const randomBarEl = document.getElementById("random-bar");
    const randomPlatformSelect = document.getElementById("random-platform");
    const randomTypeSelect = document.getElementById("random-type");
    const randomPickButton = document.getElementById("random-pick");
    const randomStatusBox = document.getElementById("random-status-box");
    const homeSectionsEl = document.getElementById("home-sections");
    const homeToggleWrapEl = document.getElementById("home-toggle-wrap");
    const toggleHomeNewsButton = document.getElementById("toggle-home-news");
    const toggleReleaseCalendarButton = document.getElementById("toggle-release-calendar");
    const releaseCalendarEl = document.getElementById("release-calendar");
    const releaseCalendarGridEl = document.getElementById("release-calendar-grid");
    const releaseCalendarNoteEl = document.getElementById("release-calendar-note");
    const showMoviePremieresButton = document.getElementById("show-movie-premieres");
    const showTvPremieresButton = document.getElementById("show-tv-premieres");
    const showAllPremieresButton = document.getElementById("show-all-premieres");
    const hasReleaseCalendar = Boolean(
      toggleReleaseCalendarButton
      && releaseCalendarEl
      && releaseCalendarGridEl
      && releaseCalendarNoteEl
      && showMoviePremieresButton
      && showTvPremieresButton
      && showAllPremieresButton
    );
    const netflixShelfEl = document.getElementById("netflix-shelf");
    const hboMaxShelfEl = document.getElementById("hbomax-shelf");

    function listen(element, eventName, handler) {
      if (!element) {
        return;
      }

      element.addEventListener(eventName, handler);
    }

    let homeLoaded = false;
    let homeNewsOperationId = 0;

    const filmwebMatchCache = new Map();
    const filmwebNewsMatchCache = new Map();
    const localizedNewsItemCache = new Map();
    const enrichedSearchResultCache = new Map();
    const movieCollectionCache = new Map();
    const tmdbProviderDataCache = new Map();
    const tmdbNewsMatchCache = new Map();

    let genreMapsPromise = null;
    let activeSearchState = null;
    let searchOperationId = 0;

    const releaseCalendarState = {
      activeKind: null,
      movie: {
        months: [],
        loading: false,
        error: "",
      },
      tv: {
        months: [],
        loading: false,
        error: "",
      },
      all: {
        months: [],
        loading: false,
        error: "",
      },
    };
    const homeShelfState = {
      netflix: {
        rawItems: [],
        movieItems: [],
        tvItems: [],
        enrichedMovies: [],
        enrichedTv: [],
        visibleMovieCount: HOME_GRID_SIZE,
        visibleTvCount: 0,
        showTv: false,
        loading: false,
        error: "",
      },
      hbomax: {
        rawItems: [],
        movieItems: [],
        tvItems: [],
        enrichedMovies: [],
        enrichedTv: [],
        visibleMovieCount: HOME_GRID_SIZE,
        visibleTvCount: 0,
        showTv: false,
        loading: false,
        error: "",
      },
    };

    const compactProviderNames = {
      "Amazon Prime Video": "Prime Video",
      "Prime Video": "Prime Video",
      "Amazon Video": "Prime Video",
      "HBO Max": "HBO Max",
      "Max": "Max",
      "Netflix": "Netflix",
      "Player": "Player",
    };

    const filmwebProviderLogos = {
      "Netflix": "https://fwcdn.pl/vodp/2_5.1.svg",
      "HBO Max": "https://fwcdn.pl/vodp/1_14.1.svg",
      "Max": "https://fwcdn.pl/vodp/1_14.1.svg",
      "Disney Plus": "https://fwcdn.pl/vodp/42_4.1.svg",
      "Disney+": "https://fwcdn.pl/vodp/42_4.1.svg",
      "Amazon Prime Video": "https://fwcdn.pl/vodp/8_3.1.svg",
      "Prime Video": "https://fwcdn.pl/vodp/8_3.1.svg",
      "Amazon Video": "https://fwcdn.pl/vodp/8_3.1.svg",
      "Player": "https://fwcdn.pl/vodp/6_6.1.svg",
      "Canal+": "https://fwcdn.pl/vodp/20_3.1.svg",
      "CANAL+": "https://fwcdn.pl/vodp/20_3.1.svg",
      "Canal+ Online": "https://fwcdn.pl/vodp/20_3.1.svg",
      "SkyShowtime": "https://fwcdn.pl/vodp/44_3.1.svg",
      "Apple TV Plus": "https://fwcdn.pl/vodp/10_2.1.svg",
      "Apple TV+": "https://fwcdn.pl/vodp/10_2.1.svg",
      "Polsat Box Go": "https://fwcdn.pl/vodp/34_6.1.svg",
      "TVP VOD": "https://fwcdn.pl/vodp/7_8.1.svg",
      "CDA Premium": "https://fwcdn.pl/vodp/65.1.svg",
      "MEGOGO": "https://fwcdn.pl/vodp/50_4.1.svg",
    };

    function providerDisplayName(name) {
      const label = String(name || "Nieznany serwis").trim();

      return compactProviderNames[label] || label;
    }

    function getTodayIso() {
      return new Date().toISOString().slice(0, 10);
    }

    async function tmdbRequest(path, params) {
      const url = new URL(LOCAL_API_ENDPOINT, window.location.href);

      url.searchParams.set("path", path);

      Object.entries(params || {}).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== "") {
          url.searchParams.set(key, value);
        }
      });

      const response = await fetch(url.toString(), {
        method: "GET",
        headers: {
          "Accept": "application/json",
        },
      });

      const text = await response.text();
      let payload = null;

      if (text) {
        try {
          payload = JSON.parse(text);
        } catch (error) {
          throw new Error("TMDb zwróciło nieprawidłowy JSON.");
        }
      }

      if (!response.ok) {
        const message = payload && (payload.error || payload.status_message)
          ? (payload.error || payload.status_message)
          : "Nie udało się pobrać danych z TMDb.";

        throw new Error(message);
      }

      return payload || {};
    }

    async function upflixRequest(platform) {
      const url = new URL(UPFLIX_API_ENDPOINT, window.location.href);

      url.searchParams.set("platform", platform);
      url.searchParams.set("limit", "300");

      const response = await fetch(url.toString(), {
        method: "GET",
        headers: {
          "Accept": "application/json",
        },
      });

      const payload = await response.json().catch(() => null);

      if (!response.ok) {
        throw new Error(
          payload && payload.error
            ? payload.error
            : "Nie udało się pobrać nowości z Upflix."
        );
      }

      return payload || {};
    }

    function imageUrl(path, size) {
      return path ? `${TMDB_IMAGE_BASE}/${size}${path}` : null;
    }

    function mediaTitle(item) {
      return item.title || item.name || item.original_title || item.original_name || "Bez tytułu";
    }

    function englishTitleForDisplay(item, displayTitle, fallbackTitle = "") {
      const englishTitle = String(
        fallbackTitle || (
          item && item.original_language === "en"
            ? (item.original_title || item.original_name || "")
            : ""
        )
      ).trim();

      if (!englishTitle) {
        return "";
      }

      return normalizedCompactText(englishTitle) === normalizedCompactText(displayTitle)
        ? ""
        : englishTitle;
    }

    function mediaYear(item) {
      const date = item.release_date || item.first_air_date || "";
      return /^\d{4}/.test(date) ? date.slice(0, 4) : null;
    }

    function mediaReleaseDate(item) {
      return item.release_date || item.first_air_date || "";
    }

    function hasPremiered(item) {
      const releaseDate = mediaReleaseDate(item);

      if (!/^\d{4}-\d{2}-\d{2}$/.test(releaseDate)) {
        return true;
      }

      return releaseDate <= getTodayIso();
    }

    function genreNamesFromObjects(item) {
      if (!item || !Array.isArray(item.genres)) {
        return [];
      }

      return item.genres
        .map((genre) => (genre && typeof genre.name === "string" ? genre.name.trim() : ""))
        .filter(Boolean);
    }

    
    function sortByEarliestRelease(left, right) {
      const leftTime = Date.parse(mediaReleaseDate(left) || "9999-12-31");
      const rightTime = Date.parse(mediaReleaseDate(right) || "9999-12-31");

      if (leftTime !== rightTime) {
        return leftTime - rightTime;
      }

      return (Number(left.id) || 0) - (Number(right.id) || 0);
    }

    function normalizeSearchText(value) {
      return String(value || "")
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, " ")
        .trim();
    }

    
    
    async function loadGenreMaps() {
      if (genreMapsPromise) {
        return genreMapsPromise;
      }

      genreMapsPromise = Promise.all([
        tmdbRequest("/genre/movie/list", { language: DEFAULT_LANGUAGE }),
        tmdbRequest("/genre/tv/list", { language: DEFAULT_LANGUAGE }),
      ])
        .then(([moviePayload, tvPayload]) => ({
          movie: new Map((moviePayload.genres || []).map((genre) => [Number(genre.id), genre.name])),
          tv: new Map((tvPayload.genres || []).map((genre) => [Number(genre.id), genre.name])),
        }))
        .catch((error) => {
          genreMapsPromise = null;
          throw error;
        });

      return genreMapsPromise;
    }

    function genreNamesForItem(item, genreMaps) {
      const explicitGenres = genreNamesFromObjects(item);

      if (explicitGenres.length) {
        return explicitGenres.slice(0, 3);
      }

      const genreIds = Array.isArray(item && item.genre_ids) ? item.genre_ids : [];
      const genreMap = item && item.media_type === "tv" ? genreMaps.tv : genreMaps.movie;

      return genreIds
        .map((genreId) => genreMap.get(Number(genreId)) || "")
        .filter(Boolean)
        .slice(0, 3);
    }

    function renderGenreLine(genres, className = "genre-line") {
      if (!Array.isArray(genres) || genres.length === 0) {
        return "";
      }

      return `<p class="${className}">${escapeHtml(genres.join(" • "))}</p>`;
    }

    function normalizedCompactText(value) {
      return normalizeSearchText(value).replace(/\s+/g, "");
    }

    function mediaYearNumber(item) {
      const year = Number.parseInt(mediaYear(item) || "", 10);
      return Number.isFinite(year) ? year : null;
    }

    function candidateTitles(item) {
      return Array.from(new Set([
        item.title,
        item.name,
        item.original_title,
        item.original_name,
      ].filter(Boolean).map((title) => String(title).trim()).filter(Boolean)));
    }

    function boundedLevenshtein(left, right, maxDistance = MAX_TYPO_DISTANCE) {
      const source = String(left || "");
      const target = String(right || "");

      if (source === target) {
        return 0;
      }

      if (!source) {
        return target.length;
      }

      if (!target) {
        return source.length;
      }

      if (Math.abs(source.length - target.length) > maxDistance) {
        return maxDistance + 1;
      }

      let previous = Array.from({ length: target.length + 1 }, (_, index) => index);

      for (let sourceIndex = 1; sourceIndex <= source.length; sourceIndex += 1) {
        const current = [sourceIndex];
        let rowMin = current[0];

        for (let targetIndex = 1; targetIndex <= target.length; targetIndex += 1) {
          const substitutionCost = source[sourceIndex - 1] === target[targetIndex - 1] ? 0 : 1;

          const value = Math.min(
            previous[targetIndex] + 1,
            current[targetIndex - 1] + 1,
            previous[targetIndex - 1] + substitutionCost
          );

          current[targetIndex] = value;
          rowMin = Math.min(rowMin, value);
        }

        if (rowMin > maxDistance) {
          return maxDistance + 1;
        }

        previous = current;
      }

      return previous[target.length];
    }

    function titleMatchDetails(title, query) {
      const normalizedQuery = normalizeSearchText(query);
      const normalizedTitle = normalizeSearchText(title);
      const tokens = normalizedQuery.split(/\s+/).filter(Boolean);

      if (!normalizedQuery || !normalizedTitle) {
        return {
          score: 0,
          compactDistance: MAX_TYPO_DISTANCE + 1,
        };
      }

      let score = 0;

      if (normalizedTitle === normalizedQuery) {
        score += 1000;
      } else if (normalizedTitle.startsWith(normalizedQuery)) {
        score += 750;
      } else if (normalizedTitle.includes(normalizedQuery)) {
        score += 500;
      }

      const tokenMatches = tokens.filter((token) => normalizedTitle.includes(token)).length;
      score += tokenMatches * 90;

      const lengthDelta = Math.abs(normalizedTitle.length - normalizedQuery.length);
      score += Math.max(0, 60 - Math.min(lengthDelta, 60));

      const compactDistance = boundedLevenshtein(
        normalizedCompactText(normalizedTitle),
        normalizedCompactText(normalizedQuery),
        MAX_TYPO_DISTANCE
      );

      if (compactDistance === 1) {
        score += 420;
      } else if (compactDistance === 2) {
        score += 280;
      }

      return {
        score,
        compactDistance,
      };
    }

    function scoreSearchCandidate(item, query) {
      const titles = candidateTitles(item);

      if (!titles.length) {
        return {
          score: Number(item.popularity) || 0,
          bestTitle: mediaTitle(item),
          bestDistance: MAX_TYPO_DISTANCE + 1,
        };
      }

      const bestMatch = titles.reduce((best, title) => {
        const details = titleMatchDetails(title, query);

        if (!best || details.score > best.score) {
          return {
            ...details,
            title,
          };
        }

        return best;
      }, null);

      const popularity = Number(item.popularity) || 0;
      const year = mediaYearNumber(item);
      const currentYear = new Date().getFullYear();
      const hasOverview = String(item.overview || "").trim() !== "";

      let score = bestMatch ? bestMatch.score : 0;

      score += Math.min(Math.log1p(popularity) * 42, 180);

      if (hasOverview) {
        score += item.media_type === "movie" ? 180 : 110;
      } else {
        score -= 45;
      }

      if (year) {
        const age = Math.max(0, currentYear - year);

        if (age <= 1) {
          score += 120;
        } else if (age <= 3) {
          score += 70;
        } else if (age <= 8) {
          score += 20;
        }
      }

      if (item.media_type === "movie") {
        score += 130;

        if (popularity >= 30) {
          score += 120;
        } else if (popularity >= 10) {
          score += 55;
        }

        if (bestMatch && bestMatch.compactDistance <= MAX_TYPO_DISTANCE && hasOverview) {
          score += 120;
        }
      }

      if (item.media_type === "tv") {
        if (popularity >= 40) {
          score += 120;
        } else if (popularity >= 15) {
          score += 60;
        }

        if (year && year >= currentYear - 1) {
          score += 160;
        } else if (year && year >= currentYear - 3) {
          score += 80;
        }

        if (bestMatch && bestMatch.compactDistance <= MAX_TYPO_DISTANCE && popularity >= 20) {
          score += 220;
        }
      }

      return {
        score,
        bestTitle: bestMatch ? bestMatch.title : mediaTitle(item),
        bestDistance: bestMatch ? bestMatch.compactDistance : MAX_TYPO_DISTANCE + 1,
      };
    }

    function filmwebSearchUrl(title, year) {
      const basePath = "https://www.filmweb.pl/search#/all";
      const query = [title, year].filter(Boolean).join(" ");

      return `${basePath}?query=${encodeURIComponent(query)}`;
    }

    function filmwebMediaType(item) {
      if (!item || !item.media_type) {
        return null;
      }

      return item.media_type === "movie"
        ? "film"
        : item.media_type === "tv"
          ? "serial"
          : null;
    }

    function filmwebMediaTypeFromNewsItem(item) {
      if (!item || !item.mediaType) {
        return null;
      }

      return item.mediaType === "movie"
        ? "film"
        : item.mediaType === "tv"
          ? "serial"
          : null;
    }

    async function filmwebRequest(params) {
      const url = new URL(FILMWEB_API_ENDPOINT, window.location.href);

      Object.entries(params || {}).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== "") {
          url.searchParams.set(key, value);
        }
      });

      const response = await fetch(url.toString(), {
        method: "GET",
        headers: {
          "Accept": "application/json",
        },
      });

      const payload = await response.json().catch(() => null);

      if (!response.ok) {
        const message = payload && payload.error
          ? payload.error
          : "Nie udało się pobrać danych z Filmweb.";

        throw new Error(message);
      }

      return payload || {};
    }

    async function resolveFilmwebMatch(item) {
      const cacheKey = `${item.media_type}:${item.id}`;

      if (filmwebMatchCache.has(cacheKey)) {
        return filmwebMatchCache.get(cacheKey);
      }

      const fallbackUrl = filmwebSearchUrl(mediaTitle(item), mediaYear(item));

      const request = (async () => {
        const mediaType = filmwebMediaType(item);

        if (!mediaType) {
          return {
            id: null,
            url: fallbackUrl,
          };
        }

        try {
          const payload = await filmwebRequest({
            action: "match",
            title: mediaTitle(item),
            originalTitle: item.original_title || item.original_name || "",
            year: mediaYear(item) || "",
            mediaType,
            providers: "1",
          });

          const filmwebId = Number(payload && payload.id);

          return {
            id: Number.isFinite(filmwebId) && filmwebId > 0 ? filmwebId : null,
            url: payload && payload.url ? payload.url : fallbackUrl,
            providers: payload && payload.providers
              ? normalizeFilmwebProviders(payload.providers)
              : null,
          };
        } catch (error) {
          return {
            id: null,
            url: fallbackUrl,
            providers: null,
          };
        }
      })();

      filmwebMatchCache.set(cacheKey, request);

      return request;
    }

    async function resolveFilmwebUrlForNewsItem(item) {
      const title = item.title || "";
      const originalTitle = item.originalTitle || "";
      const year = item.year || "";
      const mediaType = filmwebMediaTypeFromNewsItem(item);
      const cacheKey = `${item.mediaType || ""}:${title}:${originalTitle}:${year}`;

      if (filmwebNewsMatchCache.has(cacheKey)) {
        return filmwebNewsMatchCache.get(cacheKey);
      }

      const request = (async () => {
        if (!mediaType || (!title && !originalTitle)) {
          return "";
        }

        try {
          const payload = await filmwebRequest({
            action: "match",
            title,
            originalTitle,
            year,
            mediaType,
          });

          const filmwebId = Number(payload && payload.id);

          return Number.isFinite(filmwebId) && filmwebId > 0 && payload && payload.url
            ? payload.url
            : "";
        } catch (error) {
          return "";
        }
      })();

      filmwebNewsMatchCache.set(cacheKey, request);

      return request;
    }

    function primeFilmwebNewsLinks(items) {
      const pending = [];

      (items || []).forEach((item) => {
        const title = item.title || "";
        const originalTitle = item.originalTitle || "";
        const year = item.year || "";
        const mediaType = filmwebMediaTypeFromNewsItem(item);
        const cacheKey = `${item.mediaType || ""}:${title}:${originalTitle}:${year}`;

        if (filmwebNewsMatchCache.has(cacheKey)) {
          return;
        }

        if (!mediaType || (!title && !originalTitle)) {
          filmwebNewsMatchCache.set(cacheKey, Promise.resolve(""));
          return;
        }

        pending.push({ cacheKey, title, originalTitle, year, mediaType });
      });

      if (!pending.length) {
        return Promise.resolve();
      }

      const batchPromise = filmwebRequest({
        action: "matchBatch",
        items: JSON.stringify(pending.map((entry) => ({
          title: entry.title,
          originalTitle: entry.originalTitle,
          year: entry.year,
          mediaType: entry.mediaType,
        }))),
      })
        .then((payload) => (payload && Array.isArray(payload.results) ? payload.results : []))
        .catch(() => []);

      pending.forEach((entry, index) => {
        const urlPromise = batchPromise.then((results) => {
          const result = results[index];
          const filmwebId = Number(result && result.id);

          return Number.isFinite(filmwebId) && filmwebId > 0 && result && result.url
            ? result.url
            : "";
        });

        filmwebNewsMatchCache.set(entry.cacheKey, urlPromise);
      });

      return batchPromise;
    }

    function dedupeSearchResults(items) {
      const uniqueItems = new Map();

      items.forEach((item) => {
        if (!item || !item.id || !item.media_type) {
          return;
        }

        uniqueItems.set(`${item.media_type}:${item.id}`, item);
      });

      return Array.from(uniqueItems.values());
    }

    async function fetchSearchPayloads(path, params) {
      const firstPayload = await tmdbRequest(path, {
        ...params,
        page: "1",
      });

      const totalPages = Math.min(Number(firstPayload.total_pages) || 1, SEARCH_PAGE_LIMIT);
      const additionalRequests = [];

      for (let page = 2; page <= totalPages; page += 1) {
        additionalRequests.push(tmdbRequest(path, {
          ...params,
          page: String(page),
        }));
      }

      return [
        firstPayload,
        ...(await Promise.all(additionalRequests)),
      ];
    }

    function mapSearchResults(payloads, mediaType, filterExplicitAdult = false) {
      return payloads
        .flatMap((payload) => (payload.results || []))
        .filter((item) => !filterExplicitAdult || item.adult !== true)
        .map((item) => ({
          ...item,
          media_type: mediaType,
        }));
    }

    async function searchByMediaType(path, mediaType, query) {
      const baseParams = {
        query,
        language: DEFAULT_LANGUAGE,
        include_adult: "false",
      };

      const standardPayloads = await fetchSearchPayloads(path, baseParams);
      const standardResults = mapSearchResults(standardPayloads, mediaType);

      if (standardResults.length > 0) {
        return standardResults;
      }

      const fallbackPayloads = await fetchSearchPayloads(path, {
        ...baseParams,
        include_adult: "true",
      });

      return mapSearchResults(fallbackPayloads, mediaType, true);
    }

    async function loadMovieCollection(item) {
      if (!item || item.media_type !== "movie" || !item.id) {
        return null;
      }

      const cacheKey = Number(item.id);

      if (movieCollectionCache.has(cacheKey)) {
        return movieCollectionCache.get(cacheKey);
      }

      const request = (async () => {
        try {
          const details = await tmdbRequest(`/movie/${item.id}`, {
            language: DEFAULT_LANGUAGE,
          });

          const collectionId = Number(
            details && details.belongs_to_collection
              ? details.belongs_to_collection.id
              : 0
          );

          if (!collectionId) {
            return null;
          }

          const collectionPayload = await tmdbRequest(`/collection/${collectionId}`, {
            language: DEFAULT_LANGUAGE,
          });

          const parts = Array.isArray(collectionPayload.parts)
            ? collectionPayload.parts
              .map((part) => ({
                ...part,
                media_type: "movie",
              }))
              .filter(hasPremiered)
              .sort(sortByEarliestRelease)
            : [];

          return parts.length > 1
            ? {
              id: collectionId,
              name: collectionPayload.name || "",
              parts,
            }
            : null;
        } catch (error) {
          return null;
        }
      })();

      movieCollectionCache.set(cacheKey, request);

      return request;
    }

    function collectionMatchDetails(collectionData, query) {
      if (!collectionData || !Array.isArray(collectionData.parts)) {
        return {
          strongMatches: 0,
          score: 0,
        };
      }

      const nameScore = collectionData.name
        ? titleMatchDetails(collectionData.name, query).score
        : 0;

      let strongMatches = 0;
      let score = nameScore;

      collectionData.parts.forEach((part) => {
        const details = candidateTitles(part).reduce((bestDetails, title) => {
          const titleDetails = titleMatchDetails(title, query);

          return !bestDetails || titleDetails.score > bestDetails.score
            ? titleDetails
            : bestDetails;
        }, null) || titleMatchDetails(mediaTitle(part), query);

        score += details.score;

        if (details.score >= 700) {
          strongMatches += 1;
          score += 500;
        }
      });

      return {
        strongMatches,
        score,
      };
    }

    async function findPromotedMovieCollection(items, query) {
      const movieCandidates = items
        .filter((item) => item && item.media_type === "movie")
        .slice(0, SMALL_COLLECTION_INSERT_LIMIT);

      if (!movieCandidates.length) {
        return null;
      }

      const collectionCandidates = await Promise.all(
        movieCandidates.map(async (movie) => ({
          leadMovie: movie,
          collectionData: await loadMovieCollection(movie),
        }))
      );

      let bestCandidate = null;

      collectionCandidates.forEach((candidate) => {
        if (!candidate.collectionData) {
          return;
        }

        const matchDetails = collectionMatchDetails(candidate.collectionData, query);

        if (matchDetails.strongMatches < 2) {
          return;
        }

        if (!bestCandidate || matchDetails.score > bestCandidate.score) {
          bestCandidate = {
            ...candidate,
            score: matchDetails.score,
          };
        }
      });

      return bestCandidate;
    }

    function promoteMovieSeriesItems(items, leadMovie, collectionData) {
      if (
        !Array.isArray(items)
        || items.length < 2
        || !leadMovie
        || leadMovie.media_type !== "movie"
        || !collectionData
        || !Array.isArray(collectionData.parts)
      ) {
        return items;
      }

      const existingItems = new Map(
        items.map((item) => [`${item.media_type}:${item.id}`, item])
      );

      const shouldInsertMissingParts = collectionData.parts.length <= SMALL_COLLECTION_INSERT_LIMIT;
      const collectionKeys = new Set();
      const collectionItems = [];

      collectionData.parts.forEach((part) => {
        const key = `movie:${part.id}`;

        if (collectionKeys.has(key)) {
          return;
        }

        const existingItem = existingItems.get(key);
        const candidate = existingItem || (shouldInsertMissingParts ? part : null);

        if (!candidate) {
          return;
        }

        collectionKeys.add(key);
        collectionItems.push(candidate);
      });

      if (collectionItems.length < 2) {
        return items;
      }

      return [
        ...collectionItems.sort(sortByEarliestRelease),
        ...items.filter((item) => !collectionKeys.has(`${item.media_type}:${item.id}`)),
      ];
    }

    function buildDiscoverParams(mediaType, options = {}) {
      const params = {
        language: DEFAULT_LANGUAGE,
        watch_region: DEFAULT_REGION,
        with_watch_monetization_types: "flatrate",
        sort_by: options.sortBy || "popularity.desc",
        page: String(options.page || 1),
      };

      if (options.providerId) {
        params.with_watch_providers = String(options.providerId);
      }

      if (options.minVotes) {
        params["vote_count.gte"] = String(options.minVotes);
      }

      if (mediaType === "movie") {
        params.region = DEFAULT_REGION;
        params["primary_release_date.lte"] = getTodayIso();
      } else {
        params["first_air_date.lte"] = getTodayIso();
      }

      return params;
    }

    async function discoverByMediaType(path, mediaType, options) {
      const payload = await tmdbRequest(
        path,
        buildDiscoverParams(mediaType, options)
      );

      return {
        totalPages: Math.min(Number(payload.total_pages) || 1, RANDOM_MAX_PAGES),
        results: (payload.results || []).map((item) => ({
          ...item,
          media_type: mediaType,
        })),
      };
    }

    async function enrichCatalogItem(item, region, extra = {}) {
      const deferFilmweb = extra.deferFilmweb === true;
      const [tmdbProviders, genreMaps, filmwebMatch] = await Promise.all([
        resolveProviderData(item),
        loadGenreMaps(),
        deferFilmweb ? Promise.resolve(null) : resolveFilmwebMatch(item),
      ]);
      const title = mediaTitle(item);
      const year = mediaYear(item);
      const filmwebProviders = filmwebMatch && filmwebMatch.providers && filmwebMatch.providers.hasData
        ? filmwebMatch.providers
        : null;

      return {
        mediaType: item.media_type,
        title,
        englishTitle: englishTitleForDisplay(item, title),
        year,
        genres: genreNamesForItem(item, genreMaps),
        overview: item.overview || "",
        poster: imageUrl(item.poster_path || "", "w342"),
        providers: filmwebProviders || tmdbProviders,
        filmwebUrl: filmwebMatch && filmwebMatch.url ? filmwebMatch.url : "",
        filmwebLoading: deferFilmweb,
        badge: extra.badge || null,
      };
    }

    function searchResultCacheKey(item, region) {
      return `${region}:${item.media_type}:${item.id}`;
    }

    function enrichSearchResult(item, region) {
      const cacheKey = searchResultCacheKey(item, region);

      if (enrichedSearchResultCache.has(cacheKey)) {
        return enrichedSearchResultCache.get(cacheKey);
      }

      const request = enrichCatalogItem(item, region, { deferFilmweb: true });
      enrichedSearchResultCache.set(cacheKey, request);

      return request;
    }

    function baseSearchResultItem(item, region) {
      const title = mediaTitle(item);
      const year = mediaYear(item);

      return {
        mediaType: item.media_type,
        title,
        englishTitle: englishTitleForDisplay(item, title),
        year,
        genres: [],
        overview: item.overview || "",
        poster: imageUrl(item.poster_path || "", "w342"),
        providers: loadingProviderData("filmweb"),
        filmwebUrl: "",
        filmwebLoading: true,
        badge: null,
        sourceItem: item,
        region,
      };
    }

    function normalizeFilmwebProviders(payload) {
      const groups = {};

      Object.entries(
        payload && payload.groups && typeof payload.groups === "object"
          ? payload.groups
          : {}
      ).forEach(([label, providers]) => {
        if (label !== "Abonament") {
          return;
        }

        const normalizedProviders = Array.isArray(providers)
          ? providers
            .filter((provider) => provider && provider.name)
            .map((provider) => ({
              name: provider.name || "Nieznany serwis",
              logo: provider.logo || "",
              priority: Number.isFinite(provider.priority) ? provider.priority : 9999,
              url: provider.url || "",
            }))
            .sort((left, right) => {
              if (left.priority !== right.priority) {
                return left.priority - right.priority;
              }

              return left.name.localeCompare(right.name, "pl");
            })
          : [];

        if (normalizedProviders.length) {
          groups[label] = normalizedProviders;
        }
      });

      return {
        hasData: Object.keys(groups).length > 0,
        link: payload && typeof payload.link === "string" ? payload.link : null,
        groups,
        source: "filmweb",
        attributionLabel: null,
        attributionUrl: null,
      };
    }

    function emptyProviderData(source = "filmweb") {
      return {
        hasData: false,
        loading: false,
        link: null,
        groups: {},
        source,
        attributionLabel: null,
        attributionUrl: null,
      };
    }

    function loadingProviderData(source = "filmweb") {
      return {
        ...emptyProviderData(source),
        loading: true,
      };
    }

    function normalizeTmdbProviders(payload) {
      const results = payload && payload.results && typeof payload.results === "object"
        ? payload.results
        : {};
      const regionData = results[DEFAULT_REGION] || null;
      const flatrate = regionData && Array.isArray(regionData.flatrate) ? regionData.flatrate : [];
      const link = regionData && typeof regionData.link === "string" ? regionData.link : null;

      const providers = flatrate
        .filter((provider) => provider && provider.provider_name)
        .map((provider) => ({
          name: provider.provider_name,
          logo: filmwebProviderLogos[provider.provider_name] || imageUrl(provider.logo_path || "", "w92") || "",
          priority: Number.isFinite(provider.display_priority) ? provider.display_priority : 9999,
          url: link || "",
        }))
        .sort((left, right) => {
          if (left.priority !== right.priority) {
            return left.priority - right.priority;
          }

          return left.name.localeCompare(right.name, "pl");
        });

      const groups = providers.length ? { Abonament: providers } : {};

      return {
        hasData: providers.length > 0,
        loading: false,
        link,
        groups,
        source: "tmdb",
        attributionLabel: "JustWatch",
        attributionUrl: link,
      };
    }

    function loadTmdbProviderData(item) {
      if (!item || !item.id || (item.media_type !== "movie" && item.media_type !== "tv")) {
        return Promise.resolve(emptyProviderData("tmdb"));
      }

      const cacheKey = `${item.media_type}:${item.id}`;

      if (tmdbProviderDataCache.has(cacheKey)) {
        return tmdbProviderDataCache.get(cacheKey);
      }

      const request = tmdbRequest(`/${item.media_type}/${item.id}/watch/providers`, {})
        .then((payload) => normalizeTmdbProviders(payload))
        .catch(() => emptyProviderData("tmdb"));

      tmdbProviderDataCache.set(cacheKey, request);

      return request;
    }

    async function resolveProviderData(item) {
      const tmdbProviders = await loadTmdbProviderData(item);

      if (tmdbProviders && tmdbProviders.hasData) {
        return tmdbProviders;
      }

      return emptyProviderData("tmdb");
    }

    function setHomeNewsToggleState(isExpanded, isLoading = false) {
      if (!toggleHomeNewsButton) {
        return;
      }

      toggleHomeNewsButton.setAttribute("aria-expanded", String(isExpanded));
      toggleHomeNewsButton.classList.toggle("is-loading", isLoading);

      if (isLoading) {
        toggleHomeNewsButton.textContent = "Ukryj nowości na VOD";
        return;
      }

      toggleHomeNewsButton.textContent = isExpanded
        ? "Ukryj nowości na VOD"
        : "Pokaż nowości na VOD";
    }

    function showHomeSections(options = {}) {
      const isLoading = Boolean(options.loading);

      if (homeToggleWrapEl) {
        homeToggleWrapEl.classList.remove("hidden");
      }

      if (homeSectionsEl) {
        homeSectionsEl.classList.remove("hidden");
      }

      setHomeNewsToggleState(true, isLoading);
    }

    function hideHomeSections() {
      homeNewsOperationId += 1;

      if (homeToggleWrapEl) {
        homeToggleWrapEl.classList.remove("hidden");
      }

      if (homeSectionsEl) {
        homeSectionsEl.classList.add("hidden");
      }

      if (netflixShelfEl) {
        netflixShelfEl.classList.remove("is-loading-news");
      }

      if (hboMaxShelfEl) {
        hboMaxShelfEl.classList.remove("is-loading-news");
      }

      setHomeNewsToggleState(false);
    }

    function escapeHtml(value) {
      return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }

    function setMessage(type, text) {
      if (!messageBox) {
        return;
      }

      if (!text) {
        messageBox.className = "callout hidden";
        messageBox.innerHTML = "";
        return;
      }

      messageBox.className = `callout ${type}`;
      messageBox.textContent = text;
    }

    function renderStatusLoader(text) {
      return `
        <span class="status-loader-spinner" aria-hidden="true"></span>
        <span class="loading-status-text">
          <span>${escapeHtml(text)}</span>
          <span class="loading-status-dots" aria-hidden="true">
            <span class="loading-status-dot">.</span>
            <span class="loading-status-dot">.</span>
            <span class="loading-status-dot">.</span>
          </span>
        </span>
      `;
    }

    function setStatus(text, options = {}) {
      if (!statusBox) {
        return;
      }

      if (!text) {
        statusBox.className = "status hidden";
        statusBox.textContent = "";
        return;
      }

      const isLoading = Boolean(options.loading);

      statusBox.className = isLoading ? "status is-loading" : "status";

      if (isLoading) {
        statusBox.innerHTML = renderStatusLoader(text);
        return;
      }

      statusBox.textContent = text;
    }

    function setRandomStatus(text, options = {}) {
      if (!randomStatusBox) {
        return;
      }

      if (!text) {
        randomStatusBox.className = "status random-status hidden";
        randomStatusBox.textContent = "";
        return;
      }

      const isLoading = Boolean(options.loading);

      randomStatusBox.className = isLoading
        ? "status random-status is-loading"
        : "status random-status";

      if (isLoading) {
        randomStatusBox.innerHTML = renderStatusLoader(text);
        return;
      }

      randomStatusBox.textContent = text;
    }
    function clearLoadingStatuses() {
      [statusBox, randomStatusBox].forEach((box) => {
        if (!box) {
          return;
        }

        box.classList.remove("is-loading");
        box.classList.add("hidden");
        box.innerHTML = "";
        box.textContent = "";
      });
    }

    function renderNewsLoader(label = "Ładowanie nowości...") {
      const cards = Array.from({ length: HOME_GRID_SIZE }, (_, index) => `
        <div class="news-loader-card" style="--loader-index: ${index};" aria-hidden="true">
          <div class="news-loader-poster"></div>
          <div class="news-loader-line medium"></div>
          <div class="news-loader-line short"></div>
        </div>
      `).join("");

      return `
        <div class="news-loader">
          <p class="news-loader-text" role="status" aria-live="polite">
            <span class="news-loader-spinner" aria-hidden="true"></span>
            <span>${escapeHtml(label)}</span>
          </p>

          ${cards}
        </div>
      `;
    }

    function renderCalendarLoader(label = "Ładowanie kalendarza premier...") {
      return `
        <div class="calendar-loader" role="status" aria-live="polite">
          <span class="calendar-loader-spinner" aria-hidden="true"></span>
          <span>${escapeHtml(label)}</span>
        </div>
      `;
    }

    function calendarDateIso(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    }

    function monthPartsFromOffset(offset) {
      const date = new Date();
      date.setDate(1);
      date.setMonth(date.getMonth() + Number(offset || 0));

      return {
        year: date.getFullYear(),
        month: date.getMonth() + 1,
        monthKey: `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`,
      };
    }

    function dateFromIso(isoDate) {
      return new Date(`${isoDate}T00:00:00`);
    }

    function addDaysToDate(date, days) {
      const nextDate = new Date(date);
      nextDate.setDate(nextDate.getDate() + days);

      return nextDate;
    }

    function calendarMonthKey(date) {
      return calendarDateIso(date).slice(0, 7);
    }

    function formatCalendarMonthTitle(monthKey) {
      const parsedDate = new Date(`${monthKey}-01T00:00:00`);

      if (Number.isNaN(parsedDate.getTime())) {
        return monthKey;
      }

      const formatted = parsedDate.toLocaleDateString("pl-PL", {
        month: "long",
        year: "numeric",
      });

      return formatted.charAt(0).toUpperCase() + formatted.slice(1);
    }

    function buildCalendarMonthDays(monthKey) {
      const firstDayOfMonth = dateFromIso(`${monthKey}-01`);
      const gridStart = new Date(firstDayOfMonth);
      const mondayBasedDay = (gridStart.getDay() + 6) % 7;

      gridStart.setDate(gridStart.getDate() - mondayBasedDay);

      const days = [];

      for (let index = 0; index < 42; index += 1) {
        const currentDate = addDaysToDate(gridStart, index);
        const iso = calendarDateIso(currentDate);

        days.push({
          iso,
          dayNumber: currentDate.getDate(),
          monthKey: calendarMonthKey(currentDate),
          isOutside: calendarMonthKey(currentDate) !== monthKey,
          isToday: iso === getTodayIso(),
        });
      }

      return days;
    }

    function setReleaseCalendarToggleState(isExpanded) {
      if (!toggleReleaseCalendarButton) {
        return;
      }

      toggleReleaseCalendarButton.setAttribute("aria-expanded", String(isExpanded));
      toggleReleaseCalendarButton.textContent = isExpanded
        ? "Ukryj kalendarz premier"
        : "Kalendarz premier";
    }

    function showReleaseCalendar() {
      if (!hasReleaseCalendar) {
        return;
      }

      hideHomeSections();
      homeToggleWrapEl.classList.remove("hidden");
      releaseCalendarEl.classList.remove("hidden");
      setReleaseCalendarToggleState(true);
      renderReleaseCalendar();
    }

    function hideReleaseCalendar() {
      if (!hasReleaseCalendar) {
        return;
      }

      releaseCalendarEl.classList.add("hidden");
      setReleaseCalendarToggleState(false);
      closeCalendarDayDialog();
    }

    function hideRandomPanel() {
      if (!toggleRandomPanelButton || !randomBarEl) {
        return;
      }

      toggleRandomPanelButton.setAttribute("aria-expanded", "false");
      toggleRandomPanelButton.textContent = "Rozwiń losowanie";
      randomBarEl.hidden = true;
      setRandomStatus("");
    }

    function showRandomPanel() {
      if (!toggleRandomPanelButton || !randomBarEl) {
        return;
      }

      activeSearchState = null;

      if (resultsEl) {
        resultsEl.innerHTML = "";
        resultsEl.classList.add("hidden");
        resultsEl.classList.remove("has-rendered");
      }

      hideHomeSections();
      hideReleaseCalendar();
      hideRandomPanel();

      setMessage("", "");
      setStatus("");

      const url = new URL(window.location.href);
      url.searchParams.delete("q");
      url.searchParams.delete("region");

      window.history.replaceState({}, "", url);

      toggleRandomPanelButton.setAttribute("aria-expanded", "true");
      toggleRandomPanelButton.textContent = "Ukryj losowanie";
      randomBarEl.hidden = false;
    }

    function hidePanelsForSearchIntent() {
      activeSearchState = null;

      if (resultsEl) {
        resultsEl.innerHTML = "";
        resultsEl.classList.add("hidden");
        resultsEl.classList.remove("has-rendered");
      }

      hideHomeSections();
      hideReleaseCalendar();
      hideRandomPanel();

      setMessage("", "");
      setStatus("");
      setRandomStatus("");
    }

    function updatePremiereButtons(activeKind, isLoading = false) {
      const normalizedActiveKind = activeKind === "all"
        ? "all"
        : activeKind === "tv"
          ? "tv"
          : activeKind === "movie"
            ? "movie"
            : null;

      const buttonConfig = [
        {
          element: showMoviePremieresButton,
          kind: "movie",
          idleText: "Pokaż premiery filmów",
          loadingText: "Ładowanie filmów...",
        },
        {
          element: showTvPremieresButton,
          kind: "tv",
          idleText: "Pokaż premiery seriali",
          loadingText: "Ładowanie seriali...",
        },
        {
          element: showAllPremieresButton,
          kind: "all",
          idleText: "Pokaż filmy i seriale",
          loadingText: "Ładowanie filmów i seriali...",
        },
      ];

      buttonConfig.forEach(({ element, kind, idleText, loadingText }) => {
        if (!element) {
          return;
        }

        const isActive = normalizedActiveKind === kind;

        element.disabled = Boolean(isLoading);
        element.classList.toggle("secondary", isActive);
        element.classList.toggle("ghost", !isActive);
        element.classList.toggle("is-loading", isActive && isLoading);
        element.textContent = isActive && isLoading ? loadingText : idleText;
      });
    }

    function premiereKindLabel(kind) {
      if (kind === "all") {
        return "filmów i seriali";
      }

      return kind === "tv" ? "seriali" : "filmów";
    }

    function premiereMediaLabel(kind) {
      return kind === "tv" ? "Serial" : "Film";
    }

    function premiereDisplayScore(item) {
      if (!item || typeof item !== "object") {
        return 0;
      }

      const title = String(item.title || "").trim().toLowerCase();
      const url = String(item.filmwebUrl || item.url || "");
      const genres = Array.isArray(item.genres) ? item.genres : [];
      let score = 0;

      if (Number(item.popularity) > 0) {
        score += Math.min(Math.log1p(Number(item.popularity)) * 20, 120);
      }

      if (Number(item.voteCount || item.votes) > 0) {
        score += Math.min(Math.log1p(Number(item.voteCount || item.votes)) * 8, 80);
      }

      if (item.poster) {
        score += 45;
      }

      if (item.overview) {
        score += 12;
      }

      if (genres.length) {
        score += 8;
      }

      if (url && !url.includes("/vod")) {
        score += 35;
      }

      if (url.includes("/season/")) {
        score += 15;
      }

      if (url.includes("/vod")) {
        score -= 70;
      }

      if (/\bonline$/i.test(title)) {
        score -= 55;
      }

      return score;
    }

    function comparePremiereDisplayItems(left, right) {
      const scoreDelta = premiereDisplayScore(right) - premiereDisplayScore(left);

      if (scoreDelta !== 0) {
        return scoreDelta;
      }

      const leftOrder = Number.isFinite(Number(left && left._premiereOrder))
        ? Number(left._premiereOrder)
        : Number.MAX_SAFE_INTEGER;
      const rightOrder = Number.isFinite(Number(right && right._premiereOrder))
        ? Number(right._premiereOrder)
        : Number.MAX_SAFE_INTEGER;

      if (leftOrder !== rightOrder) {
        return leftOrder - rightOrder;
      }

      const typeDelta = String((left && left.mediaType) || "").localeCompare(String((right && right.mediaType) || ""));

      if (typeDelta !== 0) {
        return typeDelta;
      }

      return String((left && left.title) || "").localeCompare(String((right && right.title) || ""), "pl");
    }

    function dedupePremiereItems(items) {
      const uniqueItems = new Map();

      (items || []).forEach((item) => {
        if (!item || typeof item !== "object") {
          return;
        }

        const mediaType = item.mediaType === "tv" ? "tv" : "movie";
        const key = [
          mediaType,
          item.id || "",
          item.filmwebUrl || item.url || "",
          item.title || "",
          item.premiereDate || "",
        ].join("|");

        if (!uniqueItems.has(key)) {
          uniqueItems.set(key, {
            ...item,
            mediaType,
          });
        }
      });

      return Array.from(uniqueItems.values()).sort((left, right) => {
        const leftDate = String(left.premiereDate || "9999-12-31");
        const rightDate = String(right.premiereDate || "9999-12-31");

        if (leftDate !== rightDate) {
          return leftDate.localeCompare(rightDate);
        }

        return comparePremiereDisplayItems(left, right);
      });
    }

    async function fetchFilmwebPremiereMonth(kind, offset) {
      const normalizedKind = kind === "all" ? "all" : kind === "tv" ? "tv" : "movie";
      const parts = monthPartsFromOffset(offset);

      const fetchOneKind = async (requestKind) => {
        const payload = await filmwebRequest({
          action: "premieres",
          kind: requestKind,
          year: String(parts.year),
          month: String(parts.month),
        });

        return {
          url: payload.url || payload.sourceUrl || "",
          items: Array.isArray(payload.items)
            ? payload.items.map((item, index) => ({
              ...item,
              mediaType: requestKind === "tv" ? "tv" : "movie",
              _premiereOrder: index,
            }))
            : [],
        };
      };

      if (normalizedKind === "all") {
        const [moviePayload, tvPayload] = await Promise.all([
          fetchOneKind("movie"),
          fetchOneKind("tv"),
        ]);

        return {
          ...parts,
          url: moviePayload.url || tvPayload.url || "",
          sourceUrls: {
            movie: moviePayload.url || "",
            tv: tvPayload.url || "",
          },
          items: dedupePremiereItems([
            ...moviePayload.items,
            ...tvPayload.items,
          ]),
        };
      }

      const payload = await fetchOneKind(normalizedKind);

      return {
        ...parts,
        url: payload.url,
        items: dedupePremiereItems(payload.items),
      };
    }

    function renderCalendarPremiereChip(item, overflowCount = 0, options = {}) {
      if (overflowCount > 0) {
        return `
          <button
            class="calendar-premiere-more"
            type="button"
            data-role="show-day-premieres"
            data-date="${escapeHtml(options.date || "")}"
          >
            +${overflowCount} wi\u0119cej
          </button>
        `;
      }

      const mediaLabel = premiereMediaLabel(item.mediaType);
      const title = item.title || "Bez tytułu";
      const url = item.filmwebUrl || filmwebSearchUrl(title, item.year || "");
      const typeClass = item.mediaType === "tv" ? "calendar-premiere-chip-tv" : "calendar-premiere-chip-movie";

      return `
        <a
          class="calendar-premiere-chip ${typeClass}"
          href="${escapeHtml(url)}"
          target="_blank"
          rel="noreferrer noopener"
          title="${escapeHtml(`${mediaLabel}: ${title}`)}"
        >
          <span class="calendar-premiere-type">${escapeHtml(mediaLabel)}</span>
          <span class="calendar-premiere-title">${escapeHtml(title)}</span>
        </a>
      `;
    }

    function formatCalendarDayTitle(isoDate) {
      const parsedDate = dateFromIso(isoDate);

      if (Number.isNaN(parsedDate.getTime())) {
        return isoDate;
      }

      const formatted = parsedDate.toLocaleDateString("pl-PL", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
      });

      return formatted.charAt(0).toUpperCase() + formatted.slice(1);
    }

    function calendarDayItems(date) {
      const activeKind = releaseCalendarState.activeKind;
      const state = activeKind ? releaseCalendarState[activeKind] : null;
      const monthKey = String(date || "").slice(0, 7);
      const monthData = state && Array.isArray(state.months)
        ? state.months.find((month) => month.monthKey === monthKey)
        : null;

      if (!monthData || !Array.isArray(monthData.items)) {
        return [];
      }

      return monthData.items
        .filter((item) => item && item.premiereDate === date)
        .sort(comparePremiereDisplayItems);
    }

    function closeCalendarDayDialog() {
      const dialog = document.querySelector(".calendar-day-dialog-backdrop");

      if (dialog) {
        dialog.remove();
      }
    }

    function openCalendarDayDialog(date) {
      const items = calendarDayItems(date);

      if (!items.length) {
        return;
      }

      closeCalendarDayDialog();

      const backdrop = document.createElement("div");
      backdrop.className = "calendar-day-dialog-backdrop";
      backdrop.setAttribute("data-role", "close-day-premieres");

      backdrop.innerHTML = `
        <section
          class="calendar-day-dialog"
          role="dialog"
          aria-modal="true"
          aria-label="${escapeHtml(formatCalendarDayTitle(date))}"
        >
          <div class="calendar-day-dialog-head">
            <div>
              <p class="calendar-day-dialog-kicker">${items.length} premier${items.length === 1 ? "a" : ""}</p>
              <h3 class="calendar-day-dialog-title">${escapeHtml(formatCalendarDayTitle(date))}</h3>
            </div>

            <button
              class="ghost calendar-day-dialog-close"
              type="button"
              data-role="close-day-premieres"
              aria-label="Zamknij"
            >
              &times;
            </button>
          </div>

          <div class="calendar-day-dialog-list">
            ${items.map((item) => renderCalendarPremiereChip(item)).join("")}
          </div>
        </section>
      `;

      document.body.appendChild(backdrop);
      const closeButton = backdrop.querySelector(".calendar-day-dialog-close");

      if (closeButton) {
        closeButton.focus();
      }
    }

    function renderCalendarMonth(monthData) {
      const itemsByDate = new Map();

      (monthData.items || []).forEach((item) => {
        const date = item.premiereDate || "";

        if (!itemsByDate.has(date)) {
          itemsByDate.set(date, []);
        }

        itemsByDate.get(date).push(item);
      });

      itemsByDate.forEach((items) => {
        items.sort(comparePremiereDisplayItems);
      });

      const weekdays = ["Pon", "Wt", "Śr", "Czw", "Pt", "Sob", "Nd"];
      const days = buildCalendarMonthDays(monthData.monthKey);

      return `
        <section class="calendar-month">
          <h3 class="calendar-month-title">${escapeHtml(formatCalendarMonthTitle(monthData.monthKey))}</h3>

          <div class="calendar-scroll">
            <div class="calendar-weekdays" aria-hidden="true">
              ${weekdays.map((day) => `<div class="calendar-weekday">${escapeHtml(day)}</div>`).join("")}
            </div>

            <div class="calendar-month-grid">
              ${days.map((day) => {
                const dayItems = day.isOutside ? [] : (itemsByDate.get(day.iso) || []);
                const visibleItems = dayItems.slice(0, 2);
                const overflowCount = Math.max(0, dayItems.length - visibleItems.length);

                return `
                  <div class="calendar-cell${day.isOutside ? " is-outside" : ""}${day.isToday ? " is-today" : ""}">
                    <span class="calendar-cell-date">${escapeHtml(day.dayNumber)}</span>

                    <div class="calendar-cell-items">
                      ${visibleItems.map((item) => renderCalendarPremiereChip(item)).join("")}
                      ${overflowCount > 0 ? renderCalendarPremiereChip(null, overflowCount, { date: day.iso }) : ""}
                    </div>
                  </div>
                `;
              }).join("")}
            </div>
          </div>
        </section>
      `;
    }

    function renderReleaseCalendar() {
      if (!hasReleaseCalendar) {
        return;
      }

      const activeKind = releaseCalendarState.activeKind;

      updatePremiereButtons(
        activeKind,
        activeKind ? releaseCalendarState[activeKind].loading : false
      );

      if (!activeKind) {
        releaseCalendarGridEl.classList.remove("is-loading-news");
        releaseCalendarNoteEl.textContent = "Bieżący miesiąc z możliwością dociągnięcia kolejnego";
        releaseCalendarGridEl.innerHTML = '<div class="mini-loading">Wybierz typ premier do wyświetlenia.</div>';
        return;
      }

      const state = releaseCalendarState[activeKind];
      releaseCalendarNoteEl.textContent = `Premiery ${premiereKindLabel(activeKind)} z Filmwebu`;
      releaseCalendarGridEl.classList.toggle("is-loading-news", state.loading && state.months.length === 0);

      if (state.loading && state.months.length === 0) {
        releaseCalendarGridEl.innerHTML = renderCalendarLoader(`Ładowanie premier ${premiereKindLabel(activeKind)}...`);
        return;
      }

      if (state.error && state.months.length === 0) {
        releaseCalendarGridEl.classList.remove("is-loading-news");
        releaseCalendarGridEl.innerHTML = `<div class="mini-loading">${escapeHtml(state.error)}</div>`;
        return;
      }

      const monthsMarkup = state.months.length
        ? state.months.map(renderCalendarMonth).join("")
        : '<div class="mini-loading">Brak danych dla wybranego typu premier.</div>';

      const loadingMoreMarkup = state.loading
        ? renderCalendarLoader(`Dociągam kolejny miesiąc premier ${premiereKindLabel(activeKind)}...`)
        : "";

      const nextButtonMarkup = !state.loading
        ? `
          <div class="calendar-load-more-wrap">
            <button
              class="ghost"
              type="button"
              data-role="load-next-premiere-month"
            >
              Dociągnij kolejny miesiąc
            </button>
          </div>
        `
        : "";

      releaseCalendarGridEl.classList.remove("is-loading-news");
      releaseCalendarGridEl.innerHTML = monthsMarkup + loadingMoreMarkup + nextButtonMarkup;
    }

    async function loadPremiereMonth(kind, offset) {
      if (!hasReleaseCalendar) {
        return;
      }

      const normalizedKind = kind === "all" ? "all" : kind === "tv" ? "tv" : "movie";
      const state = releaseCalendarState[normalizedKind];

      if (!state || state.loading) {
        return;
      }

      releaseCalendarState.activeKind = normalizedKind;
      state.loading = true;
      state.error = "";
      renderReleaseCalendar();

      try {
        const monthData = await fetchFilmwebPremiereMonth(normalizedKind, offset);
        const existingIndex = state.months.findIndex((month) => month.monthKey === monthData.monthKey);

        if (existingIndex >= 0) {
          state.months[existingIndex] = monthData;
        } else {
          state.months.push(monthData);
        }
      } catch (error) {
        state.error = error instanceof Error
          ? error.message
          : "Nie udało się pobrać kalendarza premier z Filmwebu.";
      } finally {
        state.loading = false;
        renderReleaseCalendar();
      }
    }

    async function loadPremieres(kind) {
      if (!hasReleaseCalendar) {
        return;
      }

      const normalizedKind = kind === "all" ? "all" : kind === "tv" ? "tv" : "movie";
      const state = releaseCalendarState[normalizedKind];

      if (!state) {
        return;
      }

      releaseCalendarState.activeKind = normalizedKind;

      if (state.months.length > 0 && !state.loading) {
        renderReleaseCalendar();
        return;
      }

      await loadPremiereMonth(normalizedKind, 0);
    }

    async function loadNextPremiereMonth() {
      const activeKind = releaseCalendarState.activeKind;

      if (!activeKind) {
        return;
      }

      const state = releaseCalendarState[activeKind];
      await loadPremiereMonth(activeKind, state.months.length);
    }
    function scoreNewsCandidate(candidate, newsItem) {
      const titles = [
        candidate.title,
        candidate.name,
        candidate.original_title,
        candidate.original_name,
      ].filter(Boolean);

      const expectedTitles = [
        newsItem.title,
        newsItem.originalTitle,
      ].filter(Boolean);

      let score = 0;

      expectedTitles.forEach((expectedTitle) => {
        titles.forEach((candidateTitle) => {
          score = Math.max(score, titleMatchDetails(candidateTitle, expectedTitle).score);
        });
      });

      const candidateYear = mediaYearNumber(candidate);
      const expectedYear = Number.parseInt(newsItem.year || "", 10);

      if (Number.isFinite(candidateYear) && Number.isFinite(expectedYear)) {
        const yearDiff = Math.abs(candidateYear - expectedYear);

        if (yearDiff === 0) {
          score += 300;
        } else if (yearDiff === 1) {
          score += 100;
        } else if (yearDiff <= 2) {
          score += 40;
        } else {
          score -= Math.min(300, yearDiff * 60);
        }
      }

      score += Math.min(Math.log1p(Number(candidate.popularity) || 0) * 20, 120);

      if (candidate.poster_path) {
        score += 80;
      }

      return score;
    }

    async function resolveTmdbNewsMatch(newsItem) {
      const cacheKey = `${newsItem.mediaType || ""}:${newsItem.title || ""}:${newsItem.originalTitle || ""}:${newsItem.year || ""}`;

      if (tmdbNewsMatchCache.has(cacheKey)) {
        return tmdbNewsMatchCache.get(cacheKey);
      }

      const request = (async () => {
        const mediaType = newsItem.mediaType === "tv" ? "tv" : "movie";
        const path = mediaType === "tv" ? "/search/tv" : "/search/movie";

        const queries = Array.from(new Set([
          newsItem.originalTitle,
          newsItem.title,
        ].filter(Boolean).map((value) => String(value).trim()).filter(Boolean)));

        const candidates = [];

        for (const query of queries) {
          try {
            const payload = await tmdbRequest(path, {
              query,
              language: DEFAULT_LANGUAGE,
              include_adult: "false",
              page: "1",
            });

            (payload.results || []).forEach((item) => {
              candidates.push({
                ...item,
                media_type: mediaType,
              });
            });
          } catch (error) {
            // Pomiń pojedyncze nieudane wyszukanie.
          }
        }

        const uniqueCandidates = dedupeSearchResults(candidates)
          .filter(hasPremiered)
          .map((item) => ({
            item,
            score: scoreNewsCandidate(item, newsItem),
          }))
          .sort((left, right) => right.score - left.score);

        if (!uniqueCandidates.length || uniqueCandidates[0].score < 450) {
          return null;
        }

        return uniqueCandidates[0].item;
      })();

      tmdbNewsMatchCache.set(cacheKey, request);

      return request;
    }

    function newsItemCacheKey(item) {
      return [
        item.mediaType || "",
        item.title || "",
        item.originalTitle || "",
        item.year || "",
        item.addedDate || "",
        item.sectionType || "",
      ].join("|");
    }

    async function enrichNewsItem(item) {
      const cacheKey = newsItemCacheKey(item);

      if (localizedNewsItemCache.has(cacheKey)) {
        return localizedNewsItemCache.get(cacheKey);
      }

      const request = (async () => {
        const [tmdbItem, genreMaps, filmwebUrl] = await Promise.all([
          resolveTmdbNewsMatch(item),
          loadGenreMaps(),
          resolveFilmwebUrlForNewsItem(item),
        ]);

        const title = item.title || (tmdbItem ? mediaTitle(tmdbItem) : "Bez tytułu");

        const englishTitle = item.originalTitle
          ? englishTitleForDisplay({ original_language: "en" }, title, item.originalTitle)
          : (tmdbItem ? englishTitleForDisplay(tmdbItem, title) : "");

        const tmdbPoster = tmdbItem ? imageUrl(tmdbItem.poster_path || "", "w342") : null;

        return {
          ...item,
          title,
          englishTitle,
          genres: tmdbItem ? genreNamesForItem(tmdbItem, genreMaps) : [],
          overview: tmdbItem && tmdbItem.overview ? tmdbItem.overview : "",
          poster: tmdbPoster || item.poster || null,
          posterLoading: false,
          filmwebUrl: item.filmwebUrl || filmwebUrl || "",
          filmwebLoading: false,
        };
      })();

      localizedNewsItemCache.set(cacheKey, request);

      return request;
    }

    function baseNewsDisplayItem(item) {
      const title = item.title || item.originalTitle || "Bez tytułu";
      const englishTitle = item.originalTitle && item.originalTitle !== title
        ? item.originalTitle
        : "";

      return {
        ...item,
        title,
        englishTitle,
        genres: Array.isArray(item.genres) ? item.genres : [],
        overview: item.overview || "",
        poster: item.poster || null,
        posterLoading: item.posterLoading !== false,
        filmwebUrl: item.filmwebUrl || "",
        filmwebLoading: item.filmwebLoading !== false,
      };
    }

    function ensureHomeShelfItems(platform, type, count) {
      const state = homeShelfState[platform];

      if (!state) {
        return Promise.resolve();
      }

      const sourceItems = type === "tv" ? state.tvItems : state.movieItems;
      const enrichedItems = type === "tv" ? state.enrichedTv : state.enrichedMovies;
      const startIndex = enrichedItems.length;
      const endIndex = Math.min(count, sourceItems.length);

      if (startIndex >= endIndex) {
        return Promise.resolve();
      }

      const nextItems = sourceItems.slice(startIndex, endIndex);
      const loadToken = state.loadToken;

      enrichedItems.push(...nextItems.map((rawItem) => baseNewsDisplayItem(rawItem)));
      renderHomeShelf(platform);

      primeFilmwebNewsLinks(nextItems);

      return Promise.allSettled(nextItems.map((rawItem) => enrichNewsItem(rawItem)))
        .then((results) => {
          if (state.loadToken !== loadToken) {
            return;
          }

          results.forEach((result, index) => {
            enrichedItems[startIndex + index] = result.status === "fulfilled" && result.value
              ? result.value
              : {
                ...baseNewsDisplayItem(nextItems[index]),
                filmwebLoading: false,
              };
          });

          renderHomeShelf(platform);
        })
        .catch(() => {
          if (state.loadToken === loadToken) {
            for (let index = startIndex; index < endIndex; index += 1) {
              if (enrichedItems[index]) {
                enrichedItems[index] = {
                  ...enrichedItems[index],
                  filmwebLoading: false,
                };
              }
            }

            renderHomeShelf(platform);
          }
        });
    }

    function renderHomeShelf(platform) {
      const state = homeShelfState[platform];
      const target = platform === "netflix" ? netflixShelfEl : hboMaxShelfEl;

      if (!state || !target) {
        return;
      }

      const shelf = target.closest(".shelf");

      if (shelf) {
        const sectionNote = shelf.querySelector(".section-note");

        if (sectionNote) {
          sectionNote.textContent = state.showTv
            ? "Ostatnie 14 dni • wyświetlane: filmy i seriale"
            : "Ostatnie 14 dni • wyświetlane: tylko filmy";
        }

        shelf.querySelectorAll(".shelf-actions").forEach((element) => element.remove());
      }

      const isInitialLoading = state.loading
        && state.enrichedMovies.length === 0
        && state.enrichedTv.length === 0;

      target.classList.toggle("is-loading-news", isInitialLoading);

      if (isInitialLoading) {
        target.innerHTML = renderNewsLoader("Ładowanie nowości...");
        return;
      }

      if (state.error && state.enrichedMovies.length === 0 && state.enrichedTv.length === 0) {
        target.innerHTML = `<div class="mini-loading">${escapeHtml(state.error)}</div>`;
        return;
      }

      const visibleMovies = state.enrichedMovies.slice(0, state.visibleMovieCount);
      const visibleTv = state.showTv ? state.enrichedTv.slice(0, state.visibleTvCount) : [];

      const visibleItems = [
        ...visibleMovies,
        ...visibleTv,
      ];

      if (!visibleItems.length) {
        if (!state.showTv && state.movieItems.length === 0 && state.tvItems.length > 0) {
          target.innerHTML = `<div class="mini-loading">Brak filmów w ostatnich 14 dniach. Możesz doładować seriale osobnym przyciskiem.</div>`;
        } else {
          target.innerHTML = `<div class="mini-loading">Brak nowo dodanych lub ponownie dodanych filmów.</div>`;
        }
      } else {
        target.innerHTML = visibleItems.map((item) => {
          const poster = item.poster
            ? `<img class="mini-poster is-loading-poster" src="${escapeHtml(item.poster)}" alt="Plakat: ${escapeHtml(item.title)}" loading="lazy" decoding="async">`
            : item.posterLoading
              ? '<div class="mini-poster-fallback is-loading-poster" aria-label="Ładowanie plakatu"><span>Ładowanie plakatu</span></div>'
              : '<div class="mini-poster-fallback">Brak plakatu</div>';

          const sectionPill = item.sectionType === "returned"
            ? `<span class="pill highlight">Ponownie dodane</span>`
            : "";

          const mediaPill = item.mediaLabel
            ? `<span class="pill">${escapeHtml(item.mediaLabel)}</span>`
            : "";

          const yearPill = item.year
            ? `<span class="pill">${escapeHtml(item.year)}</span>`
            : "";

          const addedDatePill = item.addedDateDisplay
            ? `<span class="pill">Dodano ${escapeHtml(item.addedDateDisplay)}</span>`
            : "";

          const englishTitle = item.englishTitle
            ? `<p class="mini-alt-title">${escapeHtml(item.englishTitle)}</p>`
            : "";

          const note = item.note
            ? `<p class="mini-note">${escapeHtml(item.note)}</p>`
            : "";

          const overview = item.overview
            ? `
              <div class="description-block">
                <button
                  class="description-toggle"
                  type="button"
                  data-role="toggle-description"
                  aria-expanded="false"
                >
                  Pokaż opis
                </button>

                <p class="overview">${escapeHtml(item.overview)}</p>
              </div>
            `
            : "";

          const filmwebLink = item.filmwebUrl
            ? `
                <a
                  class="mini-link filmweb-link mini-filmweb-logo"
                  href="${escapeHtml(item.filmwebUrl)}"
                  target="_blank"
                  rel="noreferrer noopener"
                  aria-label="Sprawdź na Filmweb.pl"
                ></a>
              `
            : item.filmwebLoading
              ? `
                  <span
                    class="mini-link filmweb-link mini-filmweb-logo is-loading-filmweb"
                    aria-label="Szukam strony na Filmweb.pl"
                    role="img"
                  ></span>
                `
              : `
                  <span
                    class="mini-link filmweb-link mini-filmweb-logo is-disabled-filmweb"
                    aria-label="Nie znaleziono strony na Filmweb.pl"
                    role="img"
                  ></span>
                `;

          return `
            <article class="mini-card">
              ${poster}

              <div class="mini-body">
                <div class="card-top">
                  ${sectionPill}
                  ${mediaPill}
                  ${yearPill}
                  ${addedDatePill}
                </div>

                <h3 class="mini-title">${escapeHtml(item.title)}</h3>

                ${englishTitle}
                ${renderGenreLine(item.genres, "mini-genre")}
                ${overview}
                ${note}

                ${filmwebLink}
              </div>
            </article>
          `;
        }).join("");
      }

      if (!shelf) {
        return;
      }

      const remainingMovies = Math.max(0, state.movieItems.length - state.visibleMovieCount);
      const remainingTv = Math.max(0, state.tvItems.length - state.visibleTvCount);
      const actionButtons = [];

      if (remainingMovies > 0) {
        actionButtons.push(`
          <button
            class="ghost"
            type="button"
            data-role="show-more-news"
            data-platform="${escapeHtml(platform)}"
            data-kind="movie"
          >
            Pokaż więcej filmów (${remainingMovies})
          </button>
        `);
      }

      if (!state.showTv && state.tvItems.length > 0) {
        actionButtons.push(`
          <button
            class="ghost"
            type="button"
            data-role="show-tv-news"
            data-platform="${escapeHtml(platform)}"
          >
            Pokaż seriale (${state.tvItems.length})
          </button>
        `);
      }

      if (state.showTv && remainingTv > 0) {
        actionButtons.push(`
          <button
            class="ghost"
            type="button"
            data-role="show-more-news"
            data-platform="${escapeHtml(platform)}"
            data-kind="tv"
          >
            Pokaż więcej seriali (${remainingTv})
          </button>
        `);
      }

      if (actionButtons.length > 0) {
        shelf.insertAdjacentHTML("beforeend", `
          <div class="shelf-actions">
            ${actionButtons.join("")}
          </div>
        `);
      }
    }

    async function loadHomeShelf(platform) {
      const state = homeShelfState[platform];

      if (!state) {
        return;
      }

      state.loading = true;
      state.error = "";
      state.rawItems = [];
      state.movieItems = [];
      state.tvItems = [];
      state.enrichedMovies = [];
      state.enrichedTv = [];
      state.visibleMovieCount = HOME_GRID_SIZE;
      state.visibleTvCount = 0;
      state.showTv = false;
      state.loadToken = (Number(state.loadToken) || 0) + 1;

      renderHomeShelf(platform);

      try {
        const payload = await upflixRequest(platform);

        state.rawItems = Array.isArray(payload.items) ? payload.items : [];
        state.movieItems = state.rawItems.filter((item) => item && item.mediaType === "movie");
        state.tvItems = state.rawItems.filter((item) => item && item.mediaType === "tv");

        ensureHomeShelfItems(platform, "movie", state.visibleMovieCount);
      } catch (error) {
        state.error = error instanceof Error
          ? error.message
          : "Nie udało się pobrać nowości.";
      } finally {
        state.loading = false;
        renderHomeShelf(platform);
      }
    }

    async function loadHomeSections(force = false) {
      if (homeLoaded && !force) {
        showHomeSections();
        renderHomeShelf("netflix");
        renderHomeShelf("hbomax");
        return;
      }

      const operationId = ++homeNewsOperationId;

      showHomeSections({ loading: true });

      await Promise.allSettled([
        loadHomeShelf("netflix"),
        loadHomeShelf("hbomax"),
      ]);

      if (operationId !== homeNewsOperationId) {
        return;
      }

      homeLoaded = true;
      showHomeSections();
    }

    function randomPage(totalPages) {
      return Math.max(1, Math.floor(Math.random() * Math.max(1, totalPages)) + 1);
    }

    async function getRandomSuggestion(platformKey, typeKey, region) {
      const providerId = (PLATFORM_FILTERS[platformKey] || PLATFORM_FILTERS.all).providerId;

      const movieOptions = {
        providerId,
        page: 1,
        sortBy: "popularity.desc",
        minVotes: 150,
      };

      const tvOptions = {
        providerId,
        page: 1,
        sortBy: "popularity.desc",
        minVotes: 80,
      };

      const shouldUseMovies = typeKey === "all" || typeKey === "movie";
      const shouldUseTv = typeKey === "all" || typeKey === "tv";
      const seedRequests = [];

      if (shouldUseMovies) {
        seedRequests.push(discoverByMediaType("/discover/movie", "movie", movieOptions));
      }

      if (shouldUseTv) {
        seedRequests.push(discoverByMediaType("/discover/tv", "tv", tvOptions));
      }

      const seeds = await Promise.all(seedRequests);
      const poolRequests = [];

      seeds.forEach((seed) => {
        if (seed.results.length === 0) {
          return;
        }

        if (seed.results[0].media_type === "movie") {
          poolRequests.push(
            discoverByMediaType("/discover/movie", "movie", {
              ...movieOptions,
              page: randomPage(seed.totalPages),
            })
          );
        } else if (seed.results[0].media_type === "tv") {
          poolRequests.push(
            discoverByMediaType("/discover/tv", "tv", {
              ...tvOptions,
              page: randomPage(seed.totalPages),
            })
          );
        }
      });

      const pools = await Promise.all(poolRequests);

      const pool = dedupeSearchResults(
        pools.flatMap((group) => group.results)
      ).filter((item) => item && item.poster_path);

      if (!pool.length) {
        return null;
      }

      const selected = pool[Math.floor(Math.random() * pool.length)];

      return enrichCatalogItem(selected, region, {
        badge: "Losowy wybór",
      });
    }

    function clearResults(shouldFocus = false) {
      searchOperationId += 1;
      activeSearchState = null;

      if (queryInput) {
        queryInput.value = "";
      }

      if (resultsEl) {
        resultsEl.innerHTML = "";
        resultsEl.classList.add("hidden");
        resultsEl.classList.remove("has-rendered");
      }

      setMessage("", "");
      setStatus("");
      setRandomStatus("");
      hideHomeSections();
      hideReleaseCalendar();
      hideRandomPanel();

      const url = new URL(window.location.href);
      url.searchParams.delete("q");
      url.searchParams.delete("region");

      window.history.replaceState({}, "", url);

      if (shouldFocus && queryInput) {
        queryInput.focus();
      }
    }

    function renderResults(items, region, options = {}) {
      if (!items.length) {
        resultsEl.innerHTML = "";
        resultsEl.className = "results hidden";
        clearLoadingStatuses();

        setMessage(
          "info",
          `Brak dopasowań albo nie udało się znaleźć danych VOD w Filmwebie.`
        );

        hideHomeSections();
        hideReleaseCalendar();

        return;
      }

      hideHomeSections();
      hideReleaseCalendar();

      const hadRenderedResults = resultsEl.classList.contains("has-rendered");

      resultsEl.className = [
        "results",
        items.length === 1 ? "single-result" : "",
        hadRenderedResults ? "has-rendered" : "",
      ].filter(Boolean).join(" ");

      const cards = items.map((item, index) => {
        const badgeLabel = item.badge || (index === 0 ? "Najtrafniejsze" : "");

        const pills = [
          badgeLabel ? `<span class="pill highlight">${escapeHtml(badgeLabel)}</span>` : "",
          `<span class="pill">${item.mediaType === "movie" ? "Film" : "Serial"}</span>`,
          item.year ? `<span class="pill">${escapeHtml(item.year)}</span>` : "",
        ].join("");

        const poster = item.poster
          ? `<img class="poster" src="${escapeHtml(item.poster)}" alt="Plakat: ${escapeHtml(item.title)}">`
          : '<div class="poster-fallback">Brak plakatu</div>';

        const englishTitle = item.englishTitle
          ? `<p class="alt-title">${escapeHtml(item.englishTitle)}</p>`
          : "";

        const overview = item.overview
          ? `
            <div class="description-block">
              <button
                class="description-toggle"
                type="button"
                data-role="toggle-description"
                aria-expanded="false"
              >
                Pokaż opis
              </button>

              <p class="overview">${escapeHtml(item.overview)}</p>
            </div>
          `
          : "";

        const groupsMarkup = item.providers && item.providers.loading
          ? '<p class="overview provider-loading">Sprawdzam dostępność VOD...</p>'
          : item.providers.hasData
          ? `
            <div class="groups">
              ${Object.entries(item.providers.groups).map(([label, providers]) => `
                <section>
                  <h3 class="group-title">${escapeHtml(label)}</h3>

                  <div class="provider-list">
                    ${providers.map((provider) => {
                      const providerName = providerDisplayName(provider.name);

                      return `
                        ${provider.url
                          ? `<a class="provider" href="${escapeHtml(provider.url)}" target="_blank" rel="noreferrer noopener">`
                          : '<span class="provider">'
                        }
                          ${provider.logo ? `<img src="${escapeHtml(provider.logo)}" alt="">` : ""}
                          <span title="${escapeHtml(provider.name)}">${escapeHtml(providerName)}</span>
                        ${provider.url ? "</a>" : "</span>"}
                      `;
                    }).join("")}
                  </div>
                </section>
              `).join("")}
            </div>
          `
          : '<p class="overview">Niedostępny na VOD.</p>';

        const filmwebButton = item.filmwebUrl
          ? `
            <a
              class="filmweb-link card-filmweb-button"
              href="${escapeHtml(item.filmwebUrl)}"
              target="_blank"
              rel="noreferrer noopener"
              aria-label="Sprawdź na Filmweb.pl"
            ></a>
          `
          : item.filmwebLoading
            ? `
              <span
                class="filmweb-link card-filmweb-button is-loading-filmweb"
                aria-label="Szukam strony na Filmweb.pl"
                role="img"
              ></span>
            `
            : `
              <span
                class="filmweb-link card-filmweb-button is-disabled-filmweb"
                aria-label="Nie znaleziono strony na Filmweb.pl"
                role="img"
              ></span>
            `;

        return `
          <article class="card" style="--index: ${index};">
            <div class="card-poster-column">
              ${poster}
              ${filmwebButton}
            </div>

            <div class="card-content">
              <div class="card-top">${pills}</div>

              <h2>${escapeHtml(item.title)}</h2>

              ${englishTitle}
              ${renderGenreLine(item.genres)}
              ${overview}
              ${groupsMarkup}
            </div>
          </article>
        `;
      }).join("");

      const remainingCount = Math.max(0, Number(options.remainingCount) || 0);

      const actions = remainingCount > 0
        ? `
          <div class="results-actions">
            <button class="ghost" type="button" data-role="show-more-results">
              Pokaż więcej (${remainingCount})
            </button>
          </div>
        `
        : "";

      resultsEl.innerHTML = cards + actions;
      resultsEl.classList.add("has-rendered");

      setMessage("", "");
      clearLoadingStatuses();
    }

    function searchVisibleItems(searchState) {
      return searchState.items.slice(0, searchState.visibleCount);
    }

    function ensureBaseSearchRenderedItems(searchState) {
      const visibleItems = searchVisibleItems(searchState);

      searchState.renderedItems = Array.isArray(searchState.renderedItems)
        ? searchState.renderedItems
        : [];

      visibleItems.forEach((item, index) => {
        if (!searchState.renderedItems[index]) {
          searchState.renderedItems[index] = baseSearchResultItem(item, searchState.region);
        }
      });

      return visibleItems;
    }

    function renderSearchState(searchState) {
      renderResults(searchState.renderedItems.slice(0, searchState.visibleCount), searchState.region, {
        remainingCount: Math.max(0, searchState.items.length - searchState.visibleCount),
      });
    }

    function applySearchEnrichmentValue(searchState, targetIndex, value) {
      if (!value) {
        const existingItem = searchState.renderedItems[targetIndex];

        if (existingItem && existingItem.providers?.loading) {
          searchState.renderedItems[targetIndex] = {
            ...existingItem,
            providers: emptyProviderData("tmdb"),
          };

          return true;
        }

        return false;
      }

      searchState.renderedItems[targetIndex] = value;

      return true;
    }

    function scheduleSearchRender(searchState) {
      if (searchState.renderScheduled) {
        return;
      }

      searchState.renderScheduled = true;

      requestAnimationFrame(() => {
        searchState.renderScheduled = false;

        if (activeSearchState === searchState) {
          renderSearchState(searchState);
        }
      });
    }

    function enrichVisibleSearchItems(searchState) {
      const visibleItems = ensureBaseSearchRenderedItems(searchState);
      const pendingItems = visibleItems
        .map((item, index) => ({ item, index }))
        .filter(({ index }) => {
          const renderedItem = searchState.renderedItems[index];

          return !renderedItem || renderedItem.providers?.loading;
        });

      if (!pendingItems.length) {
        return Promise.resolve(false);
      }

      const perItem = pendingItems.map(({ item, index }) =>
        enrichSearchResult(item, searchState.region)
          .then(
            (value) => activeSearchState === searchState && applySearchEnrichmentValue(searchState, index, value),
            () => activeSearchState === searchState && applySearchEnrichmentValue(searchState, index, null)
          )
          .then((applied) => {
            if (applied) {
              scheduleSearchRender(searchState);
            }

            return applied === true;
          })
      );

      return Promise.allSettled(perItem).then((results) => {
        scheduleFilmwebProviderUpgrade(searchState);

        return results.some((result) => result.status === "fulfilled" && result.value === true);
      });
    }

    function scheduleFilmwebProviderUpgrade(searchState) {
      if (!searchState || activeSearchState !== searchState) {
        return;
      }

      const visibleItems = searchVisibleItems(searchState);
      const jobs = [];

      visibleItems.forEach((item, index) => {
        const rendered = searchState.renderedItems[index];

        if (!rendered || rendered.providers?.loading || rendered.filmwebLoading === false) {
          return;
        }

        jobs.push(
          resolveFilmwebMatch(item)
            .then((match) => ({ index, match }))
            .catch(() => null)
        );
      });

      if (!jobs.length) {
        return;
      }

      Promise.allSettled(jobs).then((results) => {
        if (activeSearchState !== searchState) {
          return;
        }

        let changed = false;

        results.forEach((result) => {
          if (result.status !== "fulfilled" || !result.value) {
            return;
          }

          const { index, match } = result.value;
          const current = searchState.renderedItems[index];

          if (!current) {
            return;
          }

          const filmwebProviders = match && match.providers && match.providers.hasData
            ? match.providers
            : null;

          searchState.renderedItems[index] = {
            ...current,
            providers: filmwebProviders || current.providers,
            filmwebUrl: match && match.url ? match.url : current.filmwebUrl,
            filmwebLoading: false,
          };
          changed = true;
        });

        if (changed) {
          renderSearchState(searchState);
        }
      });
    }

    function renderActiveSearchResults(options = {}) {
      const currentState = activeSearchState;

      if (!currentState) {
        return Promise.resolve(false);
      }

      ensureBaseSearchRenderedItems(currentState);
      currentState.renderVersion = (Number(currentState.renderVersion) || 0) + 1;
      const renderVersion = currentState.renderVersion;

      if (activeSearchState !== currentState) {
        return Promise.resolve(false);
      }

      renderSearchState(currentState);

      if (options.scheduleEnrichment === false) {
        return Promise.resolve(false);
      }

      return enrichVisibleSearchItems(currentState).then((hasUpdates) => {
        if (activeSearchState !== currentState || currentState.renderVersion !== renderVersion) {
          return false;
        }

        if (hasUpdates) {
          renderSearchState(currentState);
        }

        return hasUpdates;
      });
    }

    async function searchCatalog(query, region) {
      const [movieResults, tvResults] = await Promise.all([
        searchByMediaType("/search/movie", "movie", query),
        searchByMediaType("/search/tv", "tv", query),
      ]);

      const rankedItems = dedupeSearchResults([
        ...movieResults,
        ...tvResults,
      ])
        .filter(hasPremiered)
        .map((item) => ({
          item,
          rank: scoreSearchCandidate(item, query),
        }))
        .sort((left, right) => {
          const scoreDelta = right.rank.score - left.rank.score;

          if (scoreDelta !== 0) {
            return scoreDelta;
          }

          return (Number(right.item.popularity) || 0) - (Number(left.item.popularity) || 0);
      });

      let items = rankedItems.map(({ item }) => item);

      return {
        items,
      };
    }

    function promoteActiveSearchCollection(searchState) {
      if (!searchState || !Array.isArray(searchState.items)) {
        return Promise.resolve({
          changed: false,
          items: searchState ? searchState.items : [],
        });
      }

      const query = searchState.query || "";

      return findPromotedMovieCollection(searchState.items, query)
        .then((promotedCollection) => {
          if (activeSearchState !== searchState || !promotedCollection) {
            return {
              changed: false,
              items: searchState.items,
            };
          }

          const previousOrder = searchState.items.map((item) => `${item.media_type}:${item.id}`).join("|");
          const promotedItems = promoteMovieSeriesItems(
            searchState.items,
            promotedCollection.leadMovie,
            promotedCollection.collectionData
          );
          const nextOrder = promotedItems.map((item) => `${item.media_type}:${item.id}`).join("|");

          if (previousOrder === nextOrder) {
            return {
              changed: false,
              items: searchState.items,
            };
          }

          return {
            changed: true,
            items: promotedItems,
          };
        })
        .catch(() => ({
          changed: false,
          items: searchState.items,
        }));
    }

    function scheduleSearchBackgroundRefresh(searchState) {
      if (!searchState) {
        return;
      }

      searchState.refreshToken = (Number(searchState.refreshToken) || 0) + 1;
      const refreshToken = searchState.refreshToken;

      Promise.allSettled([
        enrichVisibleSearchItems(searchState),
        promoteActiveSearchCollection(searchState),
      ]).then(([enrichmentResult, promotionResult]) => {
        if (activeSearchState !== searchState || searchState.refreshToken !== refreshToken) {
          return Promise.resolve(false);
        }

        const enriched = enrichmentResult.status === "fulfilled" && enrichmentResult.value === true;
        const promotion = promotionResult.status === "fulfilled"
          ? promotionResult.value
          : { changed: false, items: searchState.items };

        if (!promotion.changed) {
          return Promise.resolve(enriched);
        }

        searchState.items = promotion.items;
        searchState.renderedItems = [];

        if (searchState.visibleCount > searchState.items.length) {
          searchState.visibleCount = searchState.items.length;
        }

        if (searchState.visibleCount === 0 && searchState.items.length > 0) {
          searchState.visibleCount = Math.min(RESULTS_BATCH_SIZE, searchState.items.length);
        }

        if (searchState.visibleCount > 0) {
          ensureBaseSearchRenderedItems(searchState);
        }

        return enrichVisibleSearchItems(searchState).then(() => true);
      }).then((hasUpdates) => {
        if (!hasUpdates || activeSearchState !== searchState || searchState.refreshToken !== refreshToken) {
          return;
        }

        renderSearchState(searchState);
      }).catch(() => {});
    }

    async function handleRandomPick() {
      const region = DEFAULT_REGION;
      const platformKey = randomPlatformSelect.value || "all";
      const typeKey = randomTypeSelect.value || "all";
      const platformLabel = (PLATFORM_FILTERS[platformKey] || PLATFORM_FILTERS.all).label;

      const typeLabel = typeKey === "movie"
        ? "film"
        : typeKey === "tv"
          ? "serial"
          : "film lub serial";

      activeSearchState = null;

      setMessage("", "");
      setStatus("");
      setRandomStatus(`Losuję coś do obejrzenia: ${platformLabel}, ${typeLabel}`, { loading: true });

      resultsEl.classList.add("hidden");
      resultsEl.classList.remove("has-rendered");
      resultsEl.innerHTML = "";

      hideHomeSections();
      hideReleaseCalendar();

      try {
        const randomItem = await getRandomSuggestion(platformKey, typeKey, region);

        if (!randomItem) {
          setMessage("info", `Brak dostępnych tytułów do losowania dla: ${platformLabel}.`);
          hideHomeSections();
          return;
        }

        renderResults([randomItem], region);
      } catch (error) {
        setMessage(
          "error",
          error instanceof Error
            ? error.message
            : "Nie udało się wylosować tytułu."
        );

        hideHomeSections();
      } finally {
        clearLoadingStatuses();
      }
    }

    async function handleSearch(event) {
      event.preventDefault();

      const query = queryInput.value.trim();
      const region = DEFAULT_REGION;
      const operationId = searchOperationId + 1;

      if (!query) {
        setMessage("error", "Wpisz tytuł filmu albo serialu.");
        queryInput.focus();
        return;
      }

      searchOperationId = operationId;

      loadGenreMaps().catch(() => {});

      setMessage("", "");
      setStatus("Ładowanie wyników", { loading: true });

      resultsEl.classList.add("hidden");
      resultsEl.classList.remove("has-rendered");
      resultsEl.innerHTML = "";

      hideHomeSections();
      hideReleaseCalendar();
      hideRandomPanel();

      const url = new URL(window.location.href);
      url.searchParams.set("q", query);
      url.searchParams.delete("region");

      window.history.replaceState({}, "", url);

      try {
        const searchState = await searchCatalog(query, region);

        if (operationId !== searchOperationId) {
          return;
        }

        activeSearchState = {
          items: searchState.items,
          region,
          visibleCount: Math.min(RESULTS_BATCH_SIZE, searchState.items.length),
          renderedItems: [],
          query,
        };

        renderActiveSearchResults({ scheduleEnrichment: false });
        scheduleSearchBackgroundRefresh(activeSearchState);
      } catch (error) {
        setMessage(
          "error",
          error instanceof Error
            ? error.message
            : "Wystąpił nieznany błąd."
        );

        hideHomeSections();
      } finally {
        if (operationId === searchOperationId) {
          clearLoadingStatuses();
        }
      }
    }

    function restoreFromUrl() {
      if (!form || !queryInput) {
        return;
      }

      const params = new URLSearchParams(window.location.search);
      const query = (params.get("q") || "").trim();

      queryInput.value = query;

      if (query) {
        form.dispatchEvent(new Event("submit", { cancelable: true }));
      } else {
        hideHomeSections();
      }
    }

    listen(toggleHomeNewsButton, "click", async () => {
      const expanded = toggleHomeNewsButton.getAttribute("aria-expanded") === "true";

      if (expanded) {
        hideHomeSections();
        return;
      }

      activeSearchState = null;
      if (resultsEl) {
        resultsEl.innerHTML = "";
        resultsEl.classList.add("hidden");
        resultsEl.classList.remove("has-rendered");
      }
      hideReleaseCalendar();
      hideRandomPanel();

      setMessage("", "");
      setStatus("");

      const url = new URL(window.location.href);
      url.searchParams.delete("q");
      url.searchParams.delete("region");

      window.history.replaceState({}, "", url);

      await loadHomeSections();
    });

    if (homeSectionsEl) {
      homeSectionsEl.addEventListener("load", (event) => {
        const poster = event.target;

        if (poster instanceof HTMLImageElement && poster.classList.contains("mini-poster")) {
          poster.classList.remove("is-loading-poster");
        }
      }, true);

      homeSectionsEl.addEventListener("error", (event) => {
        const poster = event.target;

        if (poster instanceof HTMLImageElement && poster.classList.contains("mini-poster")) {
          poster.classList.remove("is-loading-poster");
        }
      }, true);
    }

    if (toggleReleaseCalendarButton) {
      toggleReleaseCalendarButton.addEventListener("click", () => {
        const expanded = toggleReleaseCalendarButton.getAttribute("aria-expanded") === "true";

        if (expanded) {
          hideReleaseCalendar();
          return;
        }

        activeSearchState = null;
        if (resultsEl) {
          resultsEl.innerHTML = "";
          resultsEl.classList.add("hidden");
          resultsEl.classList.remove("has-rendered");
        }

        setMessage("", "");
        setStatus("");
        hideRandomPanel();

        const url = new URL(window.location.href);
        url.searchParams.delete("q");
        url.searchParams.delete("region");

        window.history.replaceState({}, "", url);

        showReleaseCalendar();
      });
    }

    listen(queryInput, "focus", hidePanelsForSearchIntent);
    listen(queryInput, "input", hidePanelsForSearchIntent);

    listen(clearResultsButton, "click", () => clearResults(true));

    listen(document.getElementById("site-title"), "click", () => clearResults(true));

    listen(toggleRandomPanelButton, "click", () => {
      const expanded = toggleRandomPanelButton.getAttribute("aria-expanded") === "true";

      if (expanded) {
        hideRandomPanel();
        return;
      }

      showRandomPanel();
    });

    listen(randomPickButton, "click", handleRandomPick);
    listen(form, "submit", handleSearch);

    listen(homeSectionsEl, "click", (event) => {
      const descriptionButton = event.target.closest('[data-role="toggle-description"]');

      if (descriptionButton) {
        const container = descriptionButton.closest(".description-block");
        const overview = container ? container.querySelector(".overview") : null;

        if (!overview) {
          return;
        }

        const expanded = descriptionButton.getAttribute("aria-expanded") === "true";

        descriptionButton.setAttribute("aria-expanded", String(!expanded));
        descriptionButton.textContent = expanded ? "Pokaż opis" : "Ukryj opis";
        overview.classList.toggle("is-expanded", !expanded);

        return;
      }

      const showTvButton = event.target.closest('[data-role="show-tv-news"]');

      if (showTvButton) {
        const platform = showTvButton.getAttribute("data-platform");
        const state = homeShelfState[platform];

        if (!state || state.loading) {
          return;
        }

        state.showTv = true;
        state.visibleTvCount = Math.min(HOME_GRID_SIZE, state.tvItems.length);

        showTvButton.disabled = true;
        showTvButton.classList.add("is-loading");
        showTvButton.textContent = "Ładowanie seriali...";

        ensureHomeShelfItems(platform, "tv", state.visibleTvCount)
          .then(() => renderHomeShelf(platform))
          .catch(() => renderHomeShelf(platform));

        return;
      }

      const showMoreButton = event.target.closest('[data-role="show-more-news"]');

      if (!showMoreButton) {
        return;
      }

      const platform = showMoreButton.getAttribute("data-platform");
      const kind = showMoreButton.getAttribute("data-kind") === "tv" ? "tv" : "movie";
      const state = homeShelfState[platform];

      if (!state || state.loading) {
        return;
      }

      if (kind === "tv") {
        state.showTv = true;
        state.visibleTvCount = Math.min(
          state.visibleTvCount + HOME_SHOW_MORE_STEP,
          state.tvItems.length
        );
      } else {
        state.visibleMovieCount = Math.min(
          state.visibleMovieCount + HOME_SHOW_MORE_STEP,
          state.movieItems.length
        );
      }

      showMoreButton.disabled = true;
      showMoreButton.classList.add("is-loading");
      showMoreButton.textContent = "Ładowanie...";

      ensureHomeShelfItems(
        platform,
        kind,
        kind === "tv" ? state.visibleTvCount : state.visibleMovieCount
      )
        .then(() => renderHomeShelf(platform))
        .catch(() => renderHomeShelf(platform));
    });

    listen(releaseCalendarEl, "click", (event) => {
      const premiereButton = event.target.closest('[data-role="load-premieres"]');

      if (premiereButton) {
        const requestedKind = premiereButton.getAttribute("data-kind");
        const kind = requestedKind === "all" ? "all" : requestedKind === "tv" ? "tv" : "movie";
        loadPremieres(kind);
        return;
      }

      const nextMonthButton = event.target.closest('[data-role="load-next-premiere-month"]');

      if (nextMonthButton) {
        nextMonthButton.disabled = true;
        nextMonthButton.classList.add("is-loading");
        nextMonthButton.textContent = "Dociągam...";
        loadNextPremiereMonth();
        return;
      }

      const dayPremieresButton = event.target.closest('[data-role="show-day-premieres"]');

      if (dayPremieresButton) {
        openCalendarDayDialog(dayPremieresButton.getAttribute("data-date") || "");
        return;
      }

      const descriptionButton = event.target.closest('[data-role="toggle-description"]');

      if (!descriptionButton) {
        return;
      }

      const container = descriptionButton.closest(".description-block");
      const overview = container ? container.querySelector(".overview") : null;

      if (!overview) {
        return;
      }

      const expanded = descriptionButton.getAttribute("aria-expanded") === "true";

      descriptionButton.setAttribute("aria-expanded", String(!expanded));
      descriptionButton.textContent = expanded ? "Pokaż opis" : "Ukryj opis";
      overview.classList.toggle("is-expanded", !expanded);
    });
    listen(resultsEl, "click", (event) => {
      const showMoreButton = event.target.closest('[data-role="show-more-results"]');

      if (showMoreButton) {
        if (!activeSearchState || activeSearchState.visibleCount >= activeSearchState.items.length) {
          return;
        }

        showMoreButton.disabled = true;
        showMoreButton.classList.add("is-loading");
        showMoreButton.textContent = "Ładowanie...";

        activeSearchState.visibleCount = Math.min(
          activeSearchState.visibleCount + RESULTS_BATCH_SIZE,
          activeSearchState.items.length
        );

        setStatus("Doczytuję więcej wyników", { loading: true });

        renderActiveSearchResults();
        setStatus("");

        return;
      }

      const button = event.target.closest('[data-role="toggle-description"]');

      if (!button) {
        return;
      }

      const container = button.closest(".description-block");
      const overview = container ? container.querySelector(".overview") : null;

      if (!overview) {
        return;
      }

      const expanded = button.getAttribute("aria-expanded") === "true";

      button.setAttribute("aria-expanded", String(!expanded));
      button.textContent = expanded ? "Pokaż opis" : "Ukryj opis";
      overview.classList.toggle("is-expanded", !expanded);
    });

    document.addEventListener("click", (event) => {
      if (
        event.target.classList.contains("calendar-day-dialog-backdrop")
        || event.target.closest(".calendar-day-dialog-close")
      ) {
        closeCalendarDayDialog();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeCalendarDayDialog();
      }
    });

    restoreFromUrl();
  </script>
</body>
</html>
