<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
use PlusChat\Calls\Ws\SignalingServer;use PlusChat\Calls\Token;
$port=(int)(getenv('CALL_WS_PORT')?:8080);$server=stream_socket_server('tcp://0.0.0.0:'.$port,$errno,$err);if(!$server)throw new RuntimeException('websocket_bind_failed');stream_set_blocking($server,false);
$clients=[];$rooms=[];$users=[];$signaling=new SignalingServer();
function frame(array $d):string{return (new SignalingServer())->encode($d);}
while(true){
 $read=[$server];foreach($clients as $c)$read[]=$c;$w=null;$e=null;if(@stream_select($read,$w,$e,1)===false)continue;
 foreach($read as $sock){
  $id=(int)$sock;
  if($sock===$server){$c=@stream_socket_accept($server,0);if($c){stream_set_blocking($c,false);$clients[(int)$c]=$c;}continue;}
  $raw=@fread($sock,65535);if($raw===false||$raw===''){foreach($rooms as $room=>$members)if(isset($members[$id]))unset($rooms[$room][$id]);unset($clients[$id],$users[$id]);@fclose($sock);continue;}
  if(str_contains($raw,'Sec-WebSocket-Key:')){if(preg_match('/Sec-WebSocket-Key:\s*(.+)\r/i',$raw,$m)){$accept=base64_encode(sha1(trim($m[1]).'258EAFA5-E914-47DA-95CA-C5AB0DC85B11',true));fwrite($sock,"HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: {$accept}\r\n\r\n");}continue;}
  $msg=$signaling->decode($raw);if(!$msg)continue;
  if(!isset($users[$id])){$auth=Token::verify((string)($msg['token']??''));if(!$auth){@fwrite($sock,frame(['type'=>'error','code'=>'UNAUTHORIZED']));@fclose($sock);unset($clients[$id]);continue;}$users[$id]=$auth;}
  $room=(string)($msg['call_id']??'');if($room===''||$room!==($users[$id]['call_id']??''))continue;
  if(($msg['type']??'')==='leave'){foreach($rooms[$room]??[] as $peer=>$ps)if($peer!==$id)@fwrite($ps,frame(['type'=>'peer_left','user_id'=>$users[$id]['sub']]));unset($rooms[$room][$id]);continue;}
  $rooms[$room]??=[];$rooms[$room][$id]=$sock;
  $out=$msg;unset($out['token']);$out['from_user_id']=$users[$id]['sub'];
  foreach($rooms[$room] as $peer=>$ps)if($peer!==$id)@fwrite($ps,frame($out));
 }
}
