<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'analytics';
require_page($page);
$pageScripts = ['analytics.js'];
include __DIR__ . '/includes/header.php';
?>
<section class="panel active" id="panel-analytics">
      <div class="toolbar">
        <div class="toolbarGroup"><label>Category</label>
          <select id="catFilter">
            <option value="all">All categories</option>
            <option value="bakal">Bakal (Iron/Steel)</option>
            <option value="tanso">Tanso (Copper)</option>
            <option value="aluminum">Aluminum</option>
            <option value="plastik">Plastik (Plastic)</option>
            <option value="karton">Karton/Papel</option>
            <option value="bote">Bote (Glass)</option>
            <option value="ewaste">E-waste</option>
          </select>
        </div>
        <div class="toolbarGroup hidden" id="itemGroup"><label>Item</label>
          <select id="itemFilter"></select>
        </div>
        <div class="toolbarGroup"><label>Timeframe</label>
          <div class="seg" id="timeframeSeg">
            <button class="active" data-tf="day">Today</button>
            <button data-tf="week">Week</button>
            <button data-tf="month">Month</button>
            <button data-tf="year">Year</button>
          </div>
        </div>
        <div class="chartToggle" id="chartToggle">
          <button class="active" data-type="line">&#8599;&#xFE0E; Line</button>
          <button data-type="bar">&#9646; Bar</button>
          <button data-type="table">&#8801; Table</button>
        </div>
        <button type="button" class="btnOutline printBtn" id="printOpen">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6"/><rect x="6" y="14" width="12" height="7"/><path d="M6 17H4a1 1 0 01-1-1v-5a2 2 0 012-2h14a2 2 0 012 2v5a1 1 0 01-1 1h-2"/></svg>
          Print report
        </button>
      </div>
      <div class="card">
        <div class="cardHead"><div><h2 id="analyticsTitle">Trend over time</h2><div class="cardDesc" id="analyticsDesc">Total material sorted in vs. sold</div></div></div>
        <div class="chartStage">
          <div class="chartCanvasWrap" id="analyticsCanvasWrap"><canvas id="analyticsCanvas"></canvas></div>
          <div class="tableStage" id="analyticsTableStage">
            <div class="tableWrap"><table>
              <thead><tr><th>Material</th><th class="num">Sorted in (kg)</th><th class="num">Sold (kg)</th><th class="num">Removed (kg)</th><th class="num">Net change (kg)</th><th class="num">Value on hand</th></tr></thead>
              <tbody id="analyticsTableBody"></tbody>
            </table></div>
          </div>
        </div>
        <div class="legendRow" id="analyticsLegend"></div>
      </div>
</section>

<div class="modalScrim" id="rptModal">
  <div class="modalBox wide" role="dialog" aria-modal="true" aria-labelledby="rptTitle">
    <div class="modalHead">
      <div><h2 id="rptTitle">Print report</h2><div class="cardDesc">Check the page below, then press Print</div></div>
      <button type="button" class="modalClose" id="rptClose" aria-label="Close">&times;</button>
    </div>
    <div class="modalTools">
      <div class="seg" id="rptTf">
        <button data-tf="day">Today</button>
        <button data-tf="week">Week</button>
        <button data-tf="month">Month</button>
        <button data-tf="year">Year</button>
      </div>
      <select id="rptMode" aria-label="Report contents">
        <option value="both">Sorted in &amp; sold</option>
        <option value="out">Sold only</option>
        <option value="in">Sorted in only</option>
      </select>
      <select id="rptPaper" aria-label="Paper size">
        <option value="A4">A4</option>
        <option value="Letter">Letter (short)</option>
        <option value="Long">Long (8.5 × 13)</option>
      </select>
      <button type="button" class="btnCta rptPrint" id="rptPrint">Print</button>
    </div>
    <div class="rptStage"><div class="paper" id="rptPaperEl"></div></div>
  </div>
</div>
<?php
include __DIR__ . '/includes/footer.php';
