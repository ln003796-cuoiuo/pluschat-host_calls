<?php
namespace PlusChat\Calls;
final class Db { public static function pdo(): \PDO { static $pdo; if($pdo)return $pdo; $pdo=new \PDO(getenv('CALL_DB_DSN'),getenv('CALL_DB_USER'),getenv('CALL_DB_PASSWORD'),[\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION]); return $pdo; } }
