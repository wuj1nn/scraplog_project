
  </main>
</div>

<?php require_once __DIR__ . '/data.php'; ?>
<script>const DB = <?= json_encode(load_app_data($pdo), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<script src="assets/js/data.js"></script>
<script src="assets/js/core.js"></script>
<script src="assets/js/charts.js"></script>
<script src="assets/js/nav.js"></script>
<?php foreach($pageScripts as $s): ?>
<script src="assets/js/<?= $s ?>"></script>
<?php endforeach; ?>
</body>
</html>
