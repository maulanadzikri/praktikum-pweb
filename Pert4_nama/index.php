<?php

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); 
header('Access-Control-Allow-Headers: Content-Type'); 

// Include database connection file
include_once 'db.php'; 

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getTasks();
        break;

    case 'POST':
        createTask();
        break;

    case 'PUT':
        completeTask();
        break;

    case 'DELETE':
        deleteTask();
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

function getTasks() {
    $conn = getConnection(); 

    // Check if an ID is provided in the query parameters
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM todos WHERE id = ?"); 
        $stmt->bind_param("i", $id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM todos"); 
    }

    $stmt->execute(); 
    $result = $stmt->get_result();

    // if there are results, fetch them and return as JSON
    if ($result->num_rows > 0) {
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row; 
        }
        echo json_encode($tasks); 
    } else {
        echo json_encode(['message' => 'No Task Found']); 
    }
}

// Function to create a new task in the database
function createTask() {
    $data = json_decode(file_get_contents("php://input")); 
    if (!empty($data->task)){
        $conn = getConnection(); 
        $stmt = $conn->prepare("INSERT INTO todos (task) VALUES (?  )"); 
        $stmt->bind_param("s", $data->task); 

        if ($stmt->execute()) { 
            echo json_encode(["message" => "Task created successfully"]); 
        } else {
            echo json_encode(["message" => "Failed to create task"]); 
        }

        $stmt->close(); 
        $conn->close(); 
    } else {
        echo json_encode(["message" => "Task content is empty"]); 
    }
}


function completeTask() {
    $data = json_decode(file_get_contents("php://input")); 
    if (!empty($data->id)){
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE todos SET completed = 1 WHERE id = ?"); 
        $stmt->bind_param("i", $data->id); 

        if ($stmt->execute()) { 
            echo json_encode(["message" => "Task Completed"]); 
        } else {
            echo json_encode(["message" => "Task Not Completed"]); 
        }

        $stmt->close(); 
        $conn->close();
    } else {
        echo json_encode(["message" => "Task ID is missing"]);    
    }
}

function deleteTask() {
    $data = json_decode(file_get_contents("php://input")); 

    if (!empty($data->id)){
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?"); 
        $stmt->bind_param("i", $data->id); 

        if ($stmt->execute()) { 
            echo json_encode(["message" => "Task Deleted"]); 
        } else {
            echo json_encode(["message" => "Task Not Deleted"]); 
        }

        $stmt->close(); 
        $conn->close();
    } else {
        echo json_encode(["message" => "Task ID is missing"]);   
    }
}