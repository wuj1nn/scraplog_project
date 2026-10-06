<section class="panel active" id="panel-dashboard">
      <div class="statGrid">
        <div class="statCard">
          <div><div class="statValue" id="statRevenue"></div><div class="statLabel">Total Revenue</div></div>
          <div class="ring" id="ringRevenue" style="--ringColor:#f4c430;--pct:0%;"><div class="ringInner">₱</div></div>
        </div>
        <div class="statCard">
          <div><div class="statValue"><span id="statStorage"></span> <span style="font-size:12px;color:#6b6b6b;">kg</span></div><div class="statLabel">Total Storage</div></div>
          <div class="ring" id="ringStorage" style="--ringColor:#c1743c;--pct:0%;"><div class="ringInner">&#9637;</div></div>
        </div>
        <div class="statCard">
          <div><div class="statValue" style="font-size:18px;" id="statLargest"></div><div class="statLabel">Largest Category &middot; <span id="statLargestKg"></span> kg</div></div>
          <div class="ring" id="ringLargest" style="--ringColor:#a8800c;--pct:0%;"><div class="ringInner">&#11041;</div></div>
        </div>
        <div class="statCard">
          <div><div class="statValue" id="statProjected"></div><div class="statLabel">Projected Earnings</div></div>
          <div class="ring" id="ringProjected" style="--ringColor:#3f9e6c;--pct:0%;"><div class="ringInner">&#9678;</div></div>
        </div>
      </div>

      <div class="widgetGrid">
        <div class="card">
          <div class="cardHead">
            <div><h2>Storage &amp; Sales Trend</h2><div class="cardDesc">Material sorted in vs. sold</div></div>
            <select class="chipSelect" id="dashTrendRange">
              <option value="week">This Week</option>
              <option value="month" selected>This Month</option>
              <option value="year">This Year</option>
            </select>
          </div>
          <div class="chartCanvasWrap"><canvas id="dashLineChart"></canvas></div>
          <div class="legendRow" id="dashLineLegend"></div>
        </div>

        <div class="card stockCard">
          <div class="cardHead"><div><h2>Stock by Category</h2><div class="cardDesc">Share of current storage</div></div></div>
          <div class="gaugeWrap">
            <div class="gaugeBox"><canvas id="dashDonutChart"></canvas>
              <div class="gaugeCenter"><span class="icon">&#9637;</span><b id="dashDonutTotal"></b><span>kg on hand</span></div>
            </div>
            <div class="gaugeLegend" id="dashDonutLegend"></div>
          </div>
          <div class="stockHealth">
            <p class="healthLabel">TODAY AT A GLANCE</p>
            <div class="healthRow">
              <div class="healthTile"><b id="todayIn">0</b><span>kg sorted in</span></div>
              <div class="healthTile"><b id="todaySold">0</b><span>kg sold</span></div>
              <div class="healthTile"><b id="todayNet">0</b><span>net change</span></div>
            </div>
          </div>
        </div>
      </div>
                      
      <p style="font-size:11.5px;color:#999;letter-spacing:.04em;margin:0 0 12px;">QUICK ACTIONS</p>
      <div class="quickGrid">
        <a class="quickCard" href="sort.php"><div class="ico">&#9636;</div><h3>Sort materials</h3><p>Log incoming scrap by category as it's weighed in.</p></a>
        <a class="quickCard" href="price.php"><div class="ico">&#8369;</div><h3>Edit price legend</h3><p>Adjust buying &amp; selling prices per kilo.</p></a>
        <a class="quickCard" href="sale.php"><div class="ico">&#9635;</div><h3>Record sale</h3><p>Pick material and quantity — stock updates automatically.</p></a>
      </div>

      <div class="card">
        <div class="cardHead"><h2>Recent Activity</h2></div>
        <div class="tableWrap scrollBox"><table>
          <thead><tr><th>Category</th><th>Type</th><th>Party</th><th class="num">Kg</th><th class="num">Amount</th></tr></thead>
          <tbody id="dashActivityBody"></tbody>
        </table></div>
      </div>
</section>
