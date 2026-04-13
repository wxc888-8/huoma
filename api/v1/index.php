<?php
$nod_jump = true;
include dirname(__FILE__) . '/../../includes/common.php';

@header('Content-Type: application/json; charset=UTF-8');
@header('Access-Control-Allow-Origin:*');
@header('Access-Control-Allow-Headers: Content-Type, Authorization');
@header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(json_encode(['code' => 200, 'msg' => 'ok', 'data' => null]));
}

function api_json($code, $msg, $data = null)
{
    http_response_code($code === 200 ? 200 : ($code === 401 ? 401 : 400));
    exit(json_encode(['code' => $code, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE));
}

function api_read_input()
{
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function api_get_bearer_token()
{
    $auth = '';
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) $auth = $_SERVER['HTTP_AUTHORIZATION'];
    elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    if (!$auth) return '';
    if (stripos($auth, 'Bearer ') !== 0) return '';
    return trim(substr($auth, 7));
}

function api_auth_user()
{
    global $DB, $password_hash;
    $token = api_get_bearer_token();
    if (!$token && isset($_COOKIE['user_token'])) $token = daddslashes($_COOKIE['user_token']);
    if (!$token) return null;

    $decoded = authcode(daddslashes($token), 'DECODE', SYS_KEY);
    if (!$decoded) return null;

    $parts = explode("\t", $decoded);
    if (count($parts) < 2) return null;
    $id = intval($parts[0]);
    $sid = $parts[1];
    if ($id <= 0 || !$sid) return null;

    $userrow = $DB->get_row("select * from dwz_user where id='{$id}' limit 1");
    if (!$userrow) return null;
    $session = md5($userrow['user'] . $userrow['pwd'] . $password_hash);
    if ($session !== $sid) return null;
    if (isset($userrow['state']) && intval($userrow['state']) !== 1) return null;
    $userrow['_token'] = $token;
    return $userrow;
}

function api_current_path()
{
    if (isset($_GET['r']) && $_GET['r'] !== '') return trim($_GET['r'], '/');
    if (!empty($_SERVER['PATH_INFO'])) return trim($_SERVER['PATH_INFO'], '/');
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = parse_url($uri, PHP_URL_PATH);
    $base = '/api/v1/';
    if (is_string($path) && strpos($path, $base) !== false) {
        $pos = strpos($path, $base);
        return trim(substr($path, $pos + strlen($base)), '/');
    }
    return '';
}

$route = api_current_path();
$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

if ($route === 'auth/login' && $method === 'POST') {
    $input = api_read_input();
    $user = '';
    $pwd = '';
    if (isset($input['user'])) $user = $input['user'];
    if (isset($input['email']) && !$user) $user = $input['email'];
    if (isset($input['pwd'])) $pwd = $input['pwd'];
    if (isset($input['password']) && !$pwd) $pwd = $input['password'];
    if (!$user && isset($_POST['user'])) $user = $_POST['user'];
    if (!$pwd && isset($_POST['pwd'])) $pwd = $_POST['pwd'];
    if (!$user && isset($_POST['email'])) $user = $_POST['email'];
    if (!$pwd && isset($_POST['password'])) $pwd = $_POST['password'];

    $user = daddslashes(strip_tags($user));
    $pwd = daddslashes(strip_tags($pwd));
    if ($user === '' || $pwd === '') api_json(400, '请填写账号和密码');

    $res = $DB->get_row("select id,state,user,pwd,vip,points,name,mail,addtime from dwz_user where user='{$user}' and pwd='{$pwd}' limit 1");
    if (!$res) api_json(401, '用户名或密码不正确');
    if (isset($res['state']) && intval($res['state']) === 0) api_json(401, '当前账号已被封禁');

    $id = $res['id'];
    $clientip = real_ip();
    $DB->query("update dwz_user set lasttime='{$date}',lastip='{$clientip}' where id='{$id}'");
    $session = md5($res['user'] . $res['pwd'] . $password_hash);
    $token = authcode("{$id}\t{$session}", 'ENCODE', SYS_KEY);

    $vip = 0;
    if (isset($res['vip']) && $res['vip'] > $date) $vip = 1;

    api_json(200, '登录成功', [
        'token' => $token,
        'user' => [
            'id' => intval($res['id']),
            'user' => $res['user'],
            'name' => isset($res['name']) ? $res['name'] : '',
            'mail' => isset($res['mail']) ? $res['mail'] : '',
            'points' => isset($res['points']) ? intval($res['points']) : 0,
            'vip' => $vip,
            'addtime' => isset($res['addtime']) ? $res['addtime'] : null
        ]
    ]);
}

if ($route === 'user/me' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $vip = 0;
    if (isset($userrow['vip']) && $userrow['vip'] > $date) $vip = 1;
    api_json(200, 'ok', [
        'id' => intval($userrow['id']),
        'user' => $userrow['user'],
        'name' => isset($userrow['name']) ? $userrow['name'] : '',
        'mail' => isset($userrow['mail']) ? $userrow['mail'] : '',
        'points' => isset($userrow['points']) ? intval($userrow['points']) : 0,
        'vip' => $vip,
        'token' => isset($userrow['_token']) ? $userrow['_token'] : null
    ]);
}

api_json(404, 'Not Found');

