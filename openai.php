<?php
require 'vendor/autoload.php';

// Load the .env file if it exists
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Get the OpenAI API key from the environment variable
$apiKey = $_ENV['OPENAI_API_KEY'];

// Get the user's input from the POST request
$input = $_POST['input'];

// Initialize cURL
$ch = curl_init('https://api.openai.com/v1/chat/completions');

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . 'Bearer ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $input]],
]));

// Execute cURL request and capture the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Return the response to the client
    echo $response;
}

// Close cURL
curl_close($ch);
?>
