<?php
use yii\helpers\Html;
use yii\helpers\Url;

$urlFall = Url::to(['fall', 'id' => $apple->id]);
$urlEat = Url::to(['eat', 'id' => $apple->id]);
?>

<div class="col-md-4 mb-4" id="apple-<?= $apple->id ?>">
    <div class="card apple-card">
    
        <div class="card-body">
            <h5 class="card-title" style="color: <?= $apple->color ?>">
                Яблоко #<?= $apple->id ?>
            </h5>
            
            <ul class="list-unstyled">
                <li><strong>Цвет:</strong> <?= $apple->color ?></li>
                <li><strong>Создано:</strong> <?= date('Y-m-d H:i', strtotime($apple->created_at)) ?></li>
                <li class="apple-fallen-at">
                    <?php if ($apple->fallen_at): ?>
                        <strong>Упало:</strong> <?= date('Y-m-d H:i', strtotime($apple->fallen_at)) ?>
                    <?php endif; ?>
                </li>
                <li class="apple-status">
                    <strong>Статус:</strong> 
                    <?php switch ($apple->status):
                        case 0: ?>На дереве<?php break;
                        case 1: ?>На земле<?php break;
                        case 2: ?>Гнилое<?php break;
                    endswitch; ?>
                </li>                
                <li class="apple-eaten-percent">
                    <strong>Съедено:</strong> <?= $apple->eaten_percent ?>%
                </li>
                <li class="apple-size">
                    <strong>Размер:</strong> <?= number_format($apple->size, 2) ?>
                </li>
            </ul>
            
            <div class="apple-progress">
                <?php if ($apple->eaten_percent > 0): ?>
                    <div class="progress">
                        <div class="progress-bar bg-success" 
                             style="width: <?= $apple->eaten_percent ?>%">
                            <?= $apple->eaten_percent ?>%
                        </div>
                    </div>
                <?php endif; ?>
            </div>
                <div class="apple-actions mt-3">
                    <?= $this->render('_apple_actions', ['apple' => $apple]) ?>
                </div>

        </div>
    </div>
</div>