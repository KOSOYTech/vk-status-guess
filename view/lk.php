<?php

#######################
###ПРИСВОЕНИЕ ТОКЕНА###
#######################
    session_start();

require_once __DIR__.'/../model/vkauthdata.php';

if ( isset( $_GET['code'] ) ) {
  

 
	$params = array(
		'client_id'     => $clientId,
		'client_secret' => $clientSecret,
		'code'          => $_GET['code'],
		'redirect_uri'  => $redirectUri
	);
 
	if (!$content = @file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params))) 	  {
		$error = error_get_last();
		throw new Exception('HTTP request failed. Error: ' . $error['message']);
	}
 
	$response = json_decode($content);
 
	// Если при получении токена произошла ошибка
	if (isset($response->error)) {
		throw new Exception('При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
	}
 
	$token = $response->access_token; // Токен
	$expiresIn = $response->expires_in; // Время жизни токена
	$userId = $response->user_id; // ID авторизовавшегося пользователя
 
	// Сохраняем токен в сессии
	$_SESSION['token'] = $token;
  
	// Сохраняем ID игрока в сессии
    $_SESSION['userid'] = $userId;
  
 
 
} elseif ( isset( $_GET['error'] ) ) { // Если при авторизации произошла ошибка
 
	throw new Exception( 'При авторизации произошла ошибка. Error: ' . $_GET['error']
	                     . '. Error reason: ' . $_GET['error_reason']
	                     . '. Error description: ' . $_GET['error_description'] );
}

###################################
###ПОЛУЧАЕМ ИМЯ ИГРОКА ВКОНТАКТЕ###
###################################

if ($_SESSION['token'] !== '') {

	$params = array(
    	'v' => '5.107', // Версия API
    	'access_token' => $_SESSION['token'], // Токен
    	'user_ids' =>  $_SESSION['userid'], // ID пользователей
    	'fields' => 'first_name' // Извлекаем имя
	);

	if (!$content = @file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params))) {
    $error = error_get_last();
    throw new Exception('HTTP request failed. Error: ' . $error['message']);
	}


	$response = json_decode($content);
 
	$response = $response->response;
	$response = $response[0];
	$gamerName = $response->first_name;
    $_SESSION['gamerName'] = $gamerName;

}



###############################################################
###ПОСЛЕ АВТОРИЗАЦИИ ЗАПИСЫВАЕМ ЧЕЛОВЕКА В ТУРНИРНУЮ ТАБЛИЦУ###
###############################################################


require_once __DIR__.'/../model/mysqlconnect.php';


// Проверяем, есть ли уже человек в базе
$check = mysqli_query($link, "select 1 from leaderboard where id = '{$_SESSION['userid']}' limit 1");
$checkResult = $check->num_rows;
if (!$checkResult) {
    echo "Игрок добавлен";
	$sql = mysqli_query($link, "INSERT INTO `leaderboard` (`ID`, `imya`, `points`) VALUES ('{$_SESSION['userid']}', '{$_SESSION['gamerName']}', '0')");
//Если вставка прошла успешно
	if ($sql) {
      echo '<p>Данные успешно добавлены в таблицу.</p>';
    } else {
      echo '<p>Произошла ошибка: ' . mysqli_error($link) . '</p>';
    }
}

echo '<p>Доброго времени суток, ' . $_SESSION['gamerName'] . ', и спасибо, что выбрали нашу игру :). Нажмите кнопку играть внизу или посмотрите таблицу лидеров</p>';

echo '<p><a href="http://vkstatus.tmweb.ru/view/game.php">Играть</a></p>';

echo '<p><a href="http://vkstatus.tmweb.ru/view/leaderboard.php">Таблица лидеров</a></p>';
  
###########################
###КНОПКА ВЫХОДА ИЗ ИГРЫ###
###########################

echo '<form method="post" action="http://vkstatus.tmweb.ru/view/auth.php"><input type="submit" name="logout" value="ВЫЙТИ"></form>';

?>