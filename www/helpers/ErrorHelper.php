<?php

namespace app\helpers;

class ErrorHelper
{
  public static function getErrorsValueArray($errors = [[]])
  {
    return array_merge(...array_values($errors ?? []));
  }
}
