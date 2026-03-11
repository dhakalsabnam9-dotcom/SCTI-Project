# Requirements Document: School Website Documentation

## Introduction

This document specifies the requirements for the Sindhuli Community Technical Institute (SCTI) website. The website serves as the primary digital presence for the institute, providing information about academic programs, campus facilities, events, and contact details. It also enables user authentication for students, teachers, and administrators to access personalized dashboards and services.

## Glossary

- **System**: The SCTI website application (client-side and server-side components)
- **User**: Any person accessing the website (visitor, student, teacher, or administrator)
- **Visitor**: An unauthenticated user browsing public content
- **Authenticated_User**: A user who has successfully logged in (student, teacher, or admin)
- **SPA**: Single-Page Application architecture where content updates without full page reloads
- **Content_Area**: The dynamic section of the page where content is injected
- **Navigation_Menu**: The menu system allowing users to switch between pages
- **Backend**: The PHP server-side components handling authentication and data persistence
- **Session**: Server-side storage of user authentication state
- **Form_Handler**: JavaScript function that processes form submissions via AJAX
- **Page_Module**: JavaScript file containing HTML content for a specific page
- **Password_Strength**: Measure of password security based on complexity rules
- **Dashboard**: Personalized page displayed after successful authentication

## Requirements

### Requirement 1: Single-Page Application Navigation

**User Story:** As a visitor, I want to navigate between different pages without full page reloads, so that I can browse the website quickly and smoothly.

#### Acceptance Criteria

1. WHEN a user clicks a navigation link, THE System SHALL load the requested page content without reloading the entire page
2. WHEN page content is loaded, THE System SHALL scroll to the top of the page smoothly
3. WHEN a user navigates to a page, THE System SHALL highlight the active menu item
4. WHEN the application first loads, THE System SHALL display the home page by default
5. WHEN page content changes, THE System SHALL preserve the header, navigation menu, and footer elements

### Requirement 2: Responsive Navigation Menu

**User Story:** As a mobile user, I want a responsive navigation menu, so that I can easily access all pages on my mobile device.

#### Acceptance Criteria

1. WHEN a user views the website on a mobile device, THE System SHALL display a menu toggle button
2. WHEN a user clicks the menu toggle button, THE System SHALL show or hide the navigation menu
3. WHEN a user selects a page from the mobile menu, THE System SHALL close the menu automatically
4. WHEN the viewport width is above mobile breakpoint, THE System SHALL display the full navigation menu without requiring a toggle

### Requirement 3: Public Information Pages

**User Story:** As a visitor, I want to view information about the institute, so that I can learn about programs, facilities, and events.

#### Acceptance Criteria

1. THE System SHALL provide a home page displaying banner, notices preview, events preview, campus information, courses, and teacher profiles
2. THE System SHALL provide a programs page displaying academic program listings with expandable details
3. THE System SHALL provide a gallery page displaying campus photos in a responsive grid layout
4. THE System SHALL provide a notices page displaying announcements with dates and urgency indicators
5. WHEN a user views the gallery, THE System SHALL display hover effects revealing image captions

### Requirement 4: Contact Form Submission

**User Story:** As a visitor, I want to submit inquiries through a contact form, so that I can communicate with the institute.

#### Acceptance Criteria

1. THE System SHALL provide a contact form with fields for name, email, phone, subject, and message
2. WHEN a user submits the contact form, THE System SHALL validate that all fields are filled
3. WHEN a user submits the contact form, THE System SHALL validate the email address format
4. WHEN a user submits the contact form, THE System SHALL validate the phone number format
5. WHEN the contact form is valid, THE System SHALL send the data to the backend without page reload
6. WHEN the backend processes the contact form, THE System SHALL store the message in the database with status 'new' and timestamp
7. WHEN the contact form submission succeeds, THE System SHALL display a success message and clear the form
8. IF the contact form submission fails, THEN THE System SHALL display an error message without clearing the form

### Requirement 5: User Authentication (Login)

**User Story:** As a student, teacher, or administrator, I want to log in to the system, so that I can access my personalized dashboard.

#### Acceptance Criteria

1. THE System SHALL provide a login form with fields for user type, username, and password
2. THE System SHALL support three user types: student, teacher, and admin
3. WHEN a user submits the login form, THE System SHALL validate that all fields are filled
4. WHEN the login form is valid, THE System SHALL send credentials to the backend without page reload
5. WHEN the backend receives login credentials, THE System SHALL query the appropriate database table based on user type
6. WHEN the backend finds a matching username, THE System SHALL verify the password using secure password verification
7. WHEN authentication succeeds, THE System SHALL create a session with user_id, username, user_type, full_name, email, and logged_in status
8. WHEN authentication succeeds, THE System SHALL redirect the user to the appropriate dashboard based on user type
9. IF authentication fails, THEN THE System SHALL display an error message without redirecting
10. THE System SHALL provide a password visibility toggle allowing users to show or hide password characters

### Requirement 6: User Registration (Signup)

**User Story:** As a new user, I want to create an account, so that I can access the system's authenticated features.

#### Acceptance Criteria

1. THE System SHALL provide a signup form with fields for user type, full name, username, email, password, and confirm password
2. WHEN a user submits the signup form, THE System SHALL validate that all fields are filled
3. WHEN a user submits the signup form, THE System SHALL validate the email address format
4. WHEN a user submits the signup form, THE System SHALL validate that password and confirm password match
5. WHEN the signup form is valid, THE System SHALL send the data to the backend without page reload
6. WHEN the backend receives signup data, THE System SHALL check if the username already exists
7. WHEN the backend receives signup data, THE System SHALL check if the email is already registered
8. IF the username or email already exists, THEN THE System SHALL return an error message
9. WHEN username and email are unique, THE System SHALL hash the password using secure password hashing
10. WHEN the password is hashed, THE System SHALL insert the new user record into the appropriate database table
11. WHEN user creation succeeds, THE System SHALL create a session and redirect to the appropriate dashboard
12. IF user creation fails, THEN THE System SHALL display an error message

### Requirement 7: Password Strength Validation

**User Story:** As a new user creating an account, I want real-time feedback on my password strength, so that I can create a secure password.

#### Acceptance Criteria

1. WHEN a user types in the password field on the signup page, THE System SHALL validate the password in real-time
2. THE System SHALL require passwords to be between 7 and 12 characters in length
3. THE System SHALL require passwords to contain at least one uppercase letter
4. THE System SHALL require passwords to contain at least one lowercase letter
5. THE System SHALL require passwords to contain at least one number
6. THE System SHALL require passwords to contain at least one special character (!@#$%^&*)
7. WHEN a password requirement is met, THE System SHALL display a visual indicator (checkmark) next to that requirement
8. WHEN a password requirement is not met, THE System SHALL display a visual indicator (cross) next to that requirement
9. WHEN 0-2 password requirements are met, THE System SHALL display a "Weak" strength indicator with red color at 33% width
10. WHEN 3-4 password requirements are met, THE System SHALL display a "Medium" strength indicator with yellow color at 66% width
11. WHEN all 5 password requirements are met, THE System SHALL display a "Strong" strength indicator with green color at 100% width

### Requirement 8: Password Confirmation Validation

**User Story:** As a new user creating an account, I want to confirm my password correctly, so that I don't accidentally create an account with a mistyped password.

#### Acceptance Criteria

1. WHEN a user types in the confirm password field, THE System SHALL compare it to the password field in real-time
2. WHEN the passwords match, THE System SHALL display a visual indicator showing they match
3. WHEN the passwords do not match, THE System SHALL display a visual indicator showing they do not match
4. WHEN the signup form is submitted with non-matching passwords, THE System SHALL prevent submission and display an error message

### Requirement 9: Secure Password Storage

**User Story:** As a system administrator, I want user passwords to be stored securely, so that user accounts are protected from unauthorized access.

#### Acceptance Criteria

1. WHEN a user creates an account, THE System SHALL hash the password using bcrypt algorithm before storage
2. THE System SHALL never store passwords in plain text
3. WHEN a user logs in, THE System SHALL verify the password using secure password verification against the stored hash
4. THE System SHALL use prepared statements with bound parameters for all database queries involving passwords

### Requirement 10: Session Management

**User Story:** As an authenticated user, I want my login session to persist across page navigations, so that I don't have to log in repeatedly.

#### Acceptance Criteria

1. WHEN a user successfully authenticates, THE System SHALL create a server-side session
2. THE System SHALL store user_id, username, user_type, full_name, email, and logged_in status in the session
3. WHILE a user has an active session, THE System SHALL maintain authentication state across page requests
4. WHEN a user accesses a protected resource, THE System SHALL validate the session before granting access

### Requirement 11: Form Validation and Error Handling

**User Story:** As a user submitting forms, I want clear validation feedback, so that I can correct errors and successfully submit my information.

#### Acceptance Criteria

1. WHEN a user submits a form with missing required fields, THE System SHALL display an error message indicating which fields are required
2. WHEN a user submits a form with invalid email format, THE System SHALL display an error message about email format
3. WHEN a user submits a form with invalid phone format, THE System SHALL display an error message about phone format
4. WHEN form validation fails, THE System SHALL preserve the user's input so they can correct errors
5. WHEN a form is being submitted, THE System SHALL display a loading indicator
6. WHEN a form submission completes, THE System SHALL remove the loading indicator
7. IF a network error occurs during form submission, THEN THE System SHALL display an error message and allow retry

### Requirement 12: Database Integration

**User Story:** As a system administrator, I want all user data and submissions to be stored in a database, so that information is persisted and can be retrieved later.

#### Acceptance Criteria

1. THE System SHALL store student records in a students table with fields for id, username, password, full_name, email, and timestamps
2. THE System SHALL store teacher records in a teachers table with fields for id, username, password, full_name, email, and timestamps
3. THE System SHALL store admin records in an admins table with fields for id, username, password, full_name, email, and timestamps
4. THE System SHALL store contact form submissions in a contacts table with fields for id, name, email, phone, subject, message, status, and created_at
5. WHEN a contact form is submitted, THE System SHALL set the status field to 'new' by default
6. WHEN a contact form is submitted, THE System SHALL automatically set the created_at timestamp
7. THE System SHALL use prepared statements for all database queries to prevent SQL injection

### Requirement 13: Input Sanitization

**User Story:** As a system administrator, I want all user inputs to be sanitized, so that the system is protected from malicious input and XSS attacks.

#### Acceptance Criteria

1. WHEN the backend receives user input, THE System SHALL trim whitespace from all text fields
2. WHEN the backend receives user input, THE System SHALL validate email addresses using email format validation
3. WHEN the backend receives user input, THE System SHALL validate phone numbers using phone format validation
4. WHEN the backend stores user input in the database, THE System SHALL use parameterized queries to prevent SQL injection
5. THE System SHALL validate that all required fields are present before processing

### Requirement 14: Responsive Design

**User Story:** As a user on any device, I want the website to display properly on my screen size, so that I can access all features regardless of device.

#### Acceptance Criteria

1. WHEN the viewport width is 768px or below, THE System SHALL apply tablet-specific styles
2. WHEN the viewport width is 480px or below, THE System SHALL apply mobile-specific styles
3. WHEN images are displayed, THE System SHALL scale them appropriately for the viewport size
4. WHEN forms are displayed on mobile devices, THE System SHALL ensure all fields are easily accessible and usable
5. WHEN the gallery is displayed, THE System SHALL adjust the number of columns based on screen size

### Requirement 15: AJAX Communication

**User Story:** As a user interacting with forms, I want seamless communication with the server, so that I can submit data without page reloads.

#### Acceptance Criteria

1. WHEN a form is submitted, THE System SHALL prevent the default form submission behavior
2. WHEN a form is submitted, THE System SHALL send data to the backend using AJAX (Fetch API)
3. WHEN the backend processes a request, THE System SHALL return a response in JSON or HTML format
4. WHEN the contact form backend responds, THE System SHALL return JSON with success and message fields
5. WHEN the login or signup backend responds, THE System SHALL return HTML containing success or error messages
6. WHEN an AJAX request completes, THE System SHALL parse the response and update the UI accordingly
7. IF an AJAX request fails, THEN THE System SHALL catch the error and display an error message to the user

### Requirement 16: Dashboard Redirection

**User Story:** As an authenticated user, I want to be redirected to my appropriate dashboard after login, so that I can access features relevant to my role.

#### Acceptance Criteria

1. WHEN a student successfully logs in, THE System SHALL redirect to the student dashboard
2. WHEN a teacher successfully logs in, THE System SHALL redirect to the teacher dashboard
3. WHEN an admin successfully logs in, THE System SHALL redirect to the admin dashboard
4. WHEN a new user successfully signs up, THE System SHALL redirect to the appropriate dashboard based on their user type

### Requirement 17: Browser Compatibility

**User Story:** As a user with any modern browser, I want the website to function correctly, so that I can access all features regardless of my browser choice.

#### Acceptance Criteria

1. THE System SHALL function correctly in the latest version of Google Chrome
2. THE System SHALL function correctly in the latest version of Mozilla Firefox
3. THE System SHALL function correctly in the latest version of Apple Safari
4. THE System SHALL function correctly in the latest version of Microsoft Edge
5. THE System SHALL use JavaScript features supported by modern browsers (ES6+, Fetch API)

### Requirement 18: Static Asset Management

**User Story:** As a user, I want the website to load quickly, so that I can access information without long wait times.

#### Acceptance Criteria

1. THE System SHALL serve static assets (HTML, CSS, JavaScript, images) that can be cached by the browser
2. THE System SHALL load all JavaScript modules in the correct dependency order
3. THE System SHALL load Font Awesome icons from a CDN for optimal performance
4. WHEN the application initializes, THE System SHALL load all page modules upfront for instant navigation

### Requirement 19: Security Best Practices

**User Story:** As a system administrator, I want the application to follow security best practices, so that user data and the system are protected from common vulnerabilities.

#### Acceptance Criteria

1. THE System SHALL use HTTPS for all communications (deployment requirement)
2. THE System SHALL implement CSRF protection by validating same-origin requests
3. THE System SHALL never expose sensitive information in client-side code
4. THE System SHALL validate all inputs on both client-side and server-side
5. THE System SHALL use secure session management with server-side session storage
6. THE System SHALL implement proper error handling without exposing system details to users

### Requirement 20: Modular Code Architecture

**User Story:** As a developer maintaining the system, I want the code to be modular and well-organized, so that I can easily understand, modify, and extend functionality.

#### Acceptance Criteria

1. THE System SHALL organize page content into separate JavaScript module files
2. THE System SHALL maintain a central page registry mapping page names to content
3. THE System SHALL separate concerns by having distinct files for menu functionality, page content, page registry, and application logic
4. THE System SHALL load JavaScript modules in the correct dependency order to ensure proper initialization
5. THE System SHALL use consistent naming conventions for files, functions, and variables
