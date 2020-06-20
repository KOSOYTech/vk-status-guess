<?php

#############################
###ВЫВОДИМ ТАБЛИЦУ ЛИДЕРОВ###
#############################

require_once __DIR__.'/../model/mysqlconnect.php';

$leaderBoard = mysqli_query($link, "SELECT * FROM leaderboard
ORDER BY points DESC;");
    
echo "<table style='margin-left: auto; margin-right: auto; font-size: 35px;'><tbody>";
echo "<tr><td>ID пользователя</td><td>Имя пользователя</td><td>Количество очков</td></tr>";
    while ($row = mysqli_fetch_row($leaderBoard)) {
        echo '<tr>';
        printf ("<td>%s</td> <td>%s</td> <td>%s</td><br>\n", $row[0], $row[1], $row[2]);
      	echo '</tr>';
    }
echo "</tbody></table>";

echo '<a href="http://vkstatus.tmweb.ru/view/lk.php">В личный кабинет</a>';

?>