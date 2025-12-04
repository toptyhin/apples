<?php
declare(strict_types=1);

namespace common\models\states;

interface AppleStateInterface
{
    public function fallToGround();
    public function eat(int $percent);
    public function checkRotting();
    public function getStatus();
}