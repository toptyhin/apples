<?php
declare(strict_types=1);

namespace common\models\states;

use common\models\Apple;
use yii\base\UserException;

class RottenState implements AppleStateInterface
{
    private $apple;

    public function __construct(Apple $apple)
    {
        $this->apple = $apple;
    }

    public function fallToGround()
    {
        throw new UserException('Яблоко уже на земле и сгнило');
    }

    public function eat($percent)
    {
        throw new UserException('Съесть нельзя, яблоко гнилое');
    }

    public function checkRotting()
    {
        // Уже гнилое
    }

    public function getStatus()
    {
        return Apple::STATUS_ROTTEN;
    }
}