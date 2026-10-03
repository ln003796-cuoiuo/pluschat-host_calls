<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
use PlusChat\\Calls\\Http;
header('Content-Type: application/json; charset=utf-8');
try { (new Http())->handle(); } catch (Throwable $e) { http_response_code(500); echo json_encode(['error'=>'internal_error']); }
