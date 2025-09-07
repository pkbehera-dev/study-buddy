<?php
// Ensure the $userName variable is available, otherwise default to 'Guest'
$userName = $_GET['name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Buddy - Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../src/style.css">
</head>
<body class="bg-light">
    <?php require_once 'navbar.php'; ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1>AI Study Buddy - Detailed Documentation</h1>
                <p>This document provides a comprehensive overview of the AI Study Buddy web application, detailing its features, implementation, and setup instructions.</p>

                <h2>1. Project Overview</h2>
                <p>AI Study Buddy is a web-based tool designed to assist students and learners by providing on-demand explanations of topics, generating interactive quizzes, and creating code snippets. It leverages Google's Gemini API for AI-powered content generation and Unsplash API for relevant imagery.</p>

                <h2>2. Features Implemented</h2>

                <h3>2.1. Explanation Generator (<code>pages/explanation.php</code>)</h3>
                <p>This feature allows users to get detailed explanations on any topic.</p>
                <ul>
                    <li><strong>AI-Powered Explanations:</strong> Utilizes the Google Gemini API to generate explanations.</li>
                    <li><strong>Dynamic Content Generation:</strong> The explanation is structured into three distinct sections:
                        <ul>
                            <li><strong>Main Explanation:</strong> Approximately 700-900 words, providing a comprehensive overview of the topic.</li>
                            <li><strong>Frequently Asked Questions (FAQ):</strong> Includes 10 small questions and answers related to the topic.</li>
                            <li><strong>Summary/In-short:</strong> A concise summary of the topic, within 150 words.</li>
                        </ul>
                    </li>
                    <li><strong>Markdown Formatting:</strong> The generated explanation is formatted using Markdown (headings, bold, italics, lists) for better readability, which is then converted to HTML for display.</li>
                    <li><strong>Image Integration (Optional):</strong> Users can choose to include relevant images with their explanation.
                        <ul>
                            <li><strong>Unsplash API Integration:</strong> Searches Unsplash for images related to the topic.</li>
                            <li><strong>API Key Requirement:</strong> Requires a free Unsplash API key to function.</li>
                            <li><strong>Toggle Button:</strong> A toggle switch allows users to enable or disable image inclusion.</li>
                        </ul>
                    </li>
                </ul>

                <h3>2.2. Quiz Generator (<code>pages/quiz.php</code>)</h3>
                <p>This feature allows users to test their knowledge with interactive quizzes.</p>
                <ul>
                    <li><strong>AI-Powered Quiz Generation:</strong> Utilizes the Google Gemini API to generate multiple-choice quizzes.</li>
                    <li><strong>Configurable Questions:</strong> Users can specify the number of questions they want in the quiz (between 5 and 50).</li>
                    <li><strong>Instant Feedback:</strong> Provides immediate feedback (correct/incorrect) after each answer selection.</li>
                    <li><strong>Scoreboard:</strong>
                        <ul>
                            <li><strong>Running Score:</strong> Displays the current score (correct answers / total questions) at the top of the quiz.</li>
                            <li><strong>Final Score Modal:</strong> Upon completion of all questions, an animated modal appears, showing the final score.</li>
                            <li><strong>Performance Message:</strong> Displays a congratulatory message if the user scores more than 90%.</li>
                            <li><strong>Retest Option:</strong> The modal includes a "Retest" button to quickly start a new quiz.</li>
                        </ul>
                    </li>
                    <li><strong>Pagination:</strong> Questions are displayed in pages (10 questions per page) with "Previous" and "Next" navigation buttons.</li>
                </ul>

                <h3>2.3. Code Generator (<code>pages/code.php</code>)</h3>
                <p>This feature allows users to generate code snippets for various programming languages.</p>
                <ul>
                    <li><strong>AI-Powered Code Generation:</strong> Utilizes the Google Gemini API to generate code based on user queries.</li>
                    <li><strong>Language Selection:</strong> Supports a range of popular programming languages (Python, JavaScript, PHP, Java, C#, C++, C).</li>
                    <li><strong>Code Highlighting:</strong> Generated code is displayed with syntax highlighting for improved readability.</li>
                    <li><strong>Copy to Clipboard:</strong> A convenient button allows users to easily copy the generated code.</li>
                </ul>

                <h3>2.4. User Management</h3>
                <ul>
                    <li><strong>User Identification:</strong> Users enter their name and age upon first access.</li>
                    <li><strong>Session/Local Storage:</strong> User's name is stored to personalize the experience across pages.</li>
                </ul>

                <h3>2.5. Navigation Bar (<code>pages/navbar.php</code>)</h3>
                <ul>
                    <li><strong>Consistent Navigation:</strong> A responsive navigation bar is displayed on all main application pages (Dashboard, Explanation, Quiz, Code).</li>
                    <li><strong>User Greeting:</strong> Displays a personalized greeting "HII {user}" on the left side.</li>
                    <li><strong>Home Button:</strong> A "Home" button on the right side redirects to the Dashboard.</li>
                </ul>

                <h2>3. Technical Details</h2>
                <ul>
                    <li><strong>Backend:</strong> PHP</li>
                    <li><strong>Frontend:</strong> HTML, CSS, JavaScript</li>
                    <li><strong>Styling Framework:</strong> Bootstrap 5</li>
                    <li><strong>API Communication:</strong> cURL for making HTTP requests to external APIs.</li>
                    <li><strong>AI Integration:</strong> Google Gemini API (for explanations and quizzes).</li>
                    <li><strong>Image Integration:</strong> Unsplash API (for related images in explanations).</li>
                    <li><strong>Code Highlighting:</strong> Highlight.js library.</li>
                </ul>

                <h2>4. Setup Instructions</h2>
                <p>To set up and run the AI Study Buddy application on your local machine, follow these steps:</p>

                <h3>4.1. Prerequisites</h3>
                <ul>
                    <li><strong>Web Server:</strong> A web server with PHP support (e.g., Apache, Nginx). XAMPP or MAMP are good options for local development.</li>
                    <li><strong>PHP:</strong> PHP 7.4 or higher.</li>
                    <li><strong>cURL Extension:</strong> Ensure the cURL extension is enabled in your PHP installation.</li>
                </ul>

                <h3>4.2. Clone the Repository</h3>
                <pre><code>git clone https://github.com/your-username/study-buddy.git
cd study-buddy</code></pre>
                <p><em>(Note: Replace <code>https://github.com/your-username/study-buddy.git</code> with the actual repository URL if different.)</em></p>

                <h3>4.3. Configure API Keys</h3>
                <p>The application relies on API keys for Google Gemini and Unsplash.</p>
                <ol>
                    <li><strong>Google Gemini API Key:</strong>
                        <ul>
                            <li>Go to <a href="https://makersuite.google.com/keys">Google AI Studio</a>.</li>
                            <li>Create a new API key.</li>
                            <li>Open <code>config.php</code> in the project's root directory.</li>
                            <li>Replace <code>'YOUR_GEMINI_API_KEY'</code> with your actual Gemini API key:
                                <pre><code>define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY');</code></pre>
                            </li>
                        </ul>
                    </li>
                    <li><strong>Unsplash API Key (Optional, for image feature):</strong>
                        <ul>
                            <li>Go to <a href="https://unsplash.com/developers">Unsplash Developers</a>.</li>
                            <li>Create an account or log in.</li>
                            <li>Create a new application to get your <strong>Access Key</strong>.</li>
                            <li>Open <code>config.php</code> in the project's root directory.</li>
                            <li>Replace <code>'YOUR_UNSPLASH_API_KEY'</code> with your actual Unsplash Access Key:
                                <pre><code>define('UNSPLASH_API_KEY', 'YOUR_UNSPLASH_API_KEY');</code></pre>
                            </li>
                        </ul>
                    </li>
                </ol>

                <h3>4.4. Run the Application</h3>
                <ol>
                    <li>Place the <code>study-buddy</code> project folder in your web server's document root (e.g., <code>htdocs</code> for Apache/XAMPP).</li>
                    <li>Start your web server.</li>
                    <li>Open your web browser and navigate to the URL where your project is hosted (e.g., <code>http://localhost/study-buddy</code>).</li>
                </ol>

                <h2>5. Future Enhancements (Optional)</h2>
                <ul>
                    <li>User authentication and persistent profiles.</li>
                    <li>Saving quiz results and explanation history.</li>
                    <li>More advanced markdown rendering (e.g., tables, code blocks).</li>
                    <li>Integration with other AI models or services.</li>
                    <li>Improved UI/UX.</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>