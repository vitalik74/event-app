<?php

namespace app\controllers;


use app\models\BrowserEvent;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

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

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => BrowserEvent::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}