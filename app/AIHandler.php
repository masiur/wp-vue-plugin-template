<?php

namespace WPVuePlugin;

class AIHandler {

    /**
     * Handle the AI request via cURL.
     */
    public static function handle_request() {
        // Check if the request is valid
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
            $user_message = sanitize_text_field($_POST['message']); // Sanitize the user input

            // Set up the cURL request to the Gemini AI server
            $ch = curl_init();

            // Replace with the correct Google Generative Language API URL
            // Set up the API endpoint and key
            // Get API key from environment variables
            $api_key = $_ENV['GEMINI_API_KEY'] ?: ''; // Default to empty string if not set

            if (empty($api_key)) {
                http_response_code(500);
                echo json_encode(['error' => 'API key is not set in the environment variables.']);
                return;
            }

            $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $api_key;

            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);



            // Set up the JSON payload for the request
            $payload = json_encode([
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $user_message]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 1,
                    'topK' => 64,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'text/plain'
                ]
            ]);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            // Execute the cURL request and get the response
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get the HTTP response code

            // Check for cURL errors
            if (curl_errno($ch)) {
                $error_message = curl_error($ch);
                curl_close($ch);

                // Log the error for debugging
                error_log('cURL error: ' . $error_message);

                // Return error response
                http_response_code(500);
                echo json_encode(['error' => 'Failed to communicate with AI server: ' . $error_message]);
                exit;
            }

            curl_close($ch);

            if ($http_code === 200) {
                header('Content-Type: application/json'); // Set the content type to JSON
                echo $response; // Return the AI server response
            } else {
                // Log the HTTP error for debugging
                error_log('HTTP error: ' . $http_code . ' - Response: ' . $response);

                // Return error response
                http_response_code(500);
                echo json_encode(['error' => 'Failed to communicate with AI server. HTTP Code: ' . $http_code]);
            }

            exit;
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request.']);
            exit;
        }
    }
}
