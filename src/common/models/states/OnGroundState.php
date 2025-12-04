<?php
declare(strict_types=1);

namespace common\models\states;

use common\models\Apple;
use yii\base\UserException;

class OnGroundState implements AppleStateInterface
{
    private $apple;
    private const ROTTING_HOURS = 0.05;

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
        $newPercent = $this->apple->eaten_percent + $percent;
        if ($newPercent > 100) {
            $newPercent = 100;
        }
        $this->apple->eaten_percent = $newPercent;
        // Если съели полностью, то состояние не меняем, т.к. яблоко будет удалено
    }

    public function checkRotting()
    {

        
        if ($this->apple->fallen_at) {
            $fallenTimestamp = strtotime($this->apple->fallen_at);
            
            if ($fallenTimestamp !== false && (time() - $fallenTimestamp) > (self::ROTTING_HOURS * 3600)) {
                $this->apple->status = Apple::STATUS_ROTTEN;
                $this->apple->setState(new RottenState($this->apple));
            }
        }        

    }

    public function getStatus()
    {
        return Apple::STATUS_ON_GROUND;
    }
}