<?php

use app\models\User;
use yii\db\Migration;

/**
 * Handles adding data to table `user`.
 */
class m160409_080133_add_data_to_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if (YII_ENV !== 'test') {
            $this->createUser('admin', User::ROLE_ADMINISTRATOR);
            $this->createUser('user', User::ROLE_USER);
        }
    }

    protected function createUser($username, $typeRole)
    {
        $user = new User([
            'username' => $username,
            'status' => User::STATUS_ACTIVE,
            'email' => $username . '@' . Yii::$app->params['domain']
        ]);

        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save(false);

        $this->assign($user, $typeRole);
    }

    protected function assign(User $user, $typeRole)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($typeRole);

        if ($role !== null) {
            // удаляем какие были роли
            $auth->revokeAll($user->id);
            $auth->assign($role, $user->id);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable('{{%user}}');
    }
}
