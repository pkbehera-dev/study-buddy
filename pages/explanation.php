<?php
// --- BACKEND LOGIC ---
$explanation = '';
$topic = '';
$error = '';
$userName = $_GET['name'] ?? '';
$imageUrls = [];

// If name is not in the URL, redirect to the main menu.
// This is the entry point to the page.
if (empty($userName) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// When the form is submitted, the name comes from a hidden POST field.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config.php';
    require_once '../src/api_client.php';
    
    $userName = $_POST['name'] ?? '';
    $topic = $_POST['topic'] ?? '';
    $explanationLength = "700-900 words";
    $faqCount = 10;
    $summaryLength = 150;


    if (empty($userName)) {
        // This should not happen if the form is working correctly.
        // Redirecting is better than showing a broken page.
        header('Location: index.php');
        exit;
    }

    if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY') {
        $error = "API key not configured. Please add your Gemini API key to config.php.";
    } elseif (empty($topic)) {
        $error = "Please enter a topic.";
    } else {
        // --- Google Gemini API Call ---
        $geminiApiKey = GEMINI_API_KEY;
        $prompt = "You are an AI Study Buddy. Your user's name is $userName. Explain the topic '$topic' in a very simple and easy-to-understand way, as if you were explaining it to a complete beginner. Your explanation should be approximately {$explanationLength}. Include a section for Frequently Asked Questions (FAQ) with {$faqCount} small questions and a Summary section within {$summaryLength} words. Use simple words and analogies. Please format the explanation using markdown, including headings, bold and italic text, and lists where appropriate.";

        $result = callGeminiAPI($prompt, $geminiApiKey);

        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $data = $result['data'];
            if (empty($data['candidates'])) {
                $error = "The request was blocked for safety reasons: " . ($data['promptFeedback']['blockReason'] ?? 'Unknown');
            } else {
                $explanation = $data['candidates'][0]['content']['parts'][0]['text'] ?? "No explanation generated.";
                $explanation = simpleMarkdownToHtml($explanation);

                // --- Unsplash API Call ---
                if (isset($_POST['include_images']) && defined('UNSPLASH_API_KEY') && UNSPLASH_API_KEY !== 'YOUR_UNSPLASH_API_KEY') {
                    $unsplashApiKey = UNSPLASH_API_KEY;
                    $unsplashResult = callUnsplashAPI($topic, $unsplashApiKey);
                    if (isset($unsplashResult['data']['results'])) {
                        foreach ($unsplashResult['data']['results'] as $image) {
                            $imageUrls[] = $image['urls']['small'];
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Study Buddy - Explanation</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body class="bg-light">
    <?php require_once 'navbar.php'; ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h1 class="mt-3">Explanation Generator</h1>
                <p class="lead">What topic can I explain for you?</p>

                <div class="card mt-4">
                    <div class="card-body">
                        <form method="POST" action="explanation.php?name=<?php echo urlencode($userName); ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($userName); ?>">
                            <div class="mb-3">
                                <label for="topic" class="form-label">Topic:</label>
                                <input
                                    type="text"
                                    id="topic"
                                    name="topic"
                                    class="form-control"
                                    placeholder="e.g., 'Black Holes'"
                                    value="<?php echo htmlspecialchars($topic); ?>"
                                    autocomplete="off"
                                    required
                                />
                            </div>
                            <div class="form-check form-switch mb-3">
                              <input class="form-check-input" type="checkbox" role="switch" id="include_images" name="include_images" checked>
                              <label class="form-check-label" for="include_images">Include Images</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Explanation</button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($error) || !empty($explanation)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        Result
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)):
                            // Using htmlspecialchars to prevent XSS attacks
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php else:
                            // Using nl2br to convert newlines to <br> tags for display
                        ?>
                            <p class="card-text"><?php echo nl2br($explanation); ?></p>
                            <?php if (!empty($imageUrls)) : ?>
                                <div class="mt-4">
                                    <h5>Related Images:</h5>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($imageUrls as $url) : ?>
                                            <img src="<?php echo htmlspecialchars($url); ?>" class="img-thumbnail m-2" style="width: 150px; height: 150px; object-fit: cover;">
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        document.getElementById('word_count').addEventListener('change', function () {
            if (this.value === 'custom') {
                document.getElementById('custom_word_count_container').style.display = 'block';
            } else {
                document.getElementById('custom_word_count_container').style.display = 'none';
            }
        });
    </script>
  </body>
</html>