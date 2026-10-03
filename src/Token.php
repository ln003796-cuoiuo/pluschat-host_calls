<?php
namespace PlusChat\Calls;
final class Token { public static function verify(string $token): bool { $secret=getenv('CALL_SHARED_SECRET')?:''; return $secret!=='' && hash_equals(hash_hmac('sha256','pluschat-calls',$secret),$token); } }
