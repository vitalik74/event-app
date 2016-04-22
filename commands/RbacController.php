<?php
namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $users = User::findAll(['status' => User::STATUS_ACTIVE]);

        if (empty($users)) {
            $this->createUser('admin', User::ROLE_ADMINISTRATOR);
            $this->createUser('user', User::ROLE_USER);
        }

        $roles = [];

        foreach ($users as $user) {
            $roles[$user->id] = $auth->getRolesByUser($user->id);
        }

        $auth->removeAll();

        $user = $auth->createRole(User::ROLE_USER);
        $auth->add($user);

        $admin = $auth->createRole(User::ROLE_ADMINISTRATOR);
        $auth->add($admin);
        $auth->addChild($admin, $user);

        foreach ($roles as $userId => $role) {
            foreach ($role as $roleValue) {
                $auth->assign($roleValue, $userId);
            }
        }


        Console::output('Success! RBAC roles has been added.');
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
} 