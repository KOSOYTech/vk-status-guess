<?php

session_start();


########################################################
###ПЕРЕД НОВЫМ ВОПРОСОМ ПРОВЕРЯЕМ ОТВЕТ НА ПРЕДЫДУЩИЙ###
########################################################

require_once __DIR__.'/../model/mysqlconnect.php';

// Начинаем игру, если авторизация пройдена
if	($_SESSION['token']) {
  
// Записываем текущий токен из сессии в переменную
$token = $_SESSION['token'];
  
$fileForCheck = __DIR__.'/../answers/' . $token . '.html';
$rightAnswerForCheck = file_get_contents($fileForCheck);
//ДЛЯ ОТЛАДКИ
//echo $rightAnswerForCheck;
if ($_POST['answer'] && $_POST['answer'] == $rightAnswerForCheck) {
  	echo "Ответ верный! +1 ОЧКО";
    $addPoint = mysqli_query($link, "UPDATE leaderboard SET points=points+1 where id ='{$_SESSION['userid']}'");
} else if ($_POST['answer'] && $_POST['answer'] !== $rightAnswerForCheck) {
  	echo "Ответ неверный! -3 ОЧКА";
    $removePoint = mysqli_query($link, "UPDATE leaderboard SET points=points-3 where id ='{$_SESSION['userid']}'");
} else {
	echo "";
}

##########################################
###ПОЛУЧЕНИЕ СЛУЧАЙНОГО СТАТУСА И ИМЕНИ###
##########################################

// Инициализирум переменную, в которой будет храниться статус
$status="";

// Запускаем цикл перебора пользователей до тех пор, пока не будет найдет статус
while ($status == "") {
  
	// Получаем случайное число, где максимальное значение - текущее количество пользователей ВКонтакте
	$randomUser = rand(1, 601000000);

	// Формируем параметры для отправки
	$params = array(
 	   'v' => '5.107', // Версия API
 	   'access_token' => $token, // Токен
 	   'user_id' => $randomUser // ID пользователя
	);
  
	// Делаем запрос с проверкой на ошибки
	if (!$content = @file_get_contents('https://api.vk.com/method/status.get?' . http_build_query($params))) {
	    $error = error_get_last();
	    throw new Exception('HTTP request failed. Error: ' . $error['message']);
	}

	// Преобразуем ответ из формата JSON
	$response = json_decode($content);

	// Извлекаем нужные данные
	$response = $response->response;
	$status = $response->text;
  
} // Конец цикла перебора пользователей

// Если статус найден - извлекаем имя этого пользователя
 
sleep(1);

	$params = array(
    	'v' => '5.107', // Версия API
    	'access_token' => $token, // Токен
    	'user_ids' => $randomUser, // ID пользователей
    	'fields' => 'first_name' // Извлекаем имя
	);

	if (!$content = @file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params))) {
    $error = error_get_last();
    throw new Exception('HTTP request failed. Error: ' . $error['message']);
	}

	$response = json_decode($content);
 
	$response = $response->response;
	$response = $response[0];
	$realName = $response->first_name;
//} // Конец извлечения имени автора статуса

#############################
###ПОЛУЧАЕМ ПОДСТАВНОЕ ИМЯ###
#############################

// Инициализируем переменную, в которой будем хранить подставное имя
$fakeName == '';

// Цикл получения нормального фейкового имени
while ($fakeName == "" or $fakeName == "DELETED") {

	// Получаем случайное число, где максимальное значение - текущее количество пользователей ВКонтакте
	$randomFakeUser = rand(1, 601000000);
 
	$params = array(
    	'v' => '5.107', // Версия API
    	'access_token' => $token, // Токен
    	'user_ids' => $randomFakeUser, // ID пользователей
    	'fields' => 'first_name' // Извлекаем имя
	);

	if (!$content = @file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params))) {
    $error = error_get_last();
    throw new Exception('HTTP request failed. Error: ' . $error['message']);
	}

	$response = json_decode($content);
 
	$response = $response->response;
	$response = $response[0];
	$fakeName = $response->first_name;
} // Конец цикла получения подставного имени
  
########################################
###ЗАПИСЫВАЕМ ПРАВИЛЬНЫЙ ОТВЕТ В ФАЙЛ###
########################################

$file = __DIR__.'/../answers/' . $token . '.html';
file_put_contents($file, $realName);
$rightAnswer = file_get_contents($file);
//ДЛЯ ОТЛАДКИ
//echo "Ответ из файла: " . $rightAnswer;
  
######################################################
###ПРИСВАИВАЕМ ПРАВИЛЬНЫЙ ОТВЕТ В СЛУЧАЙНОМ ПОРЯДКЕ###
######################################################
  
// Рандомно определяем, на какой позиции будет правильный ответ
$randomPlace = rand(1, 2);
 
if ($randomPlace == 1) {
	$variant1 = $realName;
  	$variant2 = $fakeName;
  } else {
	$variant1 = $fakeName;
  	$variant2 = $realName;
}
  
##############################
###ВЫВОДИМ ДАННЫЕ ОБ ИГРОКЕ###
##############################
  
echo '<p>ВАШ ID: ' . $_SESSION['userid'] . '</p>';
  
############################################
###ДАННЫЕ О ПРАВИЛЬНОМ ОТВЕТЕ ДЛЯ ОТЛАДКИ###
############################################
  
//echo '<p>Правильный ответ: ' . $realName . ' ID: ' . $randomUser . '</p>';
//echo '<p>Неправильный ответ: ' . $fakeName . ' ID: ' . $randomFakeUser . '</p>';

###########################
###ВЫВОДИМ ДАННЫЕ ИГРОКУ###
###########################

echo "<p>Предположи, как зовут автора этого статуса?</p>";
echo $status;
echo '<form method="post"><p>Варианты ответа:</p>';
echo '<p>Вариант 1:<input type="submit" name="answer" value="' . $variant1 . '"></p>';
echo '<p>Вариант 2:<input type="submit" name="answer" value="' . $variant2 . '"></p></form>';
  
echo '<a href="http://vkstatus.tmweb.ru/view/lk.php">Вернуться в личный кабинет</a>';

} // Конец игры

?>