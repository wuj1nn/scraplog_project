<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'earnings';
require_page($page);
$pageScripts = ['earnings.js'];
include __DIR__ . '/includes/header.php';
?>
<section class="panel active" id="panel-earnings">
      <div class="toolbar">
        <div class="toolbarGroup"><label>View</label>
          <div class="seg" id="earningsSeg">
            <button class="active" data-type="donut">&#9673; Donut</button>
            <button data-type="bar">&#9646; Bar</button>
            <button data-type="table">&#8801;Table</button>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="cardHead"><div><h2>Projected earnings this month</h2><div class="cardDesc">Based on current stock and today's price legend</div></div></div>
        <div id="earningsDonutView">
          <div class="gaugeWrap">
            <div class="gaugeBox"><canvas id="earningsDonutChart"></canvas>
              <div class="gaugeCenter"><span class="icon">₱</span><b id="earningsTotal"></b><span>projected</span></div>
            </div>
            <div class="gaugeLegend" id="earningsLegend"></div>
          </div>
        </div>
        <div class="chartCanvasWrap hidden" id="earningsBarWrap"><canvas id="earningsBarChart"></canvas></div>
        <div class="tableStage" id="earningsTableStage">
          <div class="tableWrap"><table>
            <thead><tr><th>Category</th><th class="num">On hand</th><th class="num">Sell price / kg</th><th class="num">Projected value</th></tr></thead>
            <tbody id="earningsTableBody"></tbody>
          </table></div>
        </div>
      </div>
</section>
<?php
include __DIR__ . '/includes/footer.php';
