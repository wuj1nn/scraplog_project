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
      </div>
      <div class="card">
        <div class="cardHead"><div><h2 id="analyticsTitle">Trend over time</h2><div class="cardDesc" id="analyticsDesc">Total material sorted in vs. sold</div></div></div>
        <div class="chartStage">
          <div class="chartCanvasWrap" id="analyticsCanvasWrap"><canvas id="analyticsCanvas"></canvas></div>
          <div class="tableStage" id="analyticsTableStage">
            <div class="tableWrap"><table>
              <thead><tr><th>Category</th><th class="num">Sorted in (kg)</th><th class="num">Sold (kg)</th><th class="num">Net change (kg)</th><th class="num">Value on hand</th></tr></thead>
              <tbody id="analyticsTableBody"></tbody>
            </table></div>
          </div>
        </div>
        <div class="legendRow" id="analyticsLegend"></div>
      </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
