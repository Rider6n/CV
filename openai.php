<?php
// Get the user's input from the POST request
$input = $_POST['input'];

// OpenAI API key - DO NOT EXPOSE THIS IN CLIENT-SIDE CODE
$apiKey = 'YOUR_OPENAI_API_KEY';

// Initialize cURL
$ch = curl_init('https://api.openai.com/v1/chat/completions');

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $input]],
]));

// Execute cURL request and capture the response
$response = curl_exec($ch);

// Close cURL
curl_close($ch);

// Return the response to the client
echo $response;
?>