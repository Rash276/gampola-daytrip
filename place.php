<?php
require_once __DIR__ . '/config.php';

$place = place_by_id((int)($_GET['id'] ?? 0));

if (!$place) {
    http_response_code(404);
    $pageTitle = 'Place not found';
    require __DIR__ . '/includes/header.php';

    echo '<section class="section empty-state">
            <h2>We couldn’t find that place.</h2>
            <a class="button button-dark" href="index.php">Back to explore</a>
          </section>';

    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $place['name'];
$active = 'explore';
$added = in_array(
    (int)$place['id'],
    array_map('intval', $_SESSION['plan'] ?? []),
    true
);

require __DIR__ . '/includes/header.php';
?>

<section class="detail-hero">
  <a class="back-link" href="index.php">← All places</a>

  <div class="detail-hero-content">
    <span class="category-pill"><?= e($place['category']) ?></span>
    <h1><?= e($place['name']) ?></h1>
    <p><?= e($place['short_description']) ?></p>

    <div class="detail-stats">
      <span>⌖ <?= number_format((float)$place['distance_km'], 1) ?> km from Gampola</span>
      <span>◷ About <?= (int)$place['visit_minutes'] ?> min</span>
    </div>
  </div>

  <?php if (!empty($place['image_url'])): ?>
    <div
      class="detail-art detail-photo"
      style="background-image: url('<?= e($place['image_url']) ?>')"
      aria-hidden="true">
    </div>
  <?php else: ?>
    <div class="detail-art" aria-hidden="true">
      <span class="detail-art-sun"></span>
      <span class="detail-art-hill"></span>
      <span class="detail-art-hill second"></span>
      <span class="detail-art-label">A LOCAL PLACE TO PAUSE</span>
    </div>
  <?php endif; ?>
</section>

<section class="detail-layout section">
  <article class="detail-main">
    <div class="eyebrow eyebrow-dark">A LITTLE ABOUT IT</div>
    <h2>Make room for a good moment.</h2>

    <p class="detail-description">
      <?= nl2br(e($place['description'])) ?>
    </p>

    <div class="tips-box">
      <span class="tips-icon">✳</span>
      <div>
        <strong>A local tip</strong>
        <p><?= nl2br(e($place['travel_tips'])) ?></p>
      </div>
    </div>

    <div class="detail-map-head">
      <div>
        <div class="eyebrow eyebrow-dark">FIND YOUR WAY</div>
        <h3>Where it is</h3>
      </div>

      <a
        class="text-link"
        target="_blank"
        rel="noopener"
        href="https://www.google.com/maps/search/?api=1&amp;query=<?= urlencode((string)$place['latitude'] . ',' . (string)$place['longitude']) ?>">
        Open in Maps ↗
      </a>
    </div>

    <div class="map-frame">
      <iframe
        title="Map showing <?= e($place['name']) ?>"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        src="https://www.openstreetmap.org/export/embed.html?bbox=<?= (float)$place['longitude'] - 0.012 ?>%2C<?= (float)$place['latitude'] - 0.009 ?>%2C<?= (float)$place['longitude'] + 0.012 ?>%2C<?= (float)$place['latitude'] + 0.009 ?>&amp;layer=mapnik&amp;marker=<?= (float)$place['latitude'] ?>%2C<?= (float)$place['longitude'] ?>">
      </iframe>
    </div>

    <small class="map-note">
      Map tiles require an internet connection. The address and coordinates remain available if the map does not load.
    </small>
  </article>

  <aside class="visit-card">
    <span class="eyebrow eyebrow-dark">BEFORE YOU GO</span>
    <h3>Good to know</h3>

    <dl>
      <div>
        <dt>Opening information</dt>
        <dd><?= e($place['opening_info']) ?></dd>
      </div>
      <div>
        <dt>Entrance fee</dt>
        <dd><?= e($place['entrance_fee']) ?></dd>
      </div>
      <div>
        <dt>Best time</dt>
        <dd><?= e($place['best_time']) ?></dd>
      </div>
      <div>
        <dt>Facilities</dt>
        <dd><?= e($place['facilities']) ?></dd>
      </div>
      <div>
        <dt>Approx. distance</dt>
        <dd><?= number_format((float)$place['distance_km'], 1) ?> km from Gampola</dd>
      </div>
    </dl>

    <p class="verify-note">
      Opening hours, fees and facilities can change. Please verify with the site before travelling.
    </p>

    <form method="post" action="plan_action.php">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="place_id" value="<?= (int)$place['id'] ?>">
      <input type="hidden" name="return" value="place.php?id=<?= (int)$place['id'] ?>">

      <button
        class="button <?= $added ? 'button-outline' : 'button-dark' ?> button-full"
        <?= $added ? 'disabled' : '' ?>>
        <?= $added ? '✓ In your day plan' : '＋ Add to my day plan' ?>
      </button>
    </form>

    <span class="estimate-note">
      It takes about <?= (int)$place['visit_minutes'] ?> minutes to explore
    </span>
  </aside>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>