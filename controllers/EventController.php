<?php

namespace app\controllers;

use app\models\EventField;
use app\models\User;
use Yii;
use app\models\Event;
use app\models\search\EventSearch;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends BaseController
{
    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Event();
        $modelEventFields = new EventField();

        if ($model->load(Yii::$app->request->post()) && $modelEventFields->load(\Yii::$app->request->post()) && $model->validate() && $modelEventFields->validate()) {
            if ($model->save(false) && $modelEventFields->save(false)) {
                $model->link('eventFieldRelation', $modelEventFields);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'users' => $this->getUsers(),
            'typeEvents' => $this->getTypeEvents(),
            'events' => $this->getEvents(),
            'defaultEvent' => $this->getDefaultEvent()
        ]);
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'users' => $this->getUsers(),
                'typeEvents' => $this->getTypeEvents(),
                'events' => $this->getEvents(),
                'defaultEvent' => $this->getDefaultEvent()
            ]);
        }
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return array
     */
    protected function getUsers()
    {
        return ArrayHelper::map(User::find()->all(), 'id', 'username');
    }

    /**
     * @return array
     */
    protected function getTypeEvents()
    {
        return $this->getEvent()->getTypeEvents();
    }

    /**
     * @return \app\components\events\Event
     */
    protected function getEvent()
    {
        return Yii::$app->event;
    }

    /**
     * @return array
     */
    protected function getEvents()
    {
        return $this->getEvent()->getEventsFromModels();
    }

    /**
     * @return mixed
     */
    protected function getDefaultEvent()
    {
        return $this->getEvent()->getDefaultEvents('yii\db\ActiveRecord');
    }

    public function actionGetFields($event)
    {
        $fields = $this->getEvent()->getFields($event);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $fields,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $this->renderPartial('fields', ['dataProvider' => $dataProvider]);
    }
}
