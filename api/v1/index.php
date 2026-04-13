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
    if (in_array($code, [200, 400, 401, 404, 500], true)) http_response_code($code);
    else http_response_code(200);
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
    $pwd = strip_tags($pwd);
    if ($user === '' || $pwd === '') api_json(400, '请填写账号和密码');

    $res = $DB->get_row("select id,state,user,pwd,vip,points,name,mail,addtime from dwz_user where user='{$user}' limit 1");
    if (!$res || !verify_password_value($pwd, $res['pwd'])) api_json(401, '用户名或密码不正确');
    $res['pwd'] = maybe_upgrade_password($res['id'], $pwd, $res['pwd']);
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

if ($route === 'user/stats' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    $allUrl = $DB->count("select count(1) from dwz_url where uid='{$uid}' and deltime is null");
    $allView = $DB->count("select sum(view) from dwz_url where uid='{$uid}' and deltime is null");
    if ($allView == '') $allView = 0;
    $yView = tongjiPv1($yesterday, $uid);
    $yIp = tongjiIp1($yesterday, $uid);
    api_json(200, 'ok', [
        'total_links' => intval($allUrl),
        'total_views' => intval($allView),
        'yesterday_ips' => intval($yIp),
        'yesterday_views' => intval($yView)
    ]);
}

if ($route === 'links/list' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);

    $kw = isset($_GET['kw']) ? trim($_GET['kw']) : '';
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    if ($limit < 1) $limit = 20;
    if ($limit > 100) $limit = 100;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    if ($offset < 0) $offset = 0;
    $sort = isset($_GET['sort']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['sort']) : 'addtime';
    $order = isset($_GET['order']) ? strtoupper($_GET['order']) : (isset($_GET['sortOrder']) ? strtoupper($_GET['sortOrder']) : 'DESC');
    if (!in_array($order, ['ASC', 'DESC'], true)) $order = 'DESC';

    $where = "uid='{$uid}' and deltime is null";
    if ($kw !== '') {
        $kw2 = daddslashes($kw);
        $where .= " and (id like '%{$kw2}%' or dwz like '%{$kw2}%' or remarks like '%{$kw2}%' or url like '%{$kw2}%')";
    }

    $total = $DB->count("select count(*) from dwz_url where ({$where})");
    $rs = $DB->query("select * from dwz_url where ({$where}) order by {$sort} {$order} limit {$offset},{$limit}");
    $rows = [];
    while ($res = $DB->fetch($rs)) {
        $rows[] = [
            'id' => $res['id'],
            'dwz' => $res['dwz'],
            'u_state' => isset($res['u_state']) ? intval($res['u_state']) : null,
            'state' => isset($res['state']) ? intval($res['state']) : null,
            'remarks' => isset($res['remarks']) ? $res['remarks'] : '',
            'title' => isset($res['title']) ? $res['title'] : '',
            'view' => isset($res['view']) ? intval($res['view']) : 0,
            'pattern' => isset($res['pattern']) ? intval($res['pattern']) : 0,
            'addtime' => isset($res['addtime']) ? $res['addtime'] : null,
            'url' => isset($res['url']) ? base64_decode($res['url']) : ''
        ];
    }

    api_json(200, 'ok', ['total' => intval($total), 'rows' => $rows]);
}

if ($route === 'links/types' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $rs = $DB->query("SELECT * FROM dwz_api WHERE status=1 ORDER BY id ASC");
    $apis = [];
    while ($row = $DB->fetch($rs)) {
        $apis[] = [
            'id' => isset($row['id']) ? intval($row['id']) : 0,
            'name' => isset($row['name']) ? $row['name'] : '',
            'keyname' => isset($row['keyname']) ? $row['keyname'] : '',
            'type' => isset($row['type']) ? intval($row['type']) : 0,
            'domain' => isset($row['domain']) ? $row['domain'] : '',
            'num' => isset($row['num']) ? intval($row['num']) : 0
        ];
    }
    api_json(200, 'ok', ['apis' => $apis]);
}

if ($route === 'links/create' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);

    $input = api_read_input();
    $url = isset($input['url']) ? trim($input['url']) : (isset($_POST['url']) ? trim($_POST['url']) : '');
    $type = isset($input['type']) ? trim($input['type']) : (isset($_POST['type']) ? trim($_POST['type']) : '');
    $pattern = isset($input['pattern']) ? intval($input['pattern']) : (isset($_POST['pattern']) ? intval($_POST['pattern']) : 1);
    $id = isset($input['id']) ? trim($input['id']) : (isset($_POST['id']) ? trim($_POST['id']) : '');

    $pwd = isset($input['pwd']) ? trim($input['pwd']) : (isset($_POST['pwd']) ? trim($_POST['pwd']) : '');
    $remarks = isset($input['remarks']) ? trim($input['remarks']) : (isset($_POST['remarks']) ? trim($_POST['remarks']) : '');
    $visit = isset($input['visit']) ? trim($input['visit']) : (isset($_POST['visit']) ? trim($_POST['visit']) : '');
    $visiturl = isset($input['visiturl']) ? trim($input['visiturl']) : (isset($_POST['visiturl']) ? trim($_POST['visiturl']) : '');
    $title = isset($input['title']) ? trim($input['title']) : (isset($_POST['title']) ? trim($_POST['title']) : '');
    $jumpmb = isset($input['jumpmb']) ? trim($input['jumpmb']) : (isset($_POST['jumpmb']) ? trim($_POST['jumpmb']) : $conf['tz_template']);
    $qqjump = isset($input['qqjump']) ? trim($input['qqjump']) : (isset($_POST['qqjump']) ? trim($_POST['qqjump']) : '');
    $wxjump = isset($input['wxjump']) ? trim($input['wxjump']) : (isset($_POST['wxjump']) ? trim($_POST['wxjump']) : '');
    $alijump = isset($input['alijump']) ? trim($input['alijump']) : (isset($_POST['alijump']) ? trim($_POST['alijump']) : '');

    if ($url === '' || $type === '') api_json(400, '参数不完整');
    if (substr($url, 0, 4) !== 'http') api_json(400, '网址不正确');

    if ($id === '') {
        $id = getCode($conf['link_length']);
    } else {
        if ($id === '0') api_json(400, '后缀不能设置为0');
        if (!preg_match("/^[0-9a-zA-Z]{1,8}$/", $id)) api_json(400, '后缀只能为字母或数字，且长度为1-8之间');
        if ($DB->get_row("select id from dwz_url where id='{$id}' limit 1")) api_json(400, '该后缀已存在');
    }

    $ret = createUrl($type, $id, $uid, base64_encode($url), $pattern, $title, $remarks, $visit, $visiturl, $pwd, $jumpmb, $qqjump, $wxjump, $alijump);
    if (isset($ret['code']) && intval($ret['code']) === 0) {
        api_json(200, 'ok', ['id' => $id, 'dwz' => $ret['msg']]);
    }
    api_json(400, isset($ret['msg']) ? $ret['msg'] : '生成失败');
}

if ($route === 'links/update' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $vip = isset($userrow['vip']) && $userrow['vip'] > $date ? 1 : 0;

    $input = api_read_input();
    $id = isset($input['id']) ? trim($input['id']) : (isset($_POST['id']) ? trim($_POST['id']) : '');
    $url = isset($input['url']) ? trim($input['url']) : (isset($_POST['url']) ? trim($_POST['url']) : '');
    $pattern = isset($input['pattern']) ? intval($input['pattern']) : (isset($_POST['pattern']) ? intval($_POST['pattern']) : 1);
    $remarks = isset($input['remarks']) ? trim($input['remarks']) : (isset($_POST['remarks']) ? trim($_POST['remarks']) : '');
    $visit = isset($input['visit']) ? trim($input['visit']) : (isset($_POST['visit']) ? trim($_POST['visit']) : '');
    $visiturl = isset($input['visiturl']) ? trim($input['visiturl']) : (isset($_POST['visiturl']) ? trim($_POST['visiturl']) : '');
    $pwd = isset($input['pwd']) ? trim($input['pwd']) : (isset($_POST['pwd']) ? trim($_POST['pwd']) : '');
    $qqjump = isset($input['qqjump']) ? trim($input['qqjump']) : (isset($_POST['qqjump']) ? trim($_POST['qqjump']) : '');
    $wxjump = isset($input['wxjump']) ? trim($input['wxjump']) : (isset($_POST['wxjump']) ? trim($_POST['wxjump']) : '');
    $alijump = isset($input['alijump']) ? trim($input['alijump']) : (isset($_POST['alijump']) ? trim($_POST['alijump']) : '');
    $title = isset($input['title']) ? trim($input['title']) : (isset($_POST['title']) ? trim($_POST['title']) : '');
    $jumpmb = isset($input['jumpmb']) ? trim($input['jumpmb']) : (isset($_POST['jumpmb']) ? trim($_POST['jumpmb']) : $conf['tz_template']);

    if ($id === '' || $url === '') api_json(400, '参数不完整');
    if (substr($url, 0, 4) !== 'http') api_json(400, '网址不正确');

    $ret = editUrl($uid, $id, $vip, base64_encode($url), $pattern, $remarks, $title, $visit, $visiturl, $pwd, $jumpmb, $qqjump, $wxjump, $alijump);
    if (isset($ret['code']) && intval($ret['code']) === 0) api_json(200, 'ok', $ret);
    api_json(400, isset($ret['msg']) ? $ret['msg'] : '修改失败', $ret);
}

if ($route === 'links/delete' && ($method === 'POST' || $method === 'DELETE')) {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);

    $input = api_read_input();
    $id = isset($input['id']) ? trim($input['id']) : (isset($_POST['id']) ? trim($_POST['id']) : (isset($_REQUEST['id']) ? trim($_REQUEST['id']) : ''));
    if ($id === '') api_json(400, '参数不完整');
    if ($DB->count("select count(id) from dwz_url where id='{$id}' and uid='{$uid}' and deltime is null") == 0) api_json(400, '网址不存在');
    $rs = $DB->query("update dwz_url set deltime = '{$date}' where id = '{$id}'");
    if ($rs) api_json(200, 'ok', null);
    api_json(500, '删除失败');
}

api_json(404, 'Not Found');
