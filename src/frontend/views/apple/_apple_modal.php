<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<!-- Модальное окно для управления яблоком -->
<div class="modal fade" id="appleModal" tabindex="-1" aria-labelledby="appleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appleModalLabel">Яблоко #<span id="modal-apple-id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-apple-body">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Загрузка...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

