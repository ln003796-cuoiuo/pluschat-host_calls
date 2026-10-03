<?php
namespace PlusChat\Calls\Ws;
use Ratchet\MessageComponentInterface; use Ratchet\ConnectionInterface;
final class SignalingServer implements MessageComponentInterface { private array $rooms=[]; public function onOpen(ConnectionInterface $c):void{} public function onClose(ConnectionInterface $c):void{foreach($this->rooms as $id=>$clients){unset($this->rooms[$id][$c->resourceId]);}} public function onError(ConnectionInterface $c,\Exception $e):void{$c->close();} public function onMessage(ConnectionInterface $from,$msg):void{$m=json_decode((string)$msg,true);$id=$m['call_id']??null;if(!$id)return;if(($m['type']??'')==='join'){$this->rooms[$id][$from->resourceId]=$from;return;}foreach($this->rooms[$id]??[] as $rid=>$client)if($rid!==$from->resourceId)$client->send($msg);} }
