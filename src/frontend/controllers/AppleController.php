<?php
declare(strict_types=1);

namespace frontend\controllers;

use common\models\Apple;
use yii\web\Controller;
use yii\web\Response;
use Yii;


class AppleController extends Controller
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        // // Проверяем гниение для всех яблок
        $apples = Apple::find()->where(['user_id' => $userId])->all();
        foreach ($apples as $apple) {
            // $apple->checkRotting();
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
        // var_dump($apple);
        $apple->save();

        return [
            'success' => true,
            'count' => 1,
            'message' => 'Сгенерировано яблоко',
            'html' => $this->renderPartial('_apple_item', [
                'apple' => $apple
            ])
        ];
    }

    public function actionFall($id)
    {
        // $this->appleService->fall($id);
        // Yii::$app->session->setFlash('success', 'Яблоко упало!');
        return $this->redirect('index');
    }

    public function actionEat()
    {
        // $id = Yii::$app->request->post('id');
        // $percent = (int)Yii::$app->request->post('percent');

        // if (!$id || !$percent) {
        //     Yii::$app->session->setFlash('error', 'Неверные данные.');
        //     return $this->redirect('index');
        // }

        // try {
        //     $apple = $this->appleService->eat($id, $percent);
        //     if ($apple) {
        //         Yii::$app->session->setFlash('success', "Откушено {$percent}%. Размер: {$apple->getSize()}");
        //     } else {
        //         Yii::$app->session->setFlash('success', 'Яблоко полностью съедено!');
        //     }
        // } catch (\Exception $e) {
        //     Yii::$app->session->setFlash('error', $e->getMessage());
        // }

        return $this->redirect('index');
    }
}