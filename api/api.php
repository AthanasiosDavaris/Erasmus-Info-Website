<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(200);
  exit();
}

require_once '../php/db_config.php';

try {
  $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
  $pdo = new PDO($db_info, $db_user, $db_password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
  exit();
}

$request_uri = explode('/', trim($_GET['request'] ?? '', '/'));
$resource = array_shift($request_uri);
$id = array_shift($request_uri);

if ($resource !== 'universities') {
  http_response_code(404);
  echo json_encode(['message' => 'Endpoint not found.']);
  exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    if ($id) {
      getUniversity($pdo, $id);
    } else {
      getAllUniversities($pdo);
    }
    break;
  case 'POST':
    createUniversity($pdo);
    break;
  case 'PUT':
    if ($id) {
      updateUniversity($pdo, $id);
    } else {
      http_response_code(400);
      echo json_encode(['message' => 'Bad Request: University ID is required for update.']);
    }
    break;
  case 'DELETE':
    if ($id) {
      deleteUniversity($pdo, $id);
    } else {
      http_response_code(400);
      echo json_encode(['message' => 'Bad Request: University ID is required for deletion.']);
    }
    break;
  default:
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
    break;
}

function getAllUniversities($pdo) {
  $statement = $pdo->query("SELECT * FROM universities ORDER BY university_name ASC");
  $universities = $statement->fetchAll();
  http_response_code(200);
  echo json_encode($universities);
}

function getUniversity($pdo, $id) {
  $statement = $pdo->prepare("SELECT * FROM universities WHERE university_id = ?");
  $statement->execute([$id]);
  $university = $statement->fetch();

  if ($university) {
    http_response_code(200);
    echo json_encode($university);
  } else {
    http_response_code(404);
    echo json_encode(['message' => 'University not found.']);
  }
}

function createUniversity($pdo) {
  $data = json_decode(file_get_contents("php://input"));

  if (empty($data->university_name) || empty($data->country)) {
    http_response_code(400);
    echo json_encode(['message' => 'Unable to create university. Data is incomplete.']);
    return;
  }

  try {
    $statement = $pdo->prepare("INSERT INTO universities (university_name, country) VALUES (?, ?)");
    $statement->execute([$data->university_name, $data->country]);
    $new_id = $pdo->lastInstertId();

    http_response_code(201);
    echo json_encode([
      'message' => 'University created successfully.',
      'university_id' => $new_id,
      'university_name' => $data->university_name,
      'country' => $data->country
    ]);
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Unable to create university. Possible duplicate entry.']);
  }
}

function updateUniversity($pdo, $id) {
  $data = json_decode(file_get_contents("php://input"));

  if (empty($data->university_name) || empty(data->country)) {
    http_response_code(400);
    echo json_encode(['message' => 'Unable to update university. Data is incomplete.']);
    return;
  }

  $statement = $pdo->prepare("UPDATE universities SET university_name = ?, country = ? WHERE university_id = ?");
  $statement->execute([$data->university_name, $data->country, $id]);

  if ($statement->rowCount() > 0) {
    http_response_code(200);
    echo json_encode(['message' => 'University updated successfully.']);
  } else {
    http_response_code(404);
    echo json_encode(['message' => 'University not found or data is the same.']);
  }
}

function deleteUniversity($pdo, $id) {
  $statement = $pdo->prepare("DELETE FROM universities WHERE university_id = ?");
  $statement->execute([$id]);

  if ($statement->rowCount() > 0) {
    http_response_code(204);
  } else {
    http_response_code(404);
    echo json_encode(['message' => 'University not found.']);
  }
}
?>