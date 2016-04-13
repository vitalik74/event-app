<?php

namespace app\controllers;


use app\models\form\RegistrationForm;
use yii\web\Controller;

class UserController extends Controller
{
    public function actionRegistration()
    {
        $model = new RegistrationForm();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view']);
        }

        return $this->render('registration', ['model' => $model]);
    }


    public function actionView()
    {
        return $this->render('view');
    }
}