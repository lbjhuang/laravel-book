<?php

namespace App\Tool\SMS;

use App\Models\BasicReturn;

class SendTemplateSMS
{
  //主帐号
  private $accountSid='8a216da8594f29f8015954cfd076022e';

  //主帐号Token
  private $accountToken='27bd006bb95a4235973811f8a6cc99eb';

  //应用Id
  private $appId='8a216da8594f29f8015954cfd0c50233';

  //请求地址，格式如下，不需要写https://
  private $serverIP='sandboxapp.cloopen.com';

  //请求端口
  private $serverPort='8883';

  //REST版本号
  private $softVersion='2013-12-26';

  /**
    * 发送模板短信
    * @param to 手机号码集合,用英文逗号分开
    * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    * @param $tempId 模板Id
    */
  public function sendTemplateSMS($to, $datas, $tempId)
  {
       $basicReturn = new BasicReturn();

       // 初始化REST SDK
       $rest = new CCPRestSDK($this->serverIP,$this->serverPort,$this->softVersion);
       $rest->setAccount($this->accountSid,$this->accountToken);
       $rest->setAppId($this->appId);

       // 发送模板短信
      //  echo "Sending TemplateSMS to $to <br/>";
       $result = $rest->sendTemplateSMS($to, $datas, $tempId);
       if($result == NULL ) {
          $basicReturn->toJson(3, 'result error!');
       }
       if($result->statusCode != 0) {
          $basicReturn->toJson($result->statusCode, $result->statusMsg);
       }else{
          $basicReturn->toJson(0, '发送成功');
       }

       return $basicReturn;
  }
}

//sendTemplateSMS("18576437523", array(1234, 5), 1);
