<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Find your next local day out'; $active = 'explore';
$search = trim((string)($_GET['q'] ?? '')); $category = (int)($_GET['category'] ?? 0); $maxDistance = (int)($_GET['distance'] ?? 25);
$sql = 'SELECT p.*, c.name category FROM places p JOIN categories c ON c.id=p.category_id WHERE p.distance_km <= 25';
$args = [];
if ($search !== '') { $sql .= ' AND (p.name LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?)'; $term='%'.$search.'%'; array_push($args,$term,$term,$term); }
if ($category > 0) { $sql .= ' AND c.id=?'; $args[]=$category; }
if (in_array($maxDistance,[5,10,15,20,25],true)) { $sql .= ' AND p.distance_km <= ?'; $args[]=$maxDistance; }
$sql .= ' ORDER BY p.distance_km, p.name'; $stmt=db()->prepare($sql); $stmt->execute($args); $places=$stmt->fetchAll();
$planIds = array_map('intval', $_SESSION['plan'] ?? []);
require __DIR__ . '/includes/header.php';
?>
<section class="hero"><div class="hero-shape shape-one"></div><div class="hero-shape shape-two"></div><div class="hero-content">
  <div class="eyebrow"><span class="eyebrow-dot"></span> YOUR LOCAL GUIDE · CENTRAL SRI LANKA</div>
  <h1>Good days start<br>close to <em>home.</em></h1>
  <p>Find a place that feels like a little escape. Explore the sights, stories and quiet corners around Gampola.</p>
  <a class="button button-light" href="#places">Explore places <span>↓</span></a>
</div>
<div class="hero-art hero-photo" aria-hidden="true">
  <div class="hero-card">
    <span>YOUR STARTING POINT</span>
    <strong>Gampola, Sri Lanka</strong>
    <small>11 local picks · within 25 km</small>
  </div>
</div>
</div>
</section>

<section id="places" class="section explore-section"><div class="section-heading"><div><div class="eyebrow eyebrow-dark">THE LOCAL EDIT</div><h2>Where will today take you?</h2><p class="section-lead">A collection of places for a slower, more memorable day out.</p></div><a class="text-link desktop-link" href="planner.php">Build your day plan <span>→</span></a></div>
<form class="filter-bar" method="get" action="index.php#places"><label class="search-field"><span>⌕</span><input type="search" name="q" value="<?= e($search) ?>" placeholder="Search places, stories, nature…" aria-label="Search attractions"></label>
<label class="select-wrap"><span class="sr-only">Category</span><select name="category"><option value="0">All categories</option><?php foreach(categories() as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $category===(int)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<label class="select-wrap distance-select"><span class="sr-only">Distance from Gampola</span><select name="distance"><option value="25">Within 25 km</option><?php foreach([5,10,15,20] as $d): ?><option value="<?= $d ?>" <?= $maxDistance===$d?'selected':'' ?>>Within <?= $d ?> km</option><?php endforeach; ?></select></label>
<button class="button button-dark" type="submit">Find places</button><?php if($search||$category||$maxDistance!==25): ?><a class="clear-link" href="index.php#places">Clear</a><?php endif; ?></form>
<div class="results-caption"><span><?= count($places) ?> place<?= count($places)===1?'':'s' ?> to explore</span><span>Measured from Gampola · approximate</span></div>
<?php if(!$places): ?><div class="empty-state"><div class="empty-icon">⌕</div><h3>No places found</h3><p>Try another search or clear the filters to see all local picks.</p><a class="button button-outline" href="index.php#places">Show all places</a></div><?php else: ?>
<div class="place-grid"><?php foreach($places as $i=>$p): $hasImage=!empty($p['image_url']); ?><article class="place-card"><a class="place-image <?= $hasImage?'has-image':'image-placeholder' ?>" href="place.php?id=<?= (int)$p['id'] ?>" <?= $hasImage?'style="background-image:url(\''.e($p['image_url']).'\')"':'' ?> aria-label="View <?= e($p['name']) ?>"><span class="image-index"><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></span><span class="image-pin">↗</span><?php if(!$hasImage): ?><span class="placeholder-landscape"><i></i><b></b><em></em></span><?php endif; ?></a><div class="place-card-body"><div class="card-meta"><span class="category-pill"><?= e($p['category']) ?></span><span class="distance">⌖ <?= number_format((float)$p['distance_km'],1) ?> km</span></div><h3><a href="place.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a></h3><p><?= e($p['short_description']) ?></p><div class="card-bottom"><span>◷ <?= (int)$p['visit_minutes'] ?> min</span><form method="post" action="plan_action.php"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="place_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="return" value="index.php"><button class="add-plan" type="submit" <?= in_array((int)$p['id'],$planIds,true)?'disabled':'' ?>><?= in_array((int)$p['id'],$planIds,true)?'✓ Added':'+ Add to plan' ?></button></form></div></div></article><?php endforeach; ?></div><?php endif; ?>
</section><section class="planner-banner"><div class="banner-mark">✳</div><div><span class="eyebrow eyebrow-dark">A LITTLE DAY, YOUR WAY</span><h2>Pick a few places.<br>Make a day of it.</h2></div><a class="button button-dark" href="planner.php">Open my day plan <span>→</span></a><div class="banner-decoration">G</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
