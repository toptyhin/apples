<?php
declare(strict_types=1);

namespace common\models\states;

use common\models\Apple;
use yii\base\UserException;

class OnGroundState implements AppleStateInterface
{
    private $apple;

    public function __construct(Apple $apple)
    {
        $this->apple = $apple;
        $this->checkRotting();
    }

    public function fallToGround()
    {
        throw new UserException('Яблоко уже на земле');
    }

    public function eat($percent)
    {
        //TODO: реализовать 
    }

    public function checkRotting()
    {
        //TODO: реализовать 
    }

    public function getStatus()
    {
        return Apple::STATUS_ON_GROUND;
    }
}