<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'storage';
require_page($page);
$pageScripts = ['storage.js'];
include __DIR__ . '/includes/header.php';
?>
<section class="panel active" id="panel-storage">
      <div class="toolbar">
        <div class="toolbarGroup"><label>View</label>
          <div class="seg" id="storageSeg">
            <button class="active" data-type="donut">&#9673; Donut</button>
            <button data-type="bar">&#9646; Bar</button>
            <button data-type="table">&#8801; Table</button>
          </div>
        </div>
        <button type="button" class="btnOutline historyBtn" id="historyOpen">&#8634; History</button>
      </div>      <div class="card">
        <div class="cardHead"><div><h2 id="storageTitle">Stock proportion by category</h2><div class="cardDesc" id="storageDesc"></div></div></div>
        <div class="chartStage">
          <div id="storageDonutView">
          <div class="gaugeWrap">
            <div class="gaugeBox"><canvas id="storageDonutChart"></canvas>
              <div class="gaugeCenter"><span class="icon">&#9637;</span><b id="storageDonutTotal"></b><span>kg on hand</span></div>
            </div>
            <div class="gaugeLegend" id="storageLegend"></div>
          </div>
        </div>
        <div class="chartCanvasWrap hidden" id="storageCanvasWrap"><canvas id="storageCanvas"></canvas></div>
          <div class="tableStage" id="storageTableStage">
            <div class="tableWrap"><table>
              <thead><tr><th>Category</th><th>Capacity</th><th class="num">On hand</th><?php if($role === 'admin'): ?><th class="num">Value</th><?php endif; ?></tr></thead>
              <tbody id="storageTableBody"></tbody>
            </table></div>
          </div>
        </div>
      </div>
</section>

<div class="modalScrim" id="historyModal">
  <div class="modalBox" role="dialog" aria-modal="true" aria-labelledby="historyTitle">
    <div class="modalHead">
      <div><h2 id="historyTitle">Storage history</h2><div class="cardDesc">Everything that was sorted into storage</div></div>
      <button type="button" class="modalClose" id="historyClose" aria-label="Close">&times;</button>
    </div>
    <div class="modalTools">
      <div class="seg" id="historyRange">
        <button data-range="1">Today</button>
        <button data-range="7">7 days</button>
        <button class="active" data-range="30">30 days</button>
        <button data-range="365">1 year</button>
      </div>
      <select id="historyCat"></select>
      <select id="historySort" class="sortSel">
        <option value="recent">Sort: Most recent</option>
        <option value="oldest">Sort: Oldest first</option>
        <option value="name">Sort: Supplier name (A-Z)</option>
        <option value="type">Sort: Material type (A-Z)</option>
        <option value="heavy">Sort: Heaviest first</option>
        <option value="light">Sort: Lightest first</option>
      </select>
    </div>
    <div class="modalBody tableWrap">
      <table>
        <thead><tr><th>Date &amp; time</th><th>Category</th><th>Supplier</th><th class="num">Kg</th></tr></thead>
        <tbody id="historyBody"></tbody>
      </table>
    </div>
    <div class="modalFoot" id="historyFoot"></div>
  </div>
</div>

<?php
include __DIR__ . '/includes/footer.php';
