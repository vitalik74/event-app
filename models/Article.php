<?php

namespace app\models;


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
    const EVENT_CUSTOM_SEND_USERS_OFF = 'off';// switch off event
    const EVENT_CUSTOM_SEND_USERS_OFF2 = 'off2';// switch off event too
    const EVENT_CUSTOM_SEND_USERS_WITH_PARAM = 'sendUsersParam';
    const EVENT_CUSTOM_SEND_USERS_WITH_MODELS = 'sendUsersModels';
    const EVENT_CUSTOM_SEND_USERS_CLOSURE = 'sendUsersClosure';
    const EVENT_CUSTOM_SEND_USERS_CLOSURE2 = 'sendUsersClosure2';


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
        $event = $this->getEvent();

        $event->bind($this);// bind default events
        $event->bind($this, static::EVENT_CUSTOM_SEND_USERS_WITH_PARAM, ['where' => ['user_id' => 1]]);
        $event->bind($this, static::EVENT_CUSTOM_SEND_USERS_WITH_MODELS, ['models' => ['user']]);
        $event->bind($this, static::EVENT_CUSTOM_SEND_USERS_CLOSURE, function () {
            return [
                'test' => 'Test variable',
            ];
        });

        parent::init();
    }

    /**
     * @return \app\components\events\Event
     */
    protected function getEvent()
    {
        /** @var \app\components\events\Event $event */
        return Yii::$app->event;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $event = $this->getEvent();
        $event->bind($this, static::EVENT_CUSTOM_SEND_USERS_CLOSURE2, function () {

        });
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
