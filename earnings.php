<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'earnings';
require_page($page);
$pageScripts = ['earnings.js'];
include __DIR__ . '/includes/header.php';
?>
<section class="panel active" id="panel-earnings">
      <div class="toolbar">
        <div class="toolbarGroup"><label>Timeframe</label>
          <div class="seg" id="earningsTf">
            <button data-tf="day">Today</button>
            <button data-tf="week">Week</button>
            <button class="active" data-tf="month">Month</button>
            <button data-tf="year">Year</button>
          </div>
        </div>
        <div class="toolbarGroup"><label>View</label>
          <div class="seg" id="earningsSeg">
            <button class="active" data-type="donut">&#9673; Donut</button>
            <button data-type="bar">&#9646; Bar</button>
            <button data-type="table">&#8801;Table</button>
          </div>
        </div>
      </div>

      <div class="statGrid three">
        <div class="statCard">
          <div><div class="statValue" id="statEarned"></div><div class="statLabel" id="lblEarned">Earned</div></div>
          <div class="ring" id="ringEarned" style="--ringColor:#f4c430;--pct:0%;"><div class="ringInner">₱</div></div>
        </div>
        <div class="statCard">
          <div><div class="statValue"><span id="statSoldKg"></span> <span style="font-size:12px;color:#6b6b6b;">kg</span></div><div class="statLabel" id="lblSold">Sold</div></div>
          <div class="ring" id="ringSold" style="--ringColor:#3f9e6c;--pct:0%;"><div class="ringInner">&#9635;</div></div>
        </div>
        <div class="statCard">
          <div><div class="statValue" id="statProjected"></div><div class="statLabel">Projected from stock on hand</div></div>
          <div class="ring" id="ringProjected" style="--ringColor:#c1743c;--pct:0%;"><div class="ringInner">&#9678;</div></div>
        </div>
      </div>

      <div class="card">
        <div class="cardHead"><div><h2>Earnings by category</h2><div class="cardDesc" id="earningsDesc"></div></div></div>
        <div id="earningsDonutView">
          <div class="gaugeWrap">
            <div class="gaugeBox"><canvas id="earningsDonutChart"></canvas>
              <div class="gaugeCenter"><span class="icon">₱</span><b id="earningsTotal"></b><span>earned</span></div>
            </div>
            <div class="gaugeLegend" id="earningsLegend"></div>
          </div>
        </div>
        <div class="chartCanvasWrap hidden" id="earningsBarWrap"><canvas id="earningsBarChart"></canvas></div>
        <div class="tableStage" id="earningsTableStage">
          <div class="tableWrap"><table>
            <thead><tr><th>Category</th><th class="num">Sold (kg)</th><th class="num">Earned</th><th class="num">On hand</th><th class="num">Projected value</th></tr></thead>
            <tbody id="earningsTableBody"></tbody>
          </table></div>
        </div>
      </div>

      <div class="card gapTop">
        <div class="cardHead"><div><h2>Earnings over time</h2><div class="cardDesc" id="earningsTrendDesc"></div></div></div>
        <div class="chartCanvasWrap"><canvas id="earningsTrendChart"></canvas></div>
      </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
