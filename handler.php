<?php
require "db.php";

global $mysqli;
$response = array();

// Запись JSON-ответа в текстовый файл
$file = 'log/response_log.txt';

if(isset($_POST['event'])){
    switch(($_POST['event'])){
        case 'add_message':{
            // Добавление данных из формы
            $user_name = htmlspecialchars(strtolower($_POST['user_name']), ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars(strtolower($_POST['email']), ENT_QUOTES, 'UTF-8');
            $message_text = htmlspecialchars($_POST['message_text'], ENT_QUOTES, 'UTF-8');
            $captcha = "111";

            // Добавление информации об IP пользователя
            $user_ip = $_SERVER['REMOTE_ADDR'];

            // Добавление информации о браузере пользователя
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            // Добавление текущей даты
            $current_date = date('Y-m-d H:i:s');

            // валидация данных
            if(empty($user_name) || empty($email) || empty($message_text)){
                $response = array("success" => false, "message" => 'Пожалуйста, заполните все поля');
            }
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $response = array("success" => false, "message" => 'Некорректный формат email адреса');
            }
            else{
                $query = "INSERT INTO messages (user_name, email, message_text, user_ip, user_browser , date,  captcha) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('sssssss', $user_name, $email, $message_text, $user_ip, $user_browser, $current_date, $captcha);
                if($stmt->execute()){
                    // Сообщение добавлено
                    $created_id = $mysqli->insert_id;
                    $response = array("success" => true, "message" => "Сообщение создано!", "id" => $created_id);
                }else{
                    // Произошла ошибка
                    $response = array("success" => false, "message" => $mysqli->error);
                }
                $response['date'] = $current_date;
            }
            break;
        }
        case 'all_messages':{
            $sort = $mysqli->real_escape_string($_POST['sort']);
            $order = $_POST['order'] === 'asc' ? 'ASC' : 'DESC'; // Ensure only valid sort order is used

            $perPage = isset($_POST['perPage']) ? (int)$_POST['perPage'] : 5;
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

            $offset = ($page - 1) * $perPage;

            $query = "SELECT * FROM messages ORDER BY $sort $order LIMIT $perPage OFFSET $offset";
            $stmt = $mysqli->prepare($query);
            if($stmt->execute()){
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $response['messages'][] = $row;
                }
            }else{
                // Произошла ошибка
                $current_date = date('Y-m-d H:i:s');
                $error = array("success" => false, "message" => $mysqli->error , 'date' => $current_date);
                file_put_contents($file, json_encode($error), FILE_APPEND);
            }

            $queryTotal = "SELECT COUNT(*) as total FROM messages";
            $totalStmt = $mysqli->prepare($queryTotal);

            if ($totalStmt->execute()) {
                $totalResult = $totalStmt->get_result();
                $totalCountRow = $totalResult->fetch_assoc();
                $response['totalMessages'] = $totalCountRow['total'];
            } else {
                // Handle error
                $current_date = date('Y-m-d H:i:s');
                $error = array("success" => false, "message" => $mysqli->error, 'date' => $current_date);
                file_put_contents($file, json_encode($error), FILE_APPEND);
            }


            break;
        }
    }
}

header('Content-Type: application/json');
// Преобразование массива в JSON
$json_response = json_encode($response);

echo $json_response; // Возвращение JSON-ответа
