<?php
namespace PlusChat\Calls;
final class Http { public function handle(): void { $path=parse_url($_SERVER['REQUEST_URI']??'/','path'); if($path==='/health'){echo json_encode(['ok'=>true,'service'=>'pluschat-calls']);return;} if($path==='/api/calls'&&($_SERVER['REQUEST_METHOD']??'GET')==='GET'){ $rows=Db::pdo()->query('SELECT * FROM calls ORDER BY created_at DESC LIMIT 100')->fetchAll(\PDO::FETCH_ASSOC); echo json_encode(['calls'=>$rows]); return;} http_response_code(404); echo json_encode(['error'=>'not_found']); } }
