<?php

namespace app\models;

use app\components\events\EventModelInterface;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property string $name
 * @property string $event
 * @property integer $user_id
 * @property string $title
 * @property string $text
 * @property string $type
 *
 * @property User $user
 */
class Event extends ActiveRecord implements EventModelInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'event', 'title', 'type'], 'required'],
            [['user_id'], 'integer'],
            [['text'], 'string'],
            [['name'], 'string', 'max' => 60],
            [['event', 'type'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'event' => 'Event',
            'user_id' => 'User ID',
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getTypeField()
    {
        return 'type';
    }

    /**
     * @return string
     */
    public function getEventField()
    {
        return 'event';
    }

    /**
     * @return string
     */
    public function getUserIdField()
    {
        return 'user_id';
    }

    /**
     * @return string
     */
    public function getTitleField()
    {
        return 'title';
    }

    /**
     * @return string
     */
    public function getTextField()
    {
        return 'text';
    }
}
