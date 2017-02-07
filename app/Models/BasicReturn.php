<?php

namespace App\Models;

class BasicReturn {


  public function toJson($status, $message, $data='')
  {
    $data = array('status' => $status, 'message' => $message, 'data'=>$data);
    return json_encode($data, JSON_UNESCAPED_UNICODE);
  }

}
