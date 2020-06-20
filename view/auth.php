<?php

############################################################
###ПРЕДВАРИТЕЛЬНЫЙ ВЫХОД ИЗ ИГРЫ, ЕСЛИ БЫЛА НАЖАТА КНОПКА###
############################################################

// Токен будем хранить в сессии
session_start();


if ($_POST['logout']) {
	unset($_SESSION['token']);
    unset($_SESSION['userid']);
    unset($_SESSION['gamerName']);
    echo "Вы успешно вышли из игры!";
}


#################
###АВТОРИЗАЦИЯ###
#################
 
require_once __DIR__.'/../model/vkauthdata.php';
 
// Формируем ссылку для авторизации
$params = array(
	'client_id'     => $clientId,
	'redirect_uri'  => $redirectUri,
	'response_type' => 'code',
	'v'             => '5.74', // (обязательный параметр) версия API, которую Вы используете
 
	// Права доступа приложения https://vk.com/dev/permissions
	// Если указать "offline", полученный access_token будет "вечным" (токен умрёт, если пользователь сменит свой пароль или удалит приложение).
	// Если не указать "offline", то полученный токен будет жить 12 часов.
	'scope'         => 'status,offline',
);


// Выводим на экран ссылку для открытия окна диалога авторизации
if (!$_SESSION['token']) {echo '<div id="auth-form"><a href="http://oauth.vk.com/authorize?' . http_build_query( $params ) . '">Авторизация через ВКонтакте</a></div>';} else {
	echo 'Вы уже авторизованы';  
}

?>

<!-- Загрузим jQuery. -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<!-- Загрузим React. -->
<!-- Примечание: для деплоя на продакшен замените окончание «development.js» на «production.min.js». -->
<script src="https://unpkg.com/react@16/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>

<!-- Загрузим наш React-компонент. -->
<script type="text/babel" src="http://vkstatus.tmweb.ru/view/auth.js"></script>