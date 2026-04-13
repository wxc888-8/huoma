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

if ($route === 'qrcodes/list' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);

    $kw = isset($_GET['kw']) ? trim($_GET['kw']) : '';
    $wechat_status = isset($_GET['wechat_status']) ? intval($_GET['wechat_status']) : -1;
    $state = isset($_GET['state']) ? intval($_GET['state']) : -1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    if ($limit < 1) $limit = 20;
    if ($limit > 100) $limit = 100;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    if ($offset < 0) $offset = 0;

    $where = "uid='{$uid}'";
    if ($kw !== '') {
        $kw2 = daddslashes($kw);
        $where .= " AND (name LIKE '%{$kw2}%' OR entry_domain LIKE '%{$kw2}%' OR landing_domain LIKE '%{$kw2}%' OR remarks LIKE '%{$kw2}%')";
    }
    if ($wechat_status !== -1) $where .= " AND wechat_status='{$wechat_status}'";
    if ($state !== -1) $where .= " AND state='{$state}'";

    $total = $DB->count("SELECT COUNT(*) FROM dwz_qrcode WHERE {$where}");
    $rs = $DB->query("SELECT * FROM dwz_qrcode WHERE {$where} ORDER BY id DESC LIMIT {$offset},{$limit}");
    $rows = [];
    while ($res = $DB->fetch($rs)) {
        $rows[] = [
            'id' => intval($res['id']),
            'name' => isset($res['name']) ? $res['name'] : '',
            'code' => isset($res['code']) ? $res['code'] : '',
            'qr_url' => isset($res['qr_url']) ? $res['qr_url'] : '',
            'entry_domain' => isset($res['entry_domain']) ? $res['entry_domain'] : '',
            'landing_domain' => isset($res['landing_domain']) ? $res['landing_domain'] : '',
            'views' => isset($res['views']) ? intval($res['views']) : 0,
            'ip_count' => isset($res['ip_count']) ? intval($res['ip_count']) : 0,
            'wechat_status' => isset($res['wechat_status']) ? intval($res['wechat_status']) : 0,
            'state' => isset($res['state']) ? intval($res['state']) : 0,
            'addtime' => isset($res['addtime']) ? $res['addtime'] : null,
            'updatetime' => isset($res['updatetime']) ? $res['updatetime'] : null,
            'remarks' => isset($res['remarks']) ? $res['remarks'] : ''
        ];
    }
    api_json(200, 'ok', ['total' => intval($total), 'rows' => $rows]);
}

if ($route === 'qrcodes/info' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (!$id) api_json(400, '参数错误');
    $qr = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}' LIMIT 1");
    if (!$qr) api_json(404, '活码不存在');
    $data_rs = $DB->query("SELECT * FROM dwz_qrcode_data WHERE qid='{$id}' ORDER BY sort ASC, id ASC");
    $items = [];
    while ($row = $DB->fetch($data_rs)) {
        $items[] = [
            'id' => intval($row['id']),
            'jump_url' => isset($row['jump_url']) ? $row['jump_url'] : '',
            'image_url' => isset($row['image_url']) ? $row['image_url'] : '',
            'threshold' => isset($row['threshold']) ? intval($row['threshold']) : 0,
            'sort' => isset($row['sort']) ? intval($row['sort']) : 0
        ];
    }
    api_json(200, 'ok', ['qr' => $qr, 'items' => $items]);
}

if ($route === 'qrcodes/save' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);

    $input = api_read_input();
    $id = isset($input['id']) ? intval($input['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
    $name = isset($input['name']) ? trim($input['name']) : (isset($_POST['name']) ? trim($_POST['name']) : '');
    $entry_domain = isset($input['entry_domain']) ? trim($input['entry_domain']) : (isset($_POST['entry_domain']) ? trim($_POST['entry_domain']) : '');
    $landing_domain = isset($input['landing_domain']) ? trim($input['landing_domain']) : (isset($_POST['landing_domain']) ? trim($_POST['landing_domain']) : '');
    $jump_time = isset($input['jump_time']) ? intval($input['jump_time']) : (isset($_POST['jump_time']) ? intval($_POST['jump_time']) : 0);
    $show_safe = isset($input['show_safe']) ? intval($input['show_safe']) : (isset($_POST['show_safe']) ? intval($_POST['show_safe']) : 1);
    $threshold_type = isset($input['threshold_type']) ? intval($input['threshold_type']) : (isset($_POST['threshold_type']) ? intval($_POST['threshold_type']) : 1);
    $infinite_loop = isset($input['infinite_loop']) ? intval($input['infinite_loop']) : (isset($_POST['infinite_loop']) ? intval($_POST['infinite_loop']) : 0);
    $remarks = isset($input['remarks']) ? trim($input['remarks']) : (isset($_POST['remarks']) ? trim($_POST['remarks']) : '');

    $items = [];
    if (isset($input['items']) && is_array($input['items'])) $items = $input['items'];
    elseif (isset($_POST['jump_url'])) {
        $jump_urls = $_POST['jump_url'];
        if (!is_array($jump_urls)) $jump_urls = [$jump_urls];
        $image_urls = isset($_POST['image_url']) ? $_POST['image_url'] : [];
        if (!is_array($image_urls)) $image_urls = [$image_urls];
        $thresholds = isset($_POST['threshold']) ? $_POST['threshold'] : [];
        if (!is_array($thresholds)) $thresholds = [$thresholds];
        for ($i = 0; $i < count($jump_urls); $i++) {
            $items[] = [
                'jump_url' => $jump_urls[$i],
                'image_url' => isset($image_urls[$i]) ? $image_urls[$i] : '',
                'threshold' => isset($thresholds[$i]) ? intval($thresholds[$i]) : 100
            ];
        }
    }

    if ($name === '' || $entry_domain === '' || $landing_domain === '') api_json(400, '参数不完整');
    if (empty($items)) api_json(400, '至少需要一个跳转地址');

    $DB->query("START TRANSACTION");
    try {
        if ($id) {
            $qr = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}' LIMIT 1");
            if (!$qr) throw new Exception('无权限或活码不存在');
            $sql = "UPDATE dwz_qrcode SET name='" . daddslashes($name) . "',entry_domain='" . daddslashes($entry_domain) . "',landing_domain='" . daddslashes($landing_domain) . "',jump_time='{$jump_time}',show_safe='{$show_safe}',threshold_type='{$threshold_type}',infinite_loop='{$infinite_loop}',remarks='" . daddslashes($remarks) . "',updatetime='{$date}' WHERE id='{$id}' AND uid='{$uid}'";
            if (!$DB->query($sql)) throw new Exception('保存失败');
            if (!$DB->query("DELETE FROM dwz_qrcode_data WHERE qid='{$id}'")) throw new Exception('保存失败');
        } else {
            $string_part = strtoupper(substr(md5($uid . time() . rand(1000, 9999)), 0, 8));
            $number_part = strval(rand(10000000, 99999999));
            $code = $string_part . '/' . $number_part;
            $sql = "INSERT INTO dwz_qrcode (uid,name,code,entry_domain,landing_domain,jump_time,show_safe,threshold_type,infinite_loop,remarks,addtime,updatetime,state,wechat_status) VALUES ('{$uid}','" . daddslashes($name) . "','" . daddslashes($code) . "','" . daddslashes($entry_domain) . "','" . daddslashes($landing_domain) . "','{$jump_time}','{$show_safe}','{$threshold_type}','{$infinite_loop}','" . daddslashes($remarks) . "','{$date}','{$date}',1,1)";
            if (!$DB->query($sql)) throw new Exception('创建失败');
            $row = $DB->get_row("SELECT MAX(id) AS id FROM dwz_qrcode WHERE uid='{$uid}'");
            $id = $row ? intval($row['id']) : 0;
            if (!$id) throw new Exception('创建失败');

            require_once ROOT . 'user/phpqrcode.php';
            $qrcode_dir = ROOT . 'user/qrcode';
            if (!file_exists($qrcode_dir)) mkdir($qrcode_dir, 0755, true);
            $qr_file = 'qrcode/' . $id . '.png';
            $qr_filepath = $qrcode_dir . '/' . $id . '.png';
            $random_sub = getrand2(mt_rand(5, 8));
            $entry_domain_parts = explode('.', preg_replace('#^https?://#i', '', $entry_domain));
            if (count($entry_domain_parts) <= 2) $qr_content = 'http://' . $random_sub . '.' . preg_replace('#^https?://#i', '', $entry_domain) . '/qr/' . $id;
            else $qr_content = 'http://' . preg_replace('#^https?://#i', '', $entry_domain) . '/qr/' . $id;
            QRcode::png($qr_content, $qr_filepath, 'L', 10, 2);
            $DB->query("UPDATE dwz_qrcode SET qr_url='" . daddslashes($qr_file) . "' WHERE id='{$id}' AND uid='{$uid}'");
        }

        for ($i = 0; $i < count($items); $i++) {
            $jump_url = isset($items[$i]['jump_url']) ? trim($items[$i]['jump_url']) : '';
            if ($jump_url === '') continue;
            $image_url = isset($items[$i]['image_url']) ? $items[$i]['image_url'] : '';
            $threshold = isset($items[$i]['threshold']) ? intval($items[$i]['threshold']) : 100;
            $sql = "INSERT INTO dwz_qrcode_data (qid,jump_url,image_url,threshold,sort,addtime) VALUES ('{$id}','" . daddslashes($jump_url) . "','" . daddslashes($image_url) . "','{$threshold}','{$i}','{$date}')";
            if (!$DB->query($sql)) throw new Exception('保存失败');
        }

        $DB->query("COMMIT");
        api_json(200, 'ok', ['id' => $id]);
    } catch (Exception $e) {
        $DB->query("ROLLBACK");
        api_json(400, $e->getMessage());
    }
}

if ($route === 'qrcodes/delete' && ($method === 'POST' || $method === 'DELETE')) {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $input = api_read_input();
    $id = isset($input['id']) ? intval($input['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
    if (!$id) api_json(400, '参数错误');
    if (!$DB->get_row("SELECT id FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}' LIMIT 1")) api_json(404, '活码不存在');
    $DB->query("START TRANSACTION");
    try {
        if (!$DB->query("DELETE FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}'")) throw new Exception('删除失败');
        $DB->query("DELETE FROM dwz_qrcode_data WHERE qid='{$id}'");
        $DB->query("COMMIT");
        api_json(200, 'ok', null);
    } catch (Exception $e) {
        $DB->query("ROLLBACK");
        api_json(400, $e->getMessage());
    }
}

if ($route === 'qrcodes/update-status' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $input = api_read_input();
    $id = isset($input['id']) ? intval($input['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
    $field = isset($input['field']) ? $input['field'] : (isset($_POST['field']) ? $_POST['field'] : '');
    $value = isset($input['value']) ? intval($input['value']) : (isset($_POST['value']) ? intval($_POST['value']) : 0);
    if (!$id || !in_array($field, ['state', 'wechat_status'], true)) api_json(400, '参数错误');
    if (!$DB->get_row("SELECT id FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}' LIMIT 1")) api_json(404, '活码不存在');
    if ($DB->query("UPDATE dwz_qrcode SET {$field}='{$value}' WHERE id='{$id}' AND uid='{$uid}'")) api_json(200, 'ok', null);
    api_json(500, '更新失败');
}

if ($route === 'qrcodes/stats' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $type = isset($_GET['type']) ? $_GET['type'] : 'day';
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
    if (!$id) api_json(400, '参数错误');
    if (!$DB->get_row("SELECT id FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}' LIMIT 1")) api_json(404, '活码不存在');

    $group_format = 'DATE(addtime)';
    if ($type === 'month') $group_format = 'DATE_FORMAT(addtime, "%Y-%m")';
    if ($type === 'year') $group_format = 'YEAR(addtime)';
    $sql = "SELECT {$group_format} AS date, COUNT(*) AS views, COUNT(DISTINCT ip) AS unique_ips FROM dwz_qrcode_visit WHERE qid='{$id}' AND DATE(addtime) BETWEEN '{$start_date}' AND '{$end_date}' GROUP BY {$group_format} ORDER BY date ASC";
    $rs = $DB->query($sql);
    $stats = [];
    while ($row = $DB->fetch($rs)) {
        $stats[] = [
            'date' => $row['date'],
            'views' => intval($row['views']),
            'unique_ips' => intval($row['unique_ips'])
        ];
    }
    api_json(200, 'ok', ['stats' => $stats]);
}

if ($route === 'domains/list' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $type = isset($_GET['type']) ? trim($_GET['type']) : '';
    if (!in_array($type, ['entry', 'landing'], true)) api_json(400, '无效的域名类型');
    $table = $type === 'entry' ? 'dwz_entry_domain' : 'dwz_landing_domain';
    $free = [];
    $rs = $DB->query("SELECT id,domain FROM {$table} WHERE state=1 AND is_paid=0 ORDER BY id ASC");
    while ($row = $DB->fetch($rs)) $free[] = ['id' => intval($row['id']), 'domain' => $row['domain']];
    $paid = [];
    $rs = $DB->query("SELECT id,domain FROM {$table} WHERE state=1 AND is_paid=1 AND uid='{$uid}' ORDER BY id ASC");
    while ($row = $DB->fetch($rs)) $paid[] = ['id' => intval($row['id']), 'domain' => $row['domain']];
    api_json(200, 'ok', ['free' => $free, 'paid' => $paid]);
}

if ($route === 'domains/store' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $type = isset($_GET['type']) ? trim($_GET['type']) : '';
    if (!in_array($type, ['entry', 'landing'], true)) api_json(400, '参数错误');
    $table = $type === 'entry' ? 'dwz_entry_domain' : 'dwz_landing_domain';
    $domains = [];
    $rs = $DB->query("SELECT * FROM {$table} WHERE is_paid=1 AND (uid IS NULL OR uid=0) ORDER BY id DESC");
    while ($row = $DB->fetch($rs)) {
        $domains[] = [
            'id' => intval($row['id']),
            'domain' => $row['domain'],
            'qqsafe' => isset($row['qqsafe']) ? intval($row['qqsafe']) : 1,
            'wxsafe' => isset($row['wxsafe']) ? intval($row['wxsafe']) : 1,
            'price' => isset($row['price']) ? intval($row['price']) : 0,
            'remark' => isset($row['remark']) ? $row['remark'] : '',
            'addtime' => isset($row['addtime']) ? $row['addtime'] : null
        ];
    }
    api_json(200, 'ok', ['domains' => $domains]);
}

if ($route === 'domains/mine' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $domains = [];
    $rs = $DB->query("SELECT * FROM dwz_entry_domain WHERE uid='{$uid}' ORDER BY id DESC");
    while ($row = $DB->fetch($rs)) $domains[] = ['id' => intval($row['id']), 'domain' => $row['domain'], 'type' => 'entry', 'addtime' => isset($row['addtime']) ? $row['addtime'] : null];
    $rs = $DB->query("SELECT * FROM dwz_landing_domain WHERE uid='{$uid}' ORDER BY id DESC");
    while ($row = $DB->fetch($rs)) $domains[] = ['id' => intval($row['id']), 'domain' => $row['domain'], 'type' => 'landing', 'addtime' => isset($row['addtime']) ? $row['addtime'] : null];
    api_json(200, 'ok', ['domains' => $domains]);
}

if ($route === 'domains/buy' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $input = api_read_input();
    $domain_id = isset($input['domain_id']) ? intval($input['domain_id']) : (isset($_POST['domain_id']) ? intval($_POST['domain_id']) : 0);
    $domain_type = isset($input['domain_type']) ? trim($input['domain_type']) : (isset($_POST['domain_type']) ? trim($_POST['domain_type']) : '');
    if (!$domain_id || !in_array($domain_type, ['entry', 'landing'], true)) api_json(400, '参数错误');
    $table = $domain_type === 'entry' ? 'dwz_entry_domain' : 'dwz_landing_domain';
    $domain = $DB->get_row("SELECT * FROM {$table} WHERE id='{$domain_id}' AND is_paid=1 AND (uid IS NULL OR uid=0) LIMIT 1");
    if (!$domain) api_json(400, '域名不存在或已被购买');
    $points_row = $DB->get_row("SELECT points FROM dwz_user WHERE id='{$uid}' LIMIT 1");
    $points = $points_row ? intval($points_row['points']) : 0;
    $price = isset($domain['price']) ? intval($domain['price']) : 0;
    if ($points < $price) api_json(400, '积分不足，请先充值积分');
    $DB->query("START TRANSACTION");
    try {
        if (!$DB->query("UPDATE dwz_user SET points=points-{$price} WHERE id='{$uid}'")) throw new Exception('扣除积分失败');
        $desc = '购买' . ($domain_type === 'entry' ? '入口' : '落地') . '域名：' . $domain['domain'];
        $DB->query("INSERT INTO dwz_points_log(uid, points, type, description, addtime) VALUES ('{$uid}', -{$price}, 'buy_domain', '" . daddslashes($desc) . "', '{$date}')");
        if (!$DB->query("UPDATE {$table} SET uid='{$uid}' WHERE id='{$domain_id}'")) throw new Exception('绑定域名失败');
        $DB->query("COMMIT");
        api_json(200, 'ok', null);
    } catch (Exception $e) {
        $DB->query("ROLLBACK");
        api_json(400, $e->getMessage());
    }
}

if ($route === 'domains/release' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $input = api_read_input();
    $domain_id = isset($input['domain_id']) ? intval($input['domain_id']) : (isset($_POST['domain_id']) ? intval($_POST['domain_id']) : 0);
    $domain_type = isset($input['domain_type']) ? trim($input['domain_type']) : (isset($_POST['domain_type']) ? trim($_POST['domain_type']) : '');
    if (!$domain_id || !in_array($domain_type, ['entry', 'landing'], true)) api_json(400, '参数错误');
    $table = $domain_type === 'entry' ? 'dwz_entry_domain' : 'dwz_landing_domain';
    $domain = $DB->get_row("SELECT * FROM {$table} WHERE id='{$domain_id}' AND uid='{$uid}' LIMIT 1");
    if (!$domain) api_json(400, '域名不存在或不属于您');
    $field = $domain_type === 'entry' ? 'entry_domain' : 'landing_domain';
    $domain_name = $domain['domain'];
    if ($DB->get_row("SELECT id FROM dwz_qrcode WHERE {$field}='" . daddslashes($domain_name) . "' AND uid='{$uid}' LIMIT 1")) api_json(400, '该域名正在被活码使用，请先修改相关活码');
    if ($DB->query("UPDATE {$table} SET uid=0 WHERE id='{$domain_id}'")) api_json(200, 'ok', null);
    api_json(500, '域名释放失败');
}

if ($route === 'billing/points-packages' && $method === 'GET') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $packages = [];
    $rs = $DB->query("SELECT * FROM dwz_points_package WHERE status=1 ORDER BY sort ASC, id ASC");
    while ($row = $DB->fetch($rs)) $packages[] = $row;
    api_json(200, 'ok', ['packages' => $packages]);
}

if ($route === 'billing/recharge-points' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $input = api_read_input();
    $package_id = isset($input['package_id']) ? intval($input['package_id']) : (isset($_POST['package_id']) ? intval($_POST['package_id']) : 0);
    $type = isset($input['type']) ? trim($input['type']) : (isset($_POST['type']) ? trim($_POST['type']) : '');
    if (!$package_id || !in_array($type, ['alipay', 'wxpay', 'qqpay'], true)) api_json(400, '参数错误');
    $package = $DB->get_row("SELECT * FROM dwz_points_package WHERE id='{$package_id}' AND status=1 LIMIT 1");
    if (!$package) api_json(400, '套餐不存在或已下架');
    $trade_no = date("YmdHis") . rand(111, 999);
    $name = '积分充值 - ' . $package['name'];
    $money = $package['price'];
    $points = $package['points_num'];
    $ip = real_ip();
    $sql = "INSERT INTO dwz_pay(trade_no,uid,num,money,ip,addtime,status,type,name) VALUES('" . daddslashes($trade_no) . "','{$uid}','{$points}','{$money}','" . daddslashes($ip) . "','{$date}',0,'" . daddslashes($type) . "','" . daddslashes($name) . "')";
    if (!$DB->query($sql)) api_json(500, '创建订单失败');
    api_json(200, 'ok', ['trade_no' => $trade_no]);
}

if ($route === 'billing/withdraw' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $input = api_read_input();
    $amount = isset($input['amount']) ? intval($input['amount']) : (isset($_POST['amount']) ? intval($_POST['amount']) : 0);
    $alipay_account = isset($input['alipay_account']) ? trim($input['alipay_account']) : (isset($_POST['alipay_account']) ? trim($_POST['alipay_account']) : '');
    $alipay_name = isset($input['alipay_name']) ? trim($input['alipay_name']) : (isset($_POST['alipay_name']) ? trim($_POST['alipay_name']) : '');
    if ($amount < 1) api_json(400, '提现金额最低1积分');
    if ($alipay_account === '' || $alipay_name === '') api_json(400, '请填写完整的支付宝信息');
    $userp = $DB->get_row("SELECT points FROM dwz_user WHERE id='{$uid}' LIMIT 1");
    $points = $userp ? intval($userp['points']) : 0;
    if ($points < $amount) api_json(400, '可用积分不足');
    $DB->query("START TRANSACTION");
    try {
        if (!$DB->query("INSERT INTO dwz_withdraw(uid,amount,alipay_account,alipay_name,create_time,status) VALUES ('{$uid}','{$amount}','" . daddslashes($alipay_account) . "','" . daddslashes($alipay_name) . "','{$date}',0)")) throw new Exception('提现申请提交失败');
        if (!$DB->query("UPDATE dwz_user SET points=points-{$amount} WHERE id='{$uid}'")) throw new Exception('扣除积分失败');
        $DB->query("INSERT INTO dwz_points_log(uid, points, type, description, addtime) VALUES ('{$uid}', -{$amount}, 'withdraw', '申请提现', '{$date}')");
        $DB->query("COMMIT");
        api_json(200, 'ok', null);
    } catch (Exception $e) {
        $DB->query("ROLLBACK");
        api_json(400, $e->getMessage());
    }
}

if ($route === 'user/update-profile' && $method === 'POST') {
    $userrow = api_auth_user();
    if (!$userrow) api_json(401, '未登录');
    $uid = intval($userrow['id']);
    $input = api_read_input();
    $name = isset($input['name']) ? trim($input['name']) : (isset($_POST['name']) ? trim($_POST['name']) : '');
    $mail = isset($input['mail']) ? trim($input['mail']) : (isset($_POST['mail']) ? trim($_POST['mail']) : '');
    $qq = isset($input['qq']) ? trim($input['qq']) : (isset($_POST['qq']) ? trim($_POST['qq']) : '');
    $pwd = isset($input['pwd']) ? $input['pwd'] : (isset($_POST['pwd']) ? $_POST['pwd'] : '');
    $pwd = is_string($pwd) ? trim($pwd) : '';

    if ($name === '' || $mail === '') api_json(400, '昵称与邮箱不能为空');
    if (!checkEmail($mail)) api_json(400, '邮箱格式不正确');
    if ($qq !== '' && !preg_match('#^[0-9]{5,11}+$#', $qq)) api_json(400, 'QQ格式不正确');

    $sets = [];
    $sets[] = "name='" . daddslashes($name) . "'";
    $sets[] = "mail='" . daddslashes($mail) . "'";
    if ($qq !== '') $sets[] = "qq='" . daddslashes($qq) . "'";

    $newPwdHash = '';
    if ($pwd !== '') {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        if (!$hash) api_json(500, '密码加密失败');
        $newPwdHash = $hash;
        $sets[] = "pwd='" . daddslashes($hash) . "'";
    }

    $sql = "UPDATE dwz_user SET " . implode(',', $sets) . " WHERE id='{$uid}'";
    if (!$DB->query($sql)) api_json(500, '保存失败');

    if ($newPwdHash !== '') {
        $session = md5($userrow['user'] . $newPwdHash . $password_hash);
        $token = authcode("{$uid}\t{$session}", 'ENCODE', SYS_KEY);
        api_json(200, 'ok', ['token' => $token]);
    }
    api_json(200, 'ok', null);
}

api_json(404, 'Not Found');
