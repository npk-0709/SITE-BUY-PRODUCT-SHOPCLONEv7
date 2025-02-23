<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
    $url = $_REQUEST['url'];
} else {
    header("Content-Type: application/json");
    echo json_encode(["error" => "URL is required"]);
    exit;
}
$parsed_url = parse_url($url);
$domain = $parsed_url['scheme'] . "://" . $parsed_url['host'] . "/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_ENCODING, "");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
    'Referer: ' . $domain,
    'Accept: */*',
    'X-Forwarded-For: ',
    'Via: ',
]);
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
} else {
    curl_setopt($ch, CURLOPT_HTTPGET, true);
}

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "Lỗi cURL: " . curl_error($ch);
    exit;
}
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);
//http_response_code($http_code);
if ($content_type) {
    header("Content-Type: $content_type");
}
echo $response;
