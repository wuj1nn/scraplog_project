<?php
require_once __DIR__ . '/includes/auth.php';

if(current_user()){
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ScrapLog</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="assets/js/landing.js"></script>
</head>
<body>

<header class="topheader">
  <a class="brandLink" href="index.php"><img src="assets/images/SCRAPLOG_nobg.png" alt="ScrapLog"></a>
  <nav class="lpNav">
    <a href="#features">Features</a>
    <a href="#why">Why ScrapLog</a>
  </nav>
  <div class="headerRight">
    <label class="themeSwitch">
      <input type="checkbox" id="lpTheme" aria-label="Toggle dark mode">
      <span class="themeTrack"><span class="themeKnob"></span></span>
    </label>
    <a class="btnCta" href="login.php">Login</a>
  </div>
</header>

<main class="main lpMain">

  <section class="lpHero">
    <div class="lpHeroText">
      <h1>Scrap<span>Log</span></h1>
      <p>Junkshop Inventory and Sales Analytics Management System</p>
      <a class="btnGold lpCta" href="#features">Explore our features</a>
      <div class="heroChips">
        <span>Bakal</span><span>Tanso</span><span>Aluminum</span><span>Plastik</span><span>Karton</span><span>Bote</span><span>E-waste</span>
      </div>
      <div class="lpPoints">
        <span>
          <svg width="24" height="24" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="8" width="30" height="34" rx="4"/><path d="M18 8V5h12v3M16 22l3 3 5-6M16 33h16"/></svg>
          Tracking
        </span>
        <span>
          <svg width="24" height="24" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 42h36M10 34l9-9 7 6 13-15M31 16h8v8"/></svg>
          Analytics
        </span>
        <span>
          <svg width="24" height="24" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 8l-6 10h8M14 18l-8 14M12 33h14l5-8M28 41l-6-8h10M32 33l10-14M39 10l-8-2-3 8M41 19l-2-9"/></svg>
          Sustainability
        </span>
      </div>
    </div>
    <div class="lpHeroImg">
      <img src="assets/images/hero.jpg" width="1600" height="795" alt="Sorted cardboard and scrap material stacked in front of a metal container">
    </div>
  </section>

  <section class="card lpSection" id="why">
    <span class="lpTag">System Comparison</span>
    <h2>Traditional Manual Logs vs. ScrapLog</h2>
    <p class="lpLead">See the clear advantage of replacing manual paper logs and spreadsheets with a centralized digital scrap management workflow.</p>
    <div class="lpCompare">
      <div class="lpCol lpBad">
        <h3>Traditional Manual Method</h3>
        <ul>
          <li><strong>Damaged or Lost Paper Logs:</strong> Paper receipts and logbooks can get stained, torn, or misplaced during heavy yard operations.</li>
          <li><strong>Manual Calculation Errors:</strong> Human mistakes during weight and payout calculation lead to direct financial loss.</li>
          <li><strong>Unknown Storage Stock:</strong> No real-time visibility into exact stock weights available in the yard for resale.</li>
          <li><strong>Time-Consuming Audits:</strong> Spending hours every evening matching receipts, cash flow, and daily material totals.</li>
        </ul>
      </div>
      <div class="lpCol lpGood">
        <h3>ScrapLog</h3>
        <ul>
          <li><strong>Secure Cloud Records:</strong> Every entry, weight, and supplier payout is instantly saved to the cloud securely.</li>
          <li><strong>Automated Payout Calculation:</strong> Select material type and weight, and ScrapLog automatically calculates exact totals based on current buying rates.</li>
          <li><strong>Real-Time Yard Inventory:</strong> Instantly check exact stock levels per material category (e.g., 412 kg Iron, 880 kg Cardboard).</li>
          <li><strong>Instant Financial Analytics:</strong> Generate one-click reports for total revenue, sales trends, and projected profit margins.</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="card lpSection" id="features">
    <h2>Built for Modern Scrap Yard Operations</h2>
    <p class="lpLead">Engineered specifically to handle high-frequency walk-ins, material sorting, pricing fluctuations, and inventory tracking.</p>
    <div class="lpGrid">
      <div class="card lpFeature">
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M24 8v30M16 40h16M6 14h36"/><path class="a" d="M6 14l-6 14h12z" transform="translate(3 0)"/><path class="a" d="M42 14l-6 14h12z" transform="translate(-9 0)"/></svg>
        Fast Sorting &amp; Weighing Entry
      </div>
      <div class="card lpFeature">
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle class="b" cx="24" cy="25" r="15"/><path class="a" d="M24 25V8a17 17 0 0 1 17 17z"/></svg>
        Live Category Breakdown
      </div>
      <div class="card lpFeature">
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path class="b" d="M6 24L24 6h16v16L22 40z"/><circle cx="33" cy="13" r="2.5"/></svg>
        Editable Price Legend
      </div>
      <div class="card lpFeature">
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect class="a" x="16" y="8" width="24" height="32" rx="3"/><rect class="b" x="8" y="12" width="24" height="32" rx="3"/><path d="M14 22h12M14 28h12M14 34h8"/></svg>
        Automated Sales &amp; Dispatch Logs
      </div>
      <div class="card lpFeature">
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle class="b" cx="24" cy="24" r="17"/><path d="M24 13v11l7 4"/></svg>
        Recent Activity Audit Trail
      </div>
      <div class="card lpFeature">
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path class="a" d="M24 5l14 5v11c0 9-6 15-14 20C16 36 10 30 10 21V10z"/><path class="b" d="M24 5l14 5v11c0 9-6 15-14 20z"/></svg>
        Multi-User &amp; Role Management
      </div>
    </div>
  </section>

  <div class="lpFoot">
    <b>ScrapLog</b>
    <span>Junkshop Inventory and Sales Analytics Management System</span>
  </div>

</main>
</body>
</html>