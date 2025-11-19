<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "menu_permission".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 * @property mixed $abbr
 */
class MenuPermission extends \yii\mongodb\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'menu_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'menuroles_id',
            'name',
            'abbr',
            'role_id',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'menuroles_id',
                'name',
                'abbr',
                'role_id',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'menuroles_id' => 'Menu Roles ID',
            'name' => 'Name',
            'abbr' => 'Abbr',
            'role_id' => 'Roles ID',
        ];
    }
}
