<?php
declare(strict_types=1);
namespace PlusChat\\Calls\\Ws;

final class SignalingServer
{
    public function decode(string $frame): ?array
    {
        $len=strlen($frame); if($len<2)return null;
        $second=ord($frame[1]); $masked=($second&128)!==0; $size=$second&127; $offset=2;
        if($size===126){if($len<4)return null;$size=unpack('n',substr($frame,2,2))[1];$offset=4;}
        elseif($size===127){if($len<10)return null;$p=unpack('N2',substr($frame,2,8));$size=$p[1]*4294967296+$p[2];$offset=10;}
        if($masked){if($len<$offset+4)return null;$mask=substr($frame,$offset,4);$offset+=4;}else{$mask='';}
        if($size<0||$len<$offset+$size)return null; $payload=substr($frame,$offset,$size);
        if($masked)for($i=0;$i<$size;$i++)$payload[$i]=$payload[$i]^$mask[$i%4];
        $data=json_decode($payload,true); return is_array($data)?$data:null;
    }

    public function encode(array $data): string
    {
        $payload=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);$len=strlen($payload);
        if($len<126)return chr(129).chr($len).$payload;
        if($len<=65535)return chr(129).chr(126).pack('n',$len).$payload;
        return chr(129).chr(127).pack('N2',intdiv($len,4294967296),$len%4294967296).$payload;
    }
}
