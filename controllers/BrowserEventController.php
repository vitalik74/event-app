<?php

namespace app\controllers;


use app\models\BrowserEvent;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\Response;

class BrowserEventController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), ['access' => [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [User::ROLE_USER],
                ],
            ],
        ]]);

        return $behaviors;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => BrowserEvent::find(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionSetRead($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $model->viewed = true;

        if ($model->save()) {
            return [
                'success' => true
            ];
        }

        return [
            'success' => false
        ];
    }

    /**
     * @param $id
     * @return BrowserEvent
     * @throws HttpException
     */
    protected function findModel($id)
    {
        $model = BrowserEvent::findOne(['id' => $id]);

        if ($model == null) {
            throw new HttpException(404);
        }

        return $model;
    }
}