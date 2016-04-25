Yii 2 event-test-app
============================
Времени затратил около 28 часов. Буду рад услышать замечания, ошибки. Писать на tsibikov_vit@mail.ru. Тестировал на php 5.5, mysql 5.6.

Установка
===================
1) `php composer.phar create-project --prefer-dist --stability=dev vitalik74/event-app basic`
2) Прописать БД в конфиге
3) Выполнить `yii migrate --migrationPath=@yii/rbac/migrations` 
4) Выполнить `yii migrate`
5) Выполнить `yii rbac/init`
6) admin 123456 логин/пароль в админку под админом.

Структура
=====================
Основной класс, который отвечает за все события это `app/components/events/Event`. Подключается как компонент к Yii. Имеет ряд настроек. Если какие-то обязательные парамтры конфига отсутствует, то выдаст эксепшен `InvalidConfigException` не верной настройки конфига. Пример конфига:

```
'event' => [
    'class' => 'app\components\events\Event', // сам класс
    'modelsNamespace' => [ // какие модели мы подключаем к системе ивентов
        'app\models',
        //'app\controllers'
    ],
    'eventsNamespace' => 'app\events', // неймспейс классов отправки самих уведомлений
    'findModelsRecursive' => false, // искать ли в подпапках классы
    'startCustomEventName' => 'EVENT_CUSTOM', // начальное название констант, которые означают какое-то событие
    'executeModels' => [ // не учитываем эти модели, поддерживается формат 'model' => ['constant', 'constant']. 
        'app\models\Article' => [
            'EVENT_CUSTOM_SEND_USERS_OFF', 'EVENT_CUSTOM_SEND_USERS_OFF2'
        ],
        'app\models\Event', 'app\models\BrowserEvent',
    ],
    'modelEventClass' => 'app\models\Event',// модель текста событий из БД. Должна реализовывать интерфейс EventModelInterface
]
```
 

Классы отправки самих уведомлений (Email, Browser и др.) находятся `app/events`. Вынес отдельно для меньшей связанности. Наследуются от `BaseSender` и реализуют интерфейс `SenderInterface`.

Модель AR `Event` должна реализовывать интерфейс `EventModelInterface`.
 

Возможности
===================
Существует 4 способа подключения событий (перед этим надо объявить константы события с первоначальным названием как указано в конфиге (по умолчанию: EVENT_CUSTOM)):
```
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

```

1) Самый простой способ подключение на дефолтные события (которые объявленый в `Model`, `Component`, `ActiveRecord`) при этом в отправщик будет передана сама модель, а переменные в тексте заменены на значения.

```
$event->bind($this);// bind default events
```

2) Аналогично пункту выше, но с условием, что событие вызовется при выполнении условия. Например, когда статус нужен определенный. При добавлении события в админке будет предложено выбрать на какие дефолтные события вызывать.
```
$event->bind($this, static::EVENT_CUSTOM_SEND_USERS_WITH_PARAM, ['where' => ['user_id' => 1]]);
```

По сути это заменяет такой код:

```
public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);

    if ($this->type == 1) {
        $this->trigger(...);
    }
    
}
```

3) Когда нужно чтобы в отправляемом сообщении указывались данные из нескольких связанных моделей. Допустим: вышла новая статья и надо указать автора. При выборе такого события в менеджере событий будут автоматом подгружены содели и указаны все возможные атрибуты. 

```
$event->bind($this, static::EVENT_CUSTOM_SEND_USERS_WITH_MODELS, ['models' => ['user']]);
```

4) И последний вариант, когда необходима свобода действий, то используем анонимную функцию. Анонимная функция должна возвращать массив с переменными. Которые экстраполируются в сообщение.

```
$event->bind($this, static::EVENT_CUSTOM_SEND_USERS_CLOSURE, function () {
        return [
            'test' => 'Test variable',
        ];
    });
```