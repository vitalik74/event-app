<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event_field".
 *
 * @property integer $id
 * @property integer $type
 * @property string $value
 * @property integer $event_id
 *
 * @property Event $event
 */
class EventField extends ActiveRecord
{
    const TYPE_TYPE = 10;
    const TYPE_DEFAULT_EVENT = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'value', 'event_id'], 'required'],
            [['type', 'event_id'], 'integer'],
            [['value'], 'string', 'max' => 100],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'value' => 'Value',
            'event_id' => 'Event ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['id' => 'event_id']);
    }
}
