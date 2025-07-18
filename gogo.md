Comprehensive Development Prompt for Training Catalog Web Application
Objective
Develop a web application for managing an electronic catalog of continuous training courses (seminars) as outlined in the project specification (Projet25_DW_LPTI2_PHP.pdf). The application must be completed within one day, strictly adhering to the requirements to achieve a perfect score (20/20). The development must follow a 5-layer architecture (Presentation, Business Logic, Data Access, Data Storage, and Utilities), with each layer having a single responsibility. Every implementation step must be tested thoroughly to minimize errors, and best practices in PHP, HTML, CSS, JavaScript, and MySQL must be applied. JavaScript functions must be grouped in external files, as specified.

Project Requirements (from Specification)
1. Functional Requirements

Application Purpose: A web application to manage a catalog of continuous training courses (seminars).
Training Course Characteristics:
Domain: e.g., Informatique, Management, etc.
Title (Intitulé): The name of the training course.
Date: The date when the training course takes place (noted as repeated in the OCR, likely a typo; assume a single date field unless clarified).


Features (inferred from typical catalog management systems, as the OCR is incomplete):
Display a list of training courses.
Allow users to add, edit, and delete training courses.
Filter or search courses by domain, title, or date.
Ensure user-friendly navigation and responsive design.


JavaScript: All JavaScript functions must be grouped in external files (e.g., scripts.js).

2. Technical Requirements

Technologies:
Backend: PHP for server-side logic.
Frontend: HTML, CSS (use a framework like Bootstrap for responsiveness), and JavaScript (external files).
Database: MySQL to store training course data.


Architecture: Implement a 5-layer architecture:
Presentation Layer: User interface (HTML, CSS, JavaScript).
Business Logic Layer: PHP logic for processing requests and business rules.
Data Access Layer: PHP classes to interact with the database (CRUD operations).
Data Storage Layer: MySQL database with a table for training courses.
Utilities Layer: Helper functions (e.g., validation, formatting) in PHP and JavaScript.


Database Schema (inferred from requirements):
Table: trainings
id (Primary Key, Auto-Increment)
domain (VARCHAR, e.g., "Informatique", "Management")
title (VARCHAR, course title)
date (DATE, training date)




Best Practices:
Use prepared statements for database queries to prevent SQL injection.
Validate and sanitize all user inputs.
Follow MVC-like principles within the 5-layer architecture.
Ensure responsive design using Bootstrap or similar.
Organize code in a modular, maintainable way (e.g., separate files for each layer).
Use version control (e.g., Git) for tracking changes during development.


Testing:
Test each implementation step (e.g., database connection, CRUD operations, UI rendering).
Use tools like PHPUnit for PHP unit tests and browser developer tools for JavaScript.
Ensure no errors or warnings in the console or server logs.



3. Deliverables

Technical Report: A document explaining the architecture, technologies used, and implementation details.
Source Code: All PHP, HTML, CSS, JavaScript, and SQL files, well-organized.
Database Script: SQL script to create and initialize the database (trainings.sql).
Presentation Video: A 3-minute video (.mp4) demonstrating the application’s functionality.

4. Submission

Deadline: Sunday, July 20, 2025, 23:59.
Submission: Upload to a Google Drive folder named LTI225_PHP_NOM1_Prenom1_NOM2_Prenom2 (replace with actual names) and share with:
imoreira@email.com
affdafa@@mail.com (likely a typo; verify or exclude if invalid)
bambaboffile@mail.com
rodevemant@ (incomplete; verify or exclude if invalid).


Video: 3-minute .mp4 file showcasing the application.

5. Evaluation

The project presentation (video) accounts for 20% of the grade.
The remaining 80% is based on functionality, code quality, and adherence to requirements.


Development Plan
To complete the application in one day, follow this structured, step-by-step plan. Each step must be implemented, tested, and verified before proceeding to the next. If a step fails, debug and fix it before continuing.
Step 1: Project Setup (30 minutes)

Objective: Set up the development environment and project structure.
Tasks:
Create a project directory: training-catalog.
Initialize a Git repository: git init.
Create subdirectories:
public/ (for frontend files: HTML, CSS, JS).
src/ (for PHP backend: business logic, data access, utilities).
docs/ (for technical report).
sql/ (for database scripts).


Install dependencies:
Ensure PHP and MySQL are installed.
Download Bootstrap CSS/JS via CDN for responsive design.


Create initial files:
public/index.html (main page).
public/css/styles.css (custom styles).
public/js/scripts.js (external JavaScript).
src/config.php (database configuration).
src/models/Training.php (data access layer).
src/controllers/TrainingController.php (business logic).
src/utils/helpers.php (utility functions).
sql/trainings.sql (database schema).




Testing:
Verify the project directory structure.
Ensure PHP server runs (php -S localhost:8000 -t public).
Check that index.html loads in the browser.



Step 2: Database Setup (30 minutes)

Objective: Create and test the MySQL database.
Tasks:
Write sql/trainings.sql:
Create database: training_catalog.
Create table: trainings with columns:
id (INT, AUTO_INCREMENT, PRIMARY KEY).
domain (VARCHAR(100)).
title (VARCHAR(255)).
date (DATE).


Insert 2-3 sample records for testing.


Configure database connection in src/config.php:
Use PDO with credentials (host, dbname, user, password).
Enable error handling and prepared statements.


Test the connection in a temporary PHP script.


Testing:
Run trainings.sql in MySQL to create the database and table.
Verify the connection using a test script (src/test_db.php).
Query the sample data to ensure it’s accessible.



Step 3: Data Access Layer (1 hour)

Objective: Implement CRUD operations in the Data Access Layer.
Tasks:
In src/models/Training.php, create a Training class with methods:
getAll(): Retrieve all trainings.
getById($id): Retrieve a single training by ID.
create($domain, $title, $date): Insert a new training.
update($id, $domain, $title, $date): Update an existing training.
delete($id): Delete a training.


Use PDO with prepared statements for all queries.
Handle exceptions and errors gracefully.


Testing:
Write a test script (src/test_training_model.php) to test each method.
Verify getAll() returns sample data.
Test create, update, and delete with sample inputs.
Check for SQL injection safety using invalid inputs.



Step 4: Business Logic Layer (1 hour)

Objective: Implement the controller to handle requests.
Tasks:
In src/controllers/TrainingController.php, create a TrainingController class:
Methods to handle HTTP requests (GET, POST, PUT, DELETE).
Validate inputs (e.g., ensure date is a valid date, domain and title are non-empty).
Call appropriate Training model methods.


Create API endpoints (e.g., public/api/trainings.php):
GET /api/trainings: List all trainings.
GET /api/trainings/:id: Get a single training.
POST /api/trainings: Create a new training.
PUT /api/trainings/:id: Update a training.
DELETE /api/trainings/:id: Delete a training.




Testing:
Use Postman or curl to test each endpoint.
Verify JSON responses are correct.
Test error cases (e.g., invalid ID, missing fields).



Step 5: Presentation Layer (2 hours)

Objective: Build a responsive frontend using HTML, CSS, and JavaScript.
Tasks:
In public/index.html:
Create a layout with Bootstrap (navbar, table for trainings, forms for adding/editing).
Display a table of trainings with columns: ID, Domain, Title, Date, Actions (Edit, Delete).
Include a form to add new trainings.
Add a modal for editing trainings.


In public/css/styles.css:
Add custom styles for branding and layout tweaks.


In public/js/scripts.js:
Write JavaScript functions to:
Fetch and display trainings (GET /api/trainings).
Handle form submission for adding trainings (POST /api/trainings).
Handle edit and delete actions (PUT/DELETE /api/trainings/:id).
Validate inputs client-side (e.g., non-empty fields, valid date).


Use fetch API for AJAX requests.




Testing:
Test the UI in multiple browsers (Chrome, Firefox).
Verify responsiveness on mobile and desktop.
Test form validation (e.g., empty fields, invalid dates).
Ensure JavaScript functions in scripts.js work without errors.



Step 6: Utilities Layer (30 minutes)

Objective: Implement helper functions for validation and formatting.
Tasks:
In src/utils/helpers.php:
Add functions for server-side validation (e.g., sanitizeInput, validateDate).
Add formatting functions (e.g., formatDate for consistent date display).


In public/js/scripts.js:
Add client-side validation functions (e.g., check for non-empty fields).




Testing:
Test validation functions with valid and invalid inputs.
Verify formatted outputs (e.g., dates in YYYY-MM-DD).



Step 7: Integration and End-to-End Testing (1 hour)

Objective: Ensure all layers work together seamlessly.
Tasks:
Test the full application workflow:
Add a new training via the form.
Verify it appears in the table.
Edit and delete the training.
Test filtering/searching (if implemented).


Check for edge cases (e.g., duplicate titles, invalid dates).


Testing:
Use browser developer tools to check for JavaScript errors.
Monitor PHP error logs.
Test with multiple users (simulate concurrent requests if possible).



Step 8: Documentation (1 hour)

Objective: Write the technical report and prepare deliverables.
Tasks:
In docs/technical_report.md:
Describe the 5-layer architecture.
List technologies used (PHP, MySQL, Bootstrap, JavaScript).
Explain key implementation details (e.g., CRUD operations, validation).
Include screenshots of the application.


Export technical_report.md to PDF if required.
Ensure all source code is organized and commented.
Verify sql/trainings.sql is complete.


Testing:
Review the report for completeness and clarity.
Ensure all files are in the correct directories.



Step 9: Video Presentation (1 hour)

Objective: Create a 3-minute video demonstrating the application.
Tasks:
Record a screencast showing:
Navigating the application.
Adding, editing, and deleting a training.
Displaying the course list.


Keep the video under 3 minutes.
Export as .mp4.


Testing:
Watch the video to ensure clarity and completeness.
Verify the file format and duration.



Step 10: Final Submission (30 minutes)

Objective: Package and submit deliverables.
Tasks:
Create a Google Drive folder: LTI225_PHP_NOM1_Prenom1_NOM2_Prenom2.
Upload:
training-catalog/ (all source code).
docs/technical_report.pdf.
sql/trainings.sql.
presentation.mp4.


Share with the specified emails (verify correct addresses).


Testing:
Confirm all files are uploaded and accessible.
Test sharing permissions with a test email if possible.




Best Practices to Follow

Code Organization:
Use meaningful variable/function names.
Comment code to explain functionality.
Keep each layer’s files separate (e.g., models, controllers).


Security:
Use prepared statements for all database queries.
Sanitize and validate all user inputs.
Implement CSRF protection for forms if time allows.


Performance:
Optimize database queries (e.g., index the trainings table on id).
Minimize HTTP requests (e.g., use CDN for Bootstrap).


Testing:
Test incrementally after each step.
Use PHPUnit for backend tests and browser tools for frontend.
Log all test results to ensure no regressions.


Version Control:
Commit after each step with descriptive messages (e.g., “Implemented Data Access Layer”).
Use branches if experimenting with features.




Success Criteria

The application meets all functional requirements (CRUD operations, course display).
The 5-layer architecture is strictly followed, with clear separation of concerns.
All code is tested, error-free, and follows best practices.
Deliverables (report, code, SQL script, video) are complete and submitted on time.
The presentation video is clear, concise, and under 3 minutes.
The application is responsive and user-friendly.


Notes for Cursor

Development Speed: Prioritize completing each step within the allocated time to meet the one-day deadline.
Testing Rigor: After each step, run tests to confirm functionality. If errors occur, debug immediately before proceeding.
Error Handling: Include try-catch blocks in PHP and error messages in JavaScript to handle failures gracefully.
Fallbacks: If a feature (e.g., filtering) cannot be implemented in time, ensure core CRUD operations are perfect.
Verification: Double-check the specification (e.g., repeated “date” fields may be an OCR error; assume one date unless clarified).
Deliverables: Ensure all required files are generated and organized as specified.

By following this prompt, the application should meet all requirements, be thoroughly tested, and be ready for submission by July 20, 2025, 23:59. Good luck!