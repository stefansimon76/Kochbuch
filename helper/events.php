<?php
declare(strict_types=1);

/**
 * @param string $name
 * @param array $data
 * @param mixed|null $action
 *
 * @return mixed
 */
function event(string $name, array $data = [], callable $action = null, int $priority = 0) {
    static $events = [];
    if ($action) {
        $events[$name][] = [
            'priority' => $priority,
            'action' => $action
        ];
        return null;
    }
    if (!isset($events[$name])) {
        return null;
    }
    $eventData = $events[$name];

    usort($eventData, function($a, $b) {
        return $b['priority'] <=> $a['priority'];
    });

    $content = ob_start();
    foreach ($eventData as $event) {
        $data = array_values($data);
        echo $event['action'](...$data);
    }
    $content = ob_get_clean();
    return $content;
}


define('EVENT_ACCOUNT_CREATED', 'event.accountCreated');
define('EVENT_ACCOUNT_LOGIN', 'event.accoountLogin');
define('EVENT_ACCOUNT_LOGOUT', 'event.accoountLogout');

event(EVENT_ACCOUNT_LOGIN,[], function(string $loginname, bool $stayLoggedIn) {
    if (!$stayLoggedIn) {
        return null;
    }
    $salt = microtime().rand();
    $rememberMeToken =  hash('sha256',$loginname.$salt);
    $expires = date_modify(date_create(), '+30 days');
    setcookie('rememberMeToken',$rememberMeToken, date_timestamp_get($expires));
    updateRememberMeToken($loginname,$rememberMeToken);
});

event(EVENT_ACCOUNT_LOGOUT,[],function(int $userId){
    $user = Benutzer::findByUserID($userId);
    if ($user != null) {
        $loginname = $user->loginname;
        setcookie('rememberMeToken', '', -1);
        updateRememberMeToken($loginname, '');
    }
});

event(EVENT_BEFORE,[],function(){
    loginByRememberMeToken();
});