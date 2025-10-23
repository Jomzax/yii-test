<?php

namespace app\helpers;

use Yii;

class AlertHelper
{
 

  public static function alert($type = 'success', $message = 'บันทึกข้อมูลสำเร็จ')
  {
    // *[doc] : https://github.com/Dominus77/yii2-sweetalert2-widget

    $options = [
      'title' => '',
    ];

    if (is_array($message)) {
      $options = array_merge($options, $message);
    } else {
      $options['title'] = $message;
    }

    Yii::$app->session->setFlash($type, $options);
  }
}
