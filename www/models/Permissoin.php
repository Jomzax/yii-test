<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "permissoin".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 * @property mixed $abbr
 */
class Permissoin extends \yii\mongodb\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'permissoin';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'menu_id',
            'name',
            'abbr',
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'menu_id','value', 'abbr'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'menu_id' => 'Menu ID',
            'name' => 'Name',
            'abbr' => 'Abbr',
        ];
    }
}
