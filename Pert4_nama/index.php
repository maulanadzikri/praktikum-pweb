<?php

// Set CORS headers
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Content-Type: application/json'); // Set content type to JSON
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Allowed HTTP methods
header('Access-Control-Allow-Headers: Content-Type'); // Allowed headers

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
        // Handle unsupported methods
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

// Function to get tasks from the database
function getTasks() {
    $conn = getConnection(); // Use the function from db.php to get the database connection
    
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
            $tasks[] = $row; // Add each row to the tasks array
        }
        echo json_encode($tasks); // Return the tasks as JSON
    } else {
        echo json_encode(['message' => 'No Task Found']); // Return an empty array if no tasks found
    }
}

// Function to create a new task in the database
function createTask() {
    $data = json_decode(file_get_contents("php://input")); // Get the JSON input and decode it
    if (!empty($data->task)){
        $conn = getConnection(); // Get the database connection
        $stmt = $conn->prepare("INSERT INTO todos (task) VALUES (?  )"); // Prepare the SQL statement
        $stmt->bind_param("s", $data->task); // Bind the task parameter

        if ($stmt->execute()) { // Execute the statement
            echo json_encode(["message" => "Task created successfully"]); // Success response
        } else {
            echo json_encode(["message" => "Failed to create task"]); // Error response
        }

        $stmt->close(); // Close the statement
        $conn->close(); 
    } else {
        echo json_encode(["message" => "Task content is empty"]); // Error response for empty task
    }
}


function completeTask() {
    $data = json_decode(file_get_contents("php://input")); // Get the JSON input and decode it

    if (!empty($data->id)){
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE todos SET completed = 1 WHERE id = ?"); // Prepare the SQL statement
        $stmt->bind_param("i", $data->id); // Bind the id parameter

        if ($stmt->execute()) { // Execute the statement
            echo json_encode(["message" => "Task Completed"]); // Success response
        } else {
            echo json_encode(["message" => "Task Not Completed"]); // Error response
        }

        $stmt->close(); 
        $conn->close();
    } else {
        echo json_encode(["message" => "Task ID is missing"]); // Error response for missing id   
    }
}

function deleteTask() {
    $data = json_decode(file_get_contents("php://input")); // Get the JSON input and decode it

    if (!empty($data->id)){
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?"); // Prepare the SQL statement
        $stmt->bind_param("i", $data->id); 

        if ($stmt->execute()) { // Execute the statement
            echo json_encode(["message" => "Task Deleted"]); // Success response
        } else {
            echo json_encode(["message" => "Task Not Deleted"]); // Error response
        }

        $stmt->close(); 
        $conn->close();
    } else {
        echo json_encode(["message" => "Task ID is missing"]); // Error response for missing id   
    }
}