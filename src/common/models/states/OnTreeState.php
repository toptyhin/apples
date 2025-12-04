<?php
declare(strict_types=1);

namespace common\models\states;

use common\models\Apple;
use yii\base\UserException;

class OnTreeState implements AppleStateInterface
{
    private $apple;

    public function __construct(Apple $apple)
    {
        $this->apple = $apple;
    }

    public function fallToGround()
    {
        $this->apple->status = Apple::STATUS_ON_GROUND;
        $this->apple->initializeState();
    }

    public function eat($percent)
    {
        throw new UserException('Съесть нельзя, яблоко на дереве');
    }

    public function checkRotting()
    {
        // На дереве не портится
    }

    public function getStatus()
    {
        return Apple::STATUS_ON_TREE;
    }
}