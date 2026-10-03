<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$pdo=new PDO(getenv('DB_DSN')?:'',getenv('DB_USERNAME')?:'',getenv('DB_PASSWORD')?:'',[
 PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$pdo->exec("CREATE TABLE IF NOT EXISTS calls(id UUID PRIMARY KEY,room_id VARCHAR(128) NOT NULL,created_by BIGINT NOT NULL,kind VARCHAR(16) NOT NULL,created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),ended_at TIMESTAMPTZ)");
$pdo->exec("CREATE INDEX IF NOT EXISTS idx_calls_room ON calls(room_id,created_at DESC)");
fwrite(STDOUT,"Calls database installed successfully.\n");
