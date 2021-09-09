<?php
const API_URL = "https://api.adcombo.com/api/v2/order/create/";
const API_KEY = "95b9c2ddd4e6cad2ef1409db1bb054ea";
const OFFER_ID = '29298'; // ID выбранного оффера
const FIO_FIELD = 'name'; // Как называется поле на ленде с именем/фио
const PHONE_FIELD = 'phone'; // Как называется поле на ленде с телефоном

// Поля ниже желательно редиректить обратно на ленд
// Куда редиректим если это не пост запрос с формой
$urlForNotPost = 'index.php';
// Куда редиректим если имя или телефон не заполнены
$urlForEmptyRequiredFields = 'index.php';
// Куда редиректим если сервер ответил что-то непонятное
$urlForNotJson = 'index.php';
// Куда редиректим если всё хорошо
$urlSuccess = 'confirm.php';

function getUserIP() {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

$isCurlEnabled = function(){
    return function_exists('curl_version');
};
if (!$isCurlEnabled) {
    echo "<pre>";
    echo "pls install curl\n";
    echo "For *unix open terminal and type this:\n";
    echo 'sudo apt-get install curl && apt-get install php-curl';
    die;
}
$args = [
    'api_key' => API_KEY,
    'name' => $_POST[FIO_FIELD],
    'phone' => $_POST[PHONE_FIELD],
    'offer_id' => OFFER_ID,
    'country_code' => key_exists('geo', $_POST) ? $_POST['geo'] : null,
    'price' => '49',
    'base_url' => 'http://my-domain.com/',
    'ip' => getUserIp(),
    'referrer' => 'http://my-click-site.com',
    'subacc' => key_exists('subacc', $_POST) ? $_POST['subacc'] : null,
    'subacc2' => key_exists('subacc2', $_POST) ? $_POST['subacc2'] : null,
    'subacc3' => '',
    'subacc4' => '',
    'utm_campaign' => '',
    'utm_content' => '',
    'utm_medium' => '',
    'utm_source' => '',
    'utm_term' => '',
    // you can add any extra field to get it in postback
    'clickid' => key_exists('clickid', $_POST) ? $_POST['clickid'] : null,
    ];
$url = API_URL.'?'.http_build_query($args);
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true
));
$res = curl_exec($curl);
curl_close($curl);
$res = json_decode($res, true);
if ($res['code'] == 'ok') {
    echo $res['msg'] . ": " . $res['order_id'];
} else {
    echo $res['error'];
}

// postback
$url = sprintf('https://kt.ulysse.team/1667048/postback?subid=%s&status=hold&from=adcombocom', $args['clickid']);
@file_get_contents($url);

$landing_url = '';
if (!empty($_REQUEST['landing_url'])) {
    $landing_url = urlencode('https://' . parse_url('https://' . urldecode($_REQUEST['landing_url']), PHP_URL_HOST) . '/pages/confirm');
}

if ($result === 'ok') {
    header('Location: '.$urlForEmptyRequiredFields);
    exit;
} else {
    $pixel = '';

    if (!empty($_POST['x'])) {
        $pixel = $_POST['x'];
    }

    $urlSuccess .= '?back=' . $landing_url . '&x=' . $pixel;
    header('Location: '.$urlSuccess);

    exit;
}
