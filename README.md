# AI Study Buddy

AI Study Buddy is a dynamic web application designed to enhance learning through AI-powered content generation. It provides on-demand explanations, interactive quizzes, and code snippets, leveraging the capabilities of Google's Gemini API and Unsplash API.

## Features

*   **Explanation Generator:** Get comprehensive explanations on any topic, structured with main content, FAQs, and a summary. Optionally includes relevant images.
*   **Quiz Generator:** Generate customizable multiple-choice quizzes with instant feedback, a running scoreboard, and pagination.
*   **Code Generator:** Obtain code snippets in various programming languages with syntax highlighting and easy copy functionality.
*   **User Management:** Simple user identification and personalized experience.
*   **Consistent Navigation:** A responsive navigation bar for seamless browsing.

## Setup

To get the AI Study Buddy up and running on your local machine, follow these steps:

1.  **Clone the Repository:**

    ```bash
    git clone https://https://github.com/your-username/study-buddy.git
    cd study-buddy
    ```
    *(Note: Replace `https://github.com/your-username/study-buddy.git` with the actual repository URL if different.)*

2.  **Configure API Keys:**
    The application requires API keys for Google Gemini and Unsplash.

    *   **Google Gemini API Key:**
        *   Go to [Google AI Studio](https://makersuite.google.com/keys).
        *   Create a new API key.
        *   Open `config.php` in the project's root directory.
        *   Replace `'YOUR_GEMINI_API_KEY'` with your actual Gemini API key:
            ```php
            define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY');
            ```

    *   **Unsplash API Key (Optional, for image feature):**
        *   Go to [Unsplash Developers](https://unsplash.com/developers).
        *   Create an account or log in.
        *   Create a new application to get your **Access Key**.
        *   Open `config.php` in the project's root directory.
        *   Replace `'YOUR_UNSPLASH_API_KEY'` with your actual Unsplash Access Key:
            ```php
            define('UNSPLASH_API_KEY', 'YOUR_UNSPLASH_API_KEY');
            ```

3.  **Run the Application:**

    *   **Prerequisites:**
        *   **Web Server:** A web server with PHP support (e.g., Apache, Nginx). XAMPP or MAMP are good options for local development.
        *   **PHP:** PHP 7.4 or higher.
        *   **cURL Extension:** Ensure the cURL extension is enabled in your PHP installation.

    *   **Steps:**
        1.  Place the `study-buddy` project folder in your web server's document root (e.g., `htdocs` for Apache/XAMPP).
        2.  Start your web server.
        3.  Open your web browser and navigate to the URL where your project is hosted (e.g., `http://localhost/study-buddy`).

## Technologies Used

*   PHP
*   HTML, CSS, JavaScript
*   Bootstrap 5
*   Google Gemini API
*   Unsplash API
*   Highlight.js
