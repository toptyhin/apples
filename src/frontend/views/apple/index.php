<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Яблочный сад';
$this->registerCssFile('@web/css/apple-tree.css');

// Разделяем яблоки по статусам
$applesOnTree = [];
$applesOnGround = [];

foreach ($apples as $apple) {
    if ($apple->isOnTree()) {
        $applesOnTree[] = $apple;
    } else {
        $applesOnGround[] = $apple;
    }
}
?>

<div class="apple-index">
    <div id="message-container" class="message-alert"></div>
    
    <div class="apple-header mb-4">
        <h1>Яблочный сад</h1>
        
        <div class="apple-controls">
            <button id="generate-btn" class="btn btn-primary">
                <span class="spinner-border spinner-border-sm d-none" id="generate-spinner"></span>
                Сгенерировать яблоко
            </button>
            
            <button id="check-rotting-btn" class="btn btn-secondary">Проверить гниение</button>
            
            <div class="apple-count-info">
                <span id="apple-count"><?= count($apples) ?></span> яблок в саду
            </div>
        </div>
    </div>
    
    <div class="apple-tree-container">
        <!-- Зона дерева -->
        <div class="tree-zone" id="tree-zone">
           
            <div class="apples-on-tree" id="apples-on-tree">
                <?php if (empty($applesOnTree)): ?>
                    <div class="no-apples">Нет яблок на дереве</div>
                <?php else: ?>
                    <?php foreach ($applesOnTree as $apple): ?>
                        <?= $this->render('_apple_visual', ['apple' => $apple]) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Зона земли -->
        <div class="ground-zone" id="ground-zone">
            <div class="ground-visual"></div>
            
            <div class="apples-on-ground" id="apples-on-ground">
                <?php if (empty($applesOnGround)): ?>
                    <div class="no-apples">Нет яблок на земле</div>
                <?php else: ?>
                    <?php foreach ($applesOnGround as $apple): ?>
                        <?= $this->render('_apple_visual', ['apple' => $apple]) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для управления яблоком -->
<?= $this->render('_apple_modal') ?>

<?php
    $urlGenerate = Url::to(['create']);
    $urlFall = Url::to(['fall', 'id' => '{id}']);
    $urlEat = Url::to(['eat', 'id' => '{id}']);
?>

<?php $this->registerJsFile('@web/js/apples.js', ['depends' => [\yii\web\JqueryAsset::class]]); ?>
<?php $this->registerJs(<<<JS
// Глобальные функции для работы с сообщениями
function showMessage(type, text) {
    const message = '<div class="alert alert-'+type+' alert-dismissible fade show">'+text+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
    $('#message-container').html(message);
    setTimeout(() => $('.alert').alert('close'), 5000);
}

// Глобальные переменные для URL
window.appleUrls = {
    generate: '{$urlGenerate}',
    fall: '{$urlFall}',
    eat: '{$urlEat}'
};
JS
); ?>
