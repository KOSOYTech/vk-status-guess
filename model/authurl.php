<?php

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

echo json_encode($params);

?>