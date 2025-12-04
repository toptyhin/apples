<?php
declare(strict_types=1);

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

use common\models\states\AppleStateInterface;
use common\models\states\OnTreeState;
use common\models\states\OnGroundState;
use common\models\states\RottenState;


/** Модель Apple
 *
 * @property int $id
 * @property int $user_id
 * @property string $color
 * @property string $created_at
 * @property string|null $fallen_at
 * @property int $status
 * @property float $eaten_percent
 * @property float $size
 */

class Apple extends ActiveRecord
{
    const STATUS_ON_TREE = 0;
    const STATUS_ON_GROUND = 1;
    const STATUS_ROTTEN = 2;

    const COLORS = ['red', 'green', 'yellow'];

    private AppleStateInterface $state;

    public static function tableName(): string
    {
        return '{{%apples}}';
    }

    public function rules():array
    {
        return [
            [['user_id', 'color', 'created_at', 'status'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['eaten_percent', 'size'], 'number'],
            [['created_at', 'fallen_at'], 'safe'],
            [['color'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function init()
    {
        parent::init();
        
        if ($this->isNewRecord) {
            $this->color = self::COLORS[array_rand(self::COLORS)];
            $this->created_at = date('Y-m-d H:i:s', time() - rand(0, 86400 * 30));

            $this->status = self::STATUS_ON_TREE;
            $this->eaten_percent = 0.0;
            $this->size = 1.0;
        }
        
        $this->initializeState();
    }


    private function initializeState()
    {
        switch ($this->status) {
            case self::STATUS_ON_TREE:
                $this->state = new OnTreeState($this);
                break;
            case self::STATUS_ON_GROUND:
                $this->state = new OnGroundState($this);
                break;
            case self::STATUS_ROTTEN:
                $this->state = new RottenState($this);
                break;
            default:
                $this->state = new OnTreeState($this);
        }
    }

    public function setState(AppleStateInterface $state)
    {
        $this->state = $state;
        $this->status = $state->getStatus();
    }

    public function getState(): AppleStateInterface
    {
        return $this->state;
    }

    public function fallToGround()
    {
        $this->state->fallToGround();
        $this->fallen_at = date('Y-m-d H:i:s', time());

        if (!$this->save()) {
            Yii::error('Ошибки валидации при падении яблока: ' . print_r($this->errors, true), __METHOD__);
            return false;
        }
        return true;
    }

    public function eat($percent)
    {
        $this->state->eat($percent);
        
        // Если яблоко полностью съедено, удаляем его из БД
        if ($this->isEaten()) {
            if ($this->delete()) {
                return true;
            }
            Yii::error('Не удалось удалить полностью съеденное яблоко', __METHOD__);
            return false;
        }
        
        // Иначе сохраняем изменения
        if (!$this->save()) {
            Yii::error('Ошибки валидации при поедании яблока: ' . print_r($this->errors, true), __METHOD__);
            return false;
        }
        return true;
    }

    public function checkRotting()
    {
        $this->state->checkRotting();
        if (!$this->save()) {
            Yii::error('Ошибки валидации при проверке гниения: ' . print_r($this->errors, true), __METHOD__);
            return false;
        }
        return true;
    }

    public function getRemainingPercent()
    {
        return 100 - $this->eaten_percent;
    }

    public function isEaten()
    {
        return $this->eaten_percent >= 100;
    }

    public function isOnTree()
    {
        return $this->status === self::STATUS_ON_TREE;
    }

    public function isOnGround()
    {
        return $this->status === self::STATUS_ON_GROUND;
    }

    public function isRotten()
    {
        return $this->status === self::STATUS_ROTTEN;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isEaten()) {
                return false; // Не сохраняем, если съедено
            }
            return true;
        }
        return false;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->initializeState();
    }    

}