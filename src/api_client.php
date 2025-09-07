<?php

/**
 * Makes a request to the Google Gemini API.
 *
 * @param string $prompt The prompt to send to the model.
 * @param string $apiKey The Google Gemini API key.
 * @return array An array with 'data' on success or 'error' on failure.
 */
function callGeminiAPI(string $prompt, string $apiKey): array
{
    $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

    $requestData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

    $response = curl_exec($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        return ['error' => "cURL Error: " . $curl_err];
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        return ['error' => "Gemini API Error: " . $data['error']['message']];
    }

    return ['data' => $data];
}

/**
 * Makes a request to the Unsplash API to search for photos.
 *
 * @param string $topic The topic to search for.
 * @param string $apiKey The Unsplash API key.
 * @return array An array with 'data' on success or 'error' on failure.
 */
function callUnsplashAPI(string $topic, string $apiKey): array
{
    $apiUrl = "https://api.unsplash.com/search/photos?query=" . urlencode($topic) . "&per_page=3&client_id=" . $apiKey;

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        return ['error' => "cURL Error: " . $curl_err];
    }

    $data = json_decode($response, true);
    if (isset($data['errors'])) {
        return ['error' => "Unsplash API Error: " . implode(', ', $data['errors'])];
    }

    return ['data' => $data];
}

/**
 * Converts simple markdown to HTML.
 *
 * @param string $text The text to convert.
 * @return string The converted HTML.
 */
function simpleMarkdownToHtml(string $text): string
{
    // Convert headings (e.g., # Heading 1, ## Heading 2)
    $text = preg_replace('/^###### (.*)$/m', '<h6>$1</h6>', $text);
    $text = preg_replace('/^##### (.*)$/m', '<h5>$1</h5>', $text);
    $text = preg_replace('/^#### (.*)$/m', '<h4>$1</h4>', $text);
    $text = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $text);

    // Convert unordered lists (e.g., - Item)
    $text = preg_replace('/^- (.*)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);

    // Convert **text** to <strong>text</strong>
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    // Convert *text* to <em>text</em>
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);

    return $text;
}