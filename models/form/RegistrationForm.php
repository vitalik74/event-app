<?php

namespace app\models\form;


use app\models\User;
use yii\base\Model;

class RegistrationForm extends Model
{
    public $username;
    public $password;
    public $email;

    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['username', 'password', 'email'], 'string'],
            [['email'], 'email'],
            [['password'], 'string', 'min' => 6, 'max' => 8],
        ];
    }

    public function save()
    {
        if ($this->validate()) {
            $user = new User([
                'email' => $this->email,
                'username' => $this->username,
                'status' => User::STATUS_ACTIVE
            ]);

            $user->setPassword($this->password);
            $user->generateAuthKey();

            return $user->save() ? $user : null;
        }

        return false;
    }
}