<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Study Buddy - Code Generator</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Highlight.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <?php 
    $userName = $_GET['name'] ?? 'Guest';
    require_once 'navbar.php'; 
    ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card mt-3">
                    <div class="card-header text-center">
                        <h1>Code Generator</h1>
                    </div>
                    <div class="card-body">
                        <form action="code.php" method="post">
                            <div class="mb-3">
                                <label for="language" class="form-label">Select Language:</label>
                                <select class="form-select" id="language" name="language">
                                    <option value="python">Python</option>
                                    <option value="javascript">JavaScript</option>
                                    <option value="php">PHP</option>
                                    <option value="java">Java</option>
                                    <option value="csharp">C#</option>
                                    <option value="cpp">C++</option>
                                    <option value="c">C</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="query" class="form-label">What code do you want to generate?</label>
                                <textarea class="form-control" id="query" name="query" rows="3" placeholder="e.g., 'Hello world', 'check for prime number'"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Code</button>
                        </form>
                    </div>
                </div>

                <?php
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                require_once '../config.php'; // Include the API key
                require_once '../src/api_client.php';

                if (isset($_POST['query']) && isset($_POST['language'])) {
                    $query = $_POST['query'];
                    $language = $_POST['language'];
                    $code = '';
                    $error = '';

                    if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY') {
                        $error = "API key not configured. Please add your Gemini API key to config.php.";
                    } else {
                        $apiKey = GEMINI_API_KEY;

                        // Construct the prompt for code generation
                        $prompt = "Generate a direct, procedural script in " . htmlspecialchars($language) . " for the following request: " . htmlspecialchars($query) . ". Include necessary input/output operations if applicable. Do NOT use function definitions, classes, docstrings, or 'if __name__ == \"__main__\":' blocks. Provide ONLY the raw code, without any additional explanations or conversational text.";

                        $result = callGeminiAPI($prompt, $apiKey);

                        if (isset($result['error'])) {
                            $error = $result['error'];
                        } else {
                            $data = $result['data'];
                            if (empty($data['candidates'])) {
                                $error = "The request was blocked for safety reasons: " . ($data['promptFeedback']['blockReason'] ?? 'Unknown');
                            } else {
                                $code = $data['candidates'][0]['content']['parts'][0]['text'] ?? "No code generated.";

                                // Remove Markdown code block delimiters
                                $code = preg_replace('/^```[a-zA-Z]*\n|\n```$/', '', $code);
                            }
                        }
                    }

                    echo '<div class="card mt-4">';
                    echo '<div class="card-header">Generated Code for: ' . htmlspecialchars($query) . '</div>';
                    echo '<div class="card-body">';
                    if (!empty($error)) {
                        echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error) . '</div>';
                    } else {
                        echo '<div style="position: relative;">'; // Container for positioning
                        echo '<button id="copy-button" class="btn btn-link text-white" style="position: absolute; top: 5px; right: 5px; z-index: 10;"><i class="bi bi-clipboard"></i></button>';
                        // Store the raw code in a data attribute for JavaScript to pick up
                        echo '<pre id="code-block" class="language-' . htmlspecialchars($language) . '" data-code="' . htmlspecialchars($code) . '"><code class="language-' . htmlspecialchars($language) . '"></code></pre>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Highlight.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/java.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/csharp.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/cpp.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const codeBlockPre = document.getElementById('code-block');
            const copyButton = document.getElementById('copy-button');

            if (codeBlockPre) {
                const codeElement = codeBlockPre.querySelector('code');
                const rawCode = codeBlockPre.getAttribute('data-code');
                if (codeElement && rawCode !== null) {
                    codeElement.textContent = rawCode;
                    hljs.highlightElement(codeElement);
                }
            }

            if (copyButton) {
                copyButton.addEventListener('click', async () => {
                    const codeElement = document.querySelector('#code-block code');
                    if (codeElement) {
                        try {
                            await navigator.clipboard.writeText(codeElement.textContent);
                            const originalIconClass = 'bi-clipboard';
                            const copiedIconClass = 'bi-check';
                            const iconElement = copyButton.querySelector('i');

                            if (iconElement) {
                                iconElement.classList.remove(originalIconClass);
                                iconElement.classList.add(copiedIconClass);
                            }

                            setTimeout(() => {
                                if (iconElement) {
                                    iconElement.classList.remove(copiedIconClass);
                                    iconElement.classList.add(originalIconClass);
                                }
                            }, 2000);
                        } catch (err) {
                            console.error('Failed to copy: ', err);
                            alert('Failed to copy code. Please copy manually.');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>