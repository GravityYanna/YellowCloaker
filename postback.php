<?php

require_once __DIR__ . '/debug.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/logging.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/macros.php';
require_once __DIR__ . '/requestfunc.php';

$curLink = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$subid = $_REQUEST['subid'] ?? '';
if ($subid === '') {
    http_response_code(500);
    echo "No subid found! Url: $curLink";
    return;
}
$status = $_REQUEST['status'] ?? '';
if ($status === '') {
    http_response_code(500);
    echo "No status found! Url: $curLink";
    return;
}
$payout = $_REQUEST['payout'] ?? '';
if ($payout === '') {
    http_response_code(500);
    echo "No payout found! Url: $curLink";
    return;
}
$inner_status = '';
switch ($status) {
    case $lead_status_name:
        $inner_status = 'Lead';
        break;
    case $purchase_status_name:
        $inner_status = 'Purchase';
        break;
    case $reject_status_name:
        $inner_status = 'Reject';
        break;
    case $trash_status_name:
        $inner_status = 'Trash';
        break;
}

if ($subid === '' || $status === '')
    $msg = "Error! No subid or status! {$curLink}";
else
    $msg = "$subid, $status, $payout";
add_log("postback", $msg);

$db = new Db();
$updated = $db->update_status($subid, $inner_status, $payout);

if ($updated) {
    process_s2s_posbacks($s2s_postbacks, $inner_status, $subid);
    http_response_code(200);
    echo "Postback for subid $subid with status $status and payout $payout accepted.";
} else {
    http_response_code(404);
    echo "Postback for subid $subid with status $status and payout $payout NOT accepted! Subid NOT FOUND.";
}

function process_s2s_posbacks(array $s2s_postbacks, string $inner_status, string $subid): void
{
    global $tds_api_key;
    $mp = new MacrosProcessor($tds_api_key, $subid);
    foreach ($s2s_postbacks as $s2s) {
        if (!in_array($inner_status, $s2s['events'])) continue;
        if (empty($s2s['url'])) continue;
        $final_url = str_replace('{status}', $inner_status, $final_url);
        $final_url = $mp->replace_url_macros($s2s['url']);
        $s2s_res = '';
        switch ($s2s['method']) {
            case 'GET':
                $s2s_res = get($final_url);
                break;
            case 'POST':
                $urlParts = explode('?', $final_url);
                if (count($urlParts) == 1)
                    $params = array();
                else
                    parse_str($urlParts[1], $params);
                $s2s_res = post($urlParts[0], $params);
                break;
        }
        add_log("postback", "{$s2s['method']}, $final_url, $inner_status, {$s2s_res['info']['http_code']}");
    }
}