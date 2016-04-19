<?php

namespace app\models;

use app\components\events\EventModelInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

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
 * @property string $default_event
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
            [['event'], 'string', 'max' => 100],
            [['type', 'default_event'], 'each', 'rule' => ['string']],
            [['title'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->type = Json::encode($this->type);
            $this->default_event = Json::encode($this->default_event);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->type = Json::decode($this->type);
        $this->default_event = Json::decode($this->default_event);
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
            'defaultEvent' => 'Default Event'
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

    /**
     * @return string
     */
    public function getDefaultEventField()
    {
        return 'default_event';
    }
}
