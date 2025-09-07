<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Start the session at the very beginning

require_once '../src/api_client.php';
require_once '../config.php'; // Include config.php

$userName = $_GET['name'] ?? 'Guest';

// Determine if it's a GET request (initial load or subject/level selection) or POST (quiz submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $proficiencyLevel = $_POST['level'] ?? '';
    // Retrieve quizData from session for POST requests
    $quizData = $_SESSION['current_quiz_data'] ?? [];

    echo "POST received. Variables assigned. No further processing for debugging.";
    exit;

} else {
    $subject = $_GET['subject'] ?? '';
    $proficiencyLevel = $_GET['level'] ?? '';
    $numQuestions = isset($_GET['num_questions']) ? (int)$_GET['num_questions'] : 10;
    if ($numQuestions < 5) $numQuestions = 5;
    if ($numQuestions > 50) $numQuestions = 50;
    // Clear previous quiz data from session if a new quiz is being started
    unset($_SESSION['current_quiz_data']);
    $quizData = [];
}

$error = '';
$showQuizForm = false;

if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY') {
    $error = "Gemini API Key is not set. Please set it in config.php.";
} else {
    $geminiApiKey = GEMINI_API_KEY; // Use the API key from config.php

    if (empty($subject) || empty($proficiencyLevel)) {
        // Show the form to select subject and proficiency
        $showQuizForm = false; // This will display the input form
    } else {
        $showQuizForm = true; // This will display the quiz questions

        // Only call API if quizData is not already in session (i.e., it's a new quiz generation)
        if (empty($quizData)) {
            // Prompt for Gemini API to generate a quiz
            $prompt = <<<EOT
Generate a {$numQuestions}-question multiple-choice quiz about {$subject} for {$proficiencyLevel} level. Each question should have 4 options (A, B, C, D) and indicate the correct answer. Provide the output in a JSON array format like this: {"questions": [{"question": "...", "options": ["A. ...", "B. ...", "C. ...", "D. ..."], "answer": "A. ..."}]}
EOT;
            
            $apiResponse = callGeminiAPI($prompt, $geminiApiKey);

            if (isset($apiResponse['error'])) {
                $error = "Error fetching quiz: " . $apiResponse['error'];
            } elseif (isset($apiResponse['data']['candidates'][0]['content']['parts'][0]['text'])) {
                $quizJson = $apiResponse['data']['candidates'][0]['content']['parts'][0]['text'];
                // Clean up the JSON string: remove markdown code block fences if present
                $quizJson = str_replace('```json', '', $quizJson);
                $quizJson = str_replace('```', '', $quizJson);
                // Attempt to fix common JSON errors from the API
                $quizJson = preg_replace('/}\s*{\s*/', '}}, {', $quizJson);
                $quizData = json_decode($quizJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $error = "Error decoding quiz data: " . json_last_error_msg() . ". Raw data: " . htmlspecialchars($quizJson);
                    $quizData = []; // Ensure quizData is empty on error
                } elseif (!isset($quizData['questions']) || !is_array($quizData['questions'])) {
                    $error = "Invalid quiz data format from API. Expected 'questions' array. Raw data: " . htmlspecialchars($quizJson);
                    $quizData = []; // Ensure quizData is empty on error
                }
                // Store quizData in session after successful API call
                $_SESSION['current_quiz_data'] = $quizData;
            } else {
                $error = "No quiz data received from API.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - AI Study Buddy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../src/style.css">
</head>
<body class="bg-light">
    <?php require_once 'navbar.php'; ?>
    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h2>Quiz</h2>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php elseif (!$showQuizForm): ?>
                    <form method="GET" action="quiz.php">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($userName); ?>">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Enter Subject:</label>
                            <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g., History" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">Select Proficiency Level:</label>
                            <select id="level" name="level" class="form-select" required>
                                <option value="">-- Select Level --</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="num_questions" class="form-label">Number of Questions (5-50):</label>
                            <input type="number" id="num_questions" name="num_questions" class="form-control" min="5" max="50" value="10" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Start Quiz</button>
                    </form>
                <?php else: // Show the quiz questions ?>
                    <div id="scoreboard" class="mb-3"></div>
                    <form id="quizForm">
                        <?php if (!empty($quizData['questions'])): ?>
                            <?php foreach ($quizData['questions'] as $index => $question): ?>
                                <div class="mb-4 question-page" data-correct-answer="<?php echo htmlspecialchars($question['answer']); ?>" style="display: none;">
                                    <p><strong><?php echo ($index + 1) . ". " . htmlspecialchars($question['question']); ?></strong></p>
                                    <?php foreach ($question['options'] as $optionIndex => $option): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="question_<?php echo $index; ?>" id="question_<?php echo $index; ?>_option_<?php echo $optionIndex; ?>" value="<?php echo htmlspecialchars($option); ?>" required>
                                            <label class="form-check-label" for="question_<?php echo $index; ?>_option_<?php echo $optionIndex; ?>">
                                                <?php echo htmlspecialchars($option); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="feedback mt-2"></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No quiz questions available. Please try again later.</p>
                        <?php endif; ?>
                    </form>
                    <div id="pagination-controls" class="d-flex justify-content-between mt-4">
                        <button id="prev-btn" class="btn btn-secondary">Previous</button>
                        <button id="next-btn" class="btn btn-primary">Next</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Final Score Modal -->
    <div class="modal fade" id="finalScoreModal" tabindex="-1" aria-labelledby="finalScoreModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="finalScoreModalLabel">Quiz Complete!</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="finalScoreModalBody">
            <!-- Final score will be inserted here -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <a href="quiz.php?name=<?php echo urlencode($userName); ?>" class="btn btn-primary">Retest</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quizForm = document.getElementById('quizForm');
            const scoreboard = document.getElementById('scoreboard');
            const finalScoreModal = new bootstrap.Modal(document.getElementById('finalScoreModal'));
            const finalScoreModalBody = document.getElementById('finalScoreModalBody');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');

            if (quizForm) {
                const questions = Array.from(quizForm.querySelectorAll('[data-correct-answer]'));
                const totalQuestions = questions.length;
                const questionsPerPage = 10;
                const totalPages = Math.ceil(totalQuestions / questionsPerPage);
                let currentPage = 1;
                let correctAnswers = 0;
                let questionsAnswered = 0;

                function showPage(page) {
                    questions.forEach((question, index) => {
                        const pageIndex = Math.floor(index / questionsPerPage) + 1;
                        question.style.display = pageIndex === page ? 'block' : 'none';
                    });

                    prevBtn.disabled = page === 1;
                    nextBtn.disabled = page === totalPages;
                }

                function updateScoreboard() {
                    scoreboard.innerHTML = `<strong>Score: ${correctAnswers} / ${totalQuestions}</strong>`;
                }

                updateScoreboard();
                showPage(currentPage);

                prevBtn.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        showPage(currentPage);
                    }
                });

                quizForm.addEventListener('change', function(event) {
                    if (event.target.type === 'radio') {
                        const questionContainer = event.target.closest('[data-correct-answer]');
                        const correctAnswer = questionContainer.dataset.correctAnswer;
                        const selectedAnswer = event.target.value;
                        const feedbackDiv = questionContainer.querySelector('.feedback');
                        
                        const radios = questionContainer.querySelectorAll('input[type="radio"]');
                        radios.forEach(radio => {
                            if (!radio.checked) {
                                radio.disabled = true;
                            }
                        });

                        questionsAnswered++;

                        if (selectedAnswer.trim() === correctAnswer.trim()) {
                            correctAnswers++;
                            feedbackDiv.innerHTML = '<span class="text-success">Correct!</span>';
                        } else {
                            feedbackDiv.innerHTML = '<span class="text-danger">Incorrect! The correct answer is: ' + correctAnswer + '</span>';
                        }

                        updateScoreboard();

                        if (questionsAnswered === totalQuestions) {
                            const percentage = (correctAnswers / totalQuestions) * 100;
                            let finalMessage = `<p>Your final score is: ${correctAnswers} / ${totalQuestions}</p>`;

                            if (percentage > 90) {
                                finalMessage += `<p class="text-success">Congratulations! You are a master of this topic!</p>`;
                            }

                            finalScoreModalBody.innerHTML = finalMessage;
                            finalScoreModal.show();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>