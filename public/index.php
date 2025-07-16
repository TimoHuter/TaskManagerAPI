<?php
    header("Access-Control-Allow-Origin: http://localhost:5174");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];
    $path = parse_url($requestUri, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $searchParams = parse_url($requestUri, PHP_URL_QUERY);
    if ($searchParams) {
        $searchParams = explode('&', $searchParams);
    }

    header('Content-Type: application/json');

    require_once __DIR__ . '/' . '../src/task_api.php';
    
    //GetTasks/AddTask
    //http://localhost:8888/index.php/users/1/tasks
    //Else
    //http://localhost:8888/index.php/users/1/tasks/1
    if (count($segments) >= 4 && $segments[1] === 'users' && is_numeric($segments[2]) && $segments[3] === 'tasks') {
        //standardmäßig 1 -> kein nutzermanagement
        $userId = intval($segments[2]);
        $taskId = -1;
        //$search[] = [];

        if (count($segments) === 5 && is_numeric($segments[4])) {
            $taskId = intval($segments[4]);
        }

        /*
        if ($searchParams) {
            foreach ($searchParams as $param) {
                $params = explode('=', $param);
                if ($params[1] !== '') {
                    $search[] .= $params;
                } else {
                    //leer => (url?param=)
                }
            }
        }
        */

        switch ($method) {
            case 'GET':
                if ($taskId !== -1) {
                    $temp = getTask($connection, $taskId);
                    if ($temp !== null) {
                        echo json_encode($temp);
                    } else {
                        echo json_encode();
                    }
                } else {
                    $temp = getTasks($connection);
                    if ($temp !== null) {
                        echo json_encode($temp);
                    } else {
                        echo json_encode([]);
                    }
                }

                break;

            case 'POST':
                $data = readBody();
                $completed = addTask($connection, $data);

                if ($completed) {
                    echo json_encode(['message' => 'task created', 'received' => true, 'content' => $data, 'completed' => true]);
                } else {
                    echo json_encode(['message' => 'error', 'received' => true, 'content' => $data, 'completed' => false]);
                }
                
                break;

            case 'DELETE':
                if ($taskId !== -1) {
                    if (deleteTask($connection, $taskId)) {
                        echo json_encode(['message' => 'task deleted']);
                    } else {
                        echo json_encode(['error' => 'error']);
                    }
                } else {
                    http_response_code(418);
                    echo json_encode(['error' => 'I\'m a teapot']);
                }

                break;

            case 'PUT':
                if ($taskId !== -1) {
                    $data = readBody();
                    $completed = editTask($connection, $data, $taskId);

                    if ($completed) {
                        echo json_encode(['message' => 'task created', 'received' => true, 'content' => $data, 'completed' => true]);
                    } else {
                        echo json_encode(['error' => 'error', 'received' => true, 'content' => $data, 'completed' => false]);
                    }
                } else {
                    http_response_code(418);
                    echo json_encode(['error' => 'I\'m a teapot']);
                }

                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);

                break;
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

    exit;

    function readBody(): array {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true); //true = assoziatives Array

        return $data;
    }
?>