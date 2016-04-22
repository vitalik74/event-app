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

        if (!empty($roles)) {
            foreach ($roles as $userId => $role) {
                foreach ($role as $roleValue) {
                    $auth->assign($roleValue, $userId);
                }
            }
        } else {
            $auth->assign($admin, 1);
            $auth->assign($user, 2);
        }

        Console::output('Success! RBAC roles has been added.');
    }
} 