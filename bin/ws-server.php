<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';

use PlusChat\Calls\Ws\SignalingServer;

$port=(int)(getenv('CALL_WS_PORT')?:8080);
$server=stream_socket_server('tcp://0.0.0.0:'.$port,$errno,$error);
if(!$server) throw new RuntimeException('WebSocket server failed');
stream_set_blocking($server,false);
$clients=[];
$rooms=[];
$signaling=new SignalingServer();

while(true){
    $read=[$server];
    foreach($clients as $socket)$read[]=$socket;
    $write=null;$except=null;
    if(@stream_select($read,$write,$except,1)===false)continue;
    foreach($read as $socket){
        if($socket===$server){
            $client=@stream_socket_accept($server,0);
            if($client){stream_set_blocking($client,false);$clients[(int)$client]=$client;}
            continue;
        }
        $id=(int)$socket;
        $data=@fread($socket,65535);
        if($data===false||$data===''){fclose($socket);unset($clients[$id]);continue;}
        if(str_contains($data,'Sec-WebSocket-Key:')){
            if(preg_match('/Sec-WebSocket-Key:\s*(.+)\r/i',$data,$m)){
                $accept=base64_encode(sha1(trim($m[1]).'258EAFA5-E914-47DA-95CA-C5AB0DC85B11',true));
                fwrite($socket,"HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: ".$accept."\r\n\r\n");
            }
            continue;
        }
        $payload=$signaling->decode($data);
        if($payload===null)continue;
        $room=(string)($payload['room']??'');
        if($room==='')continue;
        $rooms[$room]??=[];
        $rooms[$room][$id]=$socket;
        $message=$signaling->encode($payload);
        foreach($rooms[$room] as $cid=>$peer){
            if($cid!==$id)@fwrite($peer,$message);
        }
    }
}
