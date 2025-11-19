<?php

namespace app\models;

use yii\base\Model;


class SignupForm extends Model
{
    public $username;
    public $password;
    public $roles;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 50],
            ['password', 'string', 'min' => 6],
        ];
    }
}
