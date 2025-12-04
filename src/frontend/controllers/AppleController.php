<?php
declare(strict_types=1);

namespace frontend\controllers;

use common\models\Apple;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use Yii;


class AppleController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], 
                    ],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        // // Проверяем гниение для всех яблок
        $apples = Apple::find()->where(['user_id' => $userId])->all();
        foreach ($apples as $apple) {
            $apple->checkRotting();
        }
        return $this->render('index', [
            'apples' => Apple::find()->where(['user_id' => $userId])->all(),
        ]);
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;
        $apple = new Apple();
        $apple->user_id = $userId;
        $apple->save();

        return [
            'success' => true,
            'count' => 1,
            'message' => 'Сгенерировано яблоко',
            'html' => $this->renderPartial('_apple_visual', [
                'apple' => $apple
            ])
        ];
    }

    public function actionFall($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::warning("TEST LOG");
        $apple = Apple::findOne($id);
        
        if (!$apple) {
            return ['success' => false, 'message' => 'Яблоко не найдено'];
        }
        
        try {
            $apple->fallToGround();
            return [
                'success' => true,
                'message' => 'Яблоко упало!',
                'apple_id' => $apple->id,
                'status' => $apple->status,
                'fallen_at' => $apple->fallen_at,
                'html' => $this->renderPartial('_apple_actions', ['apple' => $apple])
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function actionEat($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $apple = Apple::findOne($id);
        $percent = Yii::$app->request->post('percent', 25);
        
        if (!$apple) {
            return ['success' => false, 'message' => 'Яблоко не найдено'];
        }
        
        try {
            $appleId = $apple->id;
            $beforePercent = $apple->eaten_percent;
            
            // Вызываем eat() - он сам удалит яблоко, если оно будет полностью съедено
            $result = $apple->eat($percent);
            
            if (!$result) {
                return ['success' => false, 'message' => 'Не удалось съесть яблоко'];
            }
            
            // Проверяем, удалено ли яблоко (пытаемся найти его в БД)
            $appleAfter = Apple::findOne($appleId);
            
            if (!$appleAfter) {
                // Яблоко было удалено из БД
                return [
                    'success' => true,
                    'message' => 'Яблоко полностью съедено и удалено!',
                    'removed' => true,
                    'apple_id' => $appleId
                ];
            }
            
            // Яблоко осталось, обновляем данные
            $response = [
                'success' => true,
                'apple_id' => $appleAfter->id,
                'eaten_percent' => $appleAfter->eaten_percent,
                'size' => $appleAfter->size,
                'remaining_percent' => $appleAfter->getRemainingPercent(),
                'removed' => false
            ];
            
            $response['message'] = "Откушено {$percent}% яблока. Осталось: {$response['remaining_percent']}%";
            $response['html'] = $this->renderPartial('_apple_actions', ['apple' => $appleAfter]);
            
            return $response;
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function actionGetApple($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $apple = Apple::findOne($id);
        
        if (!$apple) {
            return ['success' => false, 'message' => 'Яблоко не найдено'];
        }
        
        // Проверяем, что яблоко принадлежит текущему пользователю
        if ($apple->user_id != Yii::$app->user->id) {
            return ['success' => false, 'message' => 'Доступ запрещен'];
        }
        
        return [
            'success' => true,
            'apple' => [
                'id' => $apple->id,
                'color' => $apple->color,
                'status' => $apple->status,
                'created_at' => date('Y-m-d H:i', strtotime($apple->created_at)),
                'fallen_at' => $apple->fallen_at ? date('Y-m-d H:i', strtotime($apple->fallen_at)) : null,
                'eaten_percent' => $apple->eaten_percent,
                'size' => $apple->size,
                'remaining_percent' => $apple->getRemainingPercent(),
            ]
        ];
    }
}