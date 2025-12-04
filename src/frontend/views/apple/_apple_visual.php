<?php
use yii\helpers\Html;

$statusClass = '';
$statusIcon = '';
if ($apple->isOnTree()) {
    $statusClass = 'on-tree';
    $statusIcon = '🌳';
} elseif ($apple->isRotten()) {
    $statusClass = 'rotten';
    $statusIcon = '🤢';
} else {
    $statusClass = 'on-ground';
    $statusIcon = '🍂';
}

$colorClass = 'apple-' . $apple->color;
$eatenClass = $apple->eaten_percent > 0 ? 'partially-eaten' : '';
$size = max(40, min(80, $apple->size * 50)); // Размер яблока в пикселях
?>

<div class="apple-visual <?= $statusClass ?> <?= $colorClass ?> <?= $eatenClass ?> <?= $apple->isRotten() ? 'rotten' : '' ?>" 
     data-id="<?= $apple->id ?>"
     data-status="<?= $apple->status ?>"
     data-color="<?= $apple->color ?>"
     data-eaten-percent="<?= $apple->eaten_percent ?>"
     data-size="<?= $apple->size ?>"
     data-remaining-percent="<?= $apple->getRemainingPercent() ?>"
     data-created-at="<?= date('Y-m-d H:i', strtotime($apple->created_at)) ?>"
     data-fallen-at="<?= $apple->fallen_at ? date('Y-m-d H:i', strtotime($apple->fallen_at)) : '' ?>"
     style="--apple-size: <?= $size ?>px;"
     title="Яблоко #<?= $apple->id ?> (<?= $apple->color ?>)">
    <div class="apple-body">
        <div class="apple-highlight"></div>
        <?php if ($apple->isOnTree()): ?>
            <div class="apple-stem"></div>
            <div class="apple-leaf"></div>
        <?php endif; ?>
    </div>
    <?php if ($apple->eaten_percent > 0): ?>
        <div class="apple-eaten-indicator" style="width: <?= $apple->eaten_percent ?>%"></div>
        <div class="apple-eaten-percent-text"><?= $apple->eaten_percent ?>%</div>
    <?php endif; ?>
</div>
