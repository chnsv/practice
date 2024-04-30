<?php 
   $host = 'localhost'; // адрес сервера
   $db_name = 'u67282'; // имя базы данных
   $user = 'u67282'; // имя пользователя
   $password = '8272598'; // пароль

   // создание подключения к базе   
    $connection = mysqli_connect($host, $user, $password, $db_name);
    $opt = $_POST['options'];

    // Функция для оформления вывода в виде таблицы
    function displayTable($rows) {
        echo "<table border='1' cellpadding='10'>";
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    if ($opt=="1") {
        $query = "SELECT * FROM public WHERE type_pub = 'Газета' AND name_pub LIKE 'П%'";
        
        $result = mysqli_query($connection, $query);
        $rows = [];
        while($row = $result->fetch_assoc()) {
            $rows[] = [$row['id_pub'], $row['index_pub'], $row['type_pub'], $row['name_pub'], $row['price_pub']];
        }
        displayTable($rows);
    }

    if ($opt=="2") {
        $query = "SELECT h.name_hum, h.street_hum, h.num_house, h.flat_house, p.name_pub FROM human h JOIN public p ON h.id_pub = p.id_pub WHERE h.street_hum = 'Улица Садовая' AND p.index_pub = 12123";
        
        $result = mysqli_query($connection, $query);
        $rows = [];
        while($row = $result->fetch_assoc()){
            $rows[] = [$row['name_hum'], $row['street_hum'], $row['num_house'], $row['flat_house'], $row['name_pub']];
        }
        displayTable($rows);
    }

    if ($opt=="3") {
        $query = "SELECT * FROM human WHERE street_hum = 'Улица Садовая' AND num_house IN (2, 7, 8)";
        
        $result = mysqli_query($connection, $query);
        $rows = [];
        while($row = $result->fetch_assoc()){
            $rows[] = [$row['id_hum'], $row['name_hum'], $row['street_hum'], $row['num_house'], $row['flat_house'], $row['id_pub'], $row['date_pod'], $row['sub_period']];
        }
        displayTable($rows);
    }

    if ($opt=="4") {
        // Проверяем, был ли отправлен запрос на получение информации об издании
        if (isset($_POST['index_pub'])) {
            $index_pub = $_POST['index_pub']; // Получаем значение индекса издания из формы
        
            // Запрос к базе данных для получения информации об издании с заданным индексом
            $query = "SELECT * FROM public WHERE index_pub = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $index_pub); // Привязываем значение индекса к параметру запроса
            $stmt->execute();
            $result = $stmt->get_result();
        
            // Создаем массив для хранения строк данных
            $rows = [];
            while($row = $result->fetch_assoc()) {
                $rows[] = [$row['id_pub'], $row['index_pub'], $row['type_pub'], $row['name_pub'], $row['price_pub']];
            }
        
            // Выводим информацию об издании в виде таблицы
            if (!empty($rows)) {
                displayTable($rows);
            } else {
                echo "No publication found with this index.";
            }
        
            $stmt->close();
        }
    }    

    if ($opt=="5") {
        if (isset($_POST['lowerPrice']) && isset($_POST['upperPrice'])) {
            $lowerPrice = $_POST['lowerPrice'];
            $upperPrice = $_POST['upperPrice'];
        
            $query = "SELECT * FROM public WHERE price_pub BETWEEN ? AND ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ii", $lowerPrice, $upperPrice); // Привязываем значения границ к параметрам запроса
            $stmt->execute();
            $result = $stmt->get_result();
        
            $rows = [];
            while($row = $result->fetch_assoc()) {
                $rows[] = [$row['id_pub'], $row['index_pub'], $row['type_pub'], $row['name_pub'], $row['price_pub']];
            }
        
            if (!empty($rows)) {
                displayTable($rows);
            } else {
                echo "No publications found within the specified price range.";
            }
        
            $stmt->close();
        }
    }
    
    if ($opt=="6") {
        $query = "SELECT p.index_pub, p.name_pub, p.price_pub, h.date_pod, h.sub_period, p.price_pub * h.sub_period AS subscription_cost FROM human h JOIN public p ON h.id_pub = p.id_pub";
        
        $result = mysqli_query($connection, $query);
        $rows = [];
        while($row = $result->fetch_assoc()){
            $rows[] = [$row['index_pub'], $row['name_pub'], $row['price_pub'], $row['date_pod'], $row['sub_period'], $row['subscription_cost']];
        }
        displayTable($rows);
    }

    if ($opt=="7") {
        $query = "SELECT type_pub, AVG(price_pub) AS average_price FROM public GROUP BY type_pub";
        
        $result = mysqli_query($connection, $query);
        $rows = [];
        while($row = $result->fetch_assoc()){
            $rows[] = [$row['type_pub'], $row['average_price']];
        }
        displayTable($rows);
    }

    if ($opt=="8") {
        $query = "SELECT street_hum, COUNT(id_hum) AS subscribers_count FROM human GROUP BY street_hum";
        
        $result = mysqli_query($connection, $query);
        $rows = [];
        while($row = $result->fetch_assoc()){
            $rows[] = [$row['street_hum'], $row['subscribers_count']];
        }
        displayTable($rows);
    }

    // закрываем соединение с базой
    mysqli_close($connection);
?>
