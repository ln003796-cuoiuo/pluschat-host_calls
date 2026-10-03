<?php
declare(strict_types=1);
namespace PlusChat\Calls;
final class Token{
 public static function verify(string $token):?array{
  $secret=(string)(getenv('CALL_SIGNING_SECRET')??'');$parts=explode('.',$token);if(count($parts)!==3||strlen($secret)<32)return null;
  [$h,$p,$s]=$parts;$calc=rtrim(strtr(base64_encode(hash_hmac('sha256',$h.'.'.$p,$secret,true)),'+/','-_'),'=');if(!hash_equals($calc,$s))return null;
  $data=json_decode(base64_decode(strtr($p,'-_','+/').'==='),true);if(!is_array($data)||($data['exp']??0)<time())return null;return $data;
 }
}
