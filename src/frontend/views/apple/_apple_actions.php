<?php
use yii\helpers\Html;

if ($apple->isOnTree()): ?>
    <button class="btn btn-warning btn-sm apple-fall-btn" data-id="<?= $apple->id ?>">
        Уронить
    </button>
<?php elseif ($apple->isOnGround()): ?>
    <?= Html::beginForm(['eat', 'id' => $apple->id], 'post', [
        'class' => 'form-inline apple-eat-form',
        'data-id' => $apple->id
    ]) ?>
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input type="number" name="percent" 
               min="1" max="<?= $apple->getRemainingPercent() ?>" 
               value="25" class="form-control form-control-sm mr-2" 
               style="width: 80px;">
        <button type="submit" class="btn btn-success btn-sm">
            🍽️ Съесть
        </button>
    <?= Html::endForm() ?>
<?php elseif ($apple->isRotten()): ?>
    <span class="text-muted">Гнилое яблоко</span>
<?php endif; ?>

