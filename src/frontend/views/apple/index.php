<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Яблочный сад';
$this->registerJsFile('https://code.jquery.com/jquery-3.6.0.min.js');
$this->registerCss(<<<CSS
.apple-card { transition: all 0.3s; }
.apple-card.rotten { opacity: 0.7; background-color: #f8f9fa; }
.apple-card.eaten { opacity: 0.5; }
.progress { height: 10px; margin-top: 10px; }
.message-alert { position: fixed; top: 20px; right: 20px; z-index: 1000; }
CSS
);
?>

<div class="apple-index">
    <div id="message-container" class="message-alert"></div>
    
    <h1>Яблочный сад</h1>
    
    <div class="mb-4">
        <button id="generate-btn" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm d-none" id="generate-spinner"></span>
            Сгенерировать яблоки
        </button>
        
        <button id="check-rotting-btn" class="btn btn-secondary">Проверить гниение</button>
        
        <div class="mt-2 small text-muted">
            <span id="apple-count"><?= count($apples) ?></span> яблок в саду
        </div>
    </div>
    
    <div id="apples-container" class="row">
        <?php if (empty($apples)): ?>
            <div class="col-12">
                <div class="alert alert-info">Яблок пока нет. Сгенерируйте их!</div>
            </div>
        <?php else: ?>
            <?php foreach ($apples as $apple): ?>
                <?= $this->render('_apple_item', ['apple' => $apple]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
    $urlGenerate = Url::to(['create']);
    $urlFall = Url::to(['fall', 'id' => '{id}']);
    $urlCheckRotting = Url::to(['check-rotting']);
?>

<?php $this->registerJs(<<<JS
// Глобальные функции для работы с сообщениями
function showMessage(type, text) {
    const message = '<div class="alert alert-'+type+' alert-dismissible fade show">'+text+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
    $('#message-container').html(message);
    setTimeout(() => $('.alert').alert('close'), 5000);
}

// Генерация яблок
$('#generate-btn').click(function() {
    const btn = $(this);
    btn.prop('disabled', true);
    $('#generate-spinner').removeClass('d-none');
    
    $.ajax({
        url: '{$urlGenerate}',
        type: 'POST',
        data: {
            '_csrf': yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                if ($('#apples-container .alert-info').length) {
                    $('#apples-container').html(response.html);
                } else {
                    $('#apples-container').append(response.html);
                }
                $('#apple-count').text(parseInt($('#apple-count').text()) + response.count);
                showMessage('success', response.message);
            } else {
                showMessage('danger', response.message);
            }
        },
        error: function() {
            showMessage('danger', 'Ошибка при генерации яблок');
        },
        complete: function() {
            btn.prop('disabled', false);
            $('#generate-spinner').addClass('d-none');
        }
    });
});


JS
); ?>