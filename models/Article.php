<?php

namespace app\models;

use app\components\events\EventFactory;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property string $short_text
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $user_id
 *
 * @property User $user
 */
class Article extends ActiveRecord
{
    const EVENT_CUSTOM_SEND_USERS = 'sendUsers';
    const EVENT_CUSTOM_SEND_USERS_OFF = 'sendUsersOff';
    const EVENT_CUSTOM_SEND_USERS_OFF2 = 'sendUsersOff2';
    const EVENT_CUSTOM_SEND_USERS_OFF3 = 'sendUsersOff3';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        /** @var EventFactory $event */
        $event = Yii::$app->event;

        $event->bindDefaultEvents();
        //$event->bind($this);// bind default events
        $event->bind($this, static::EVENT_CUSTOM_SEND_USERS_OFF);
        $event->unbind($this, static::EVENT_CUSTOM_SEND_USERS_OFF3);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'text', 'created_at', 'updated_at', 'user_id'], 'required'],
            [['text', 'short_text'], 'string'],
            [['created_at', 'updated_at', 'user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'text' => 'Text',
            'short_text' => 'Short Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
