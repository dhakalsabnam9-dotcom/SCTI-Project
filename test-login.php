<?php
// Test if PHP is working
echo "<!DOCTYPE html>";
echo "<html><head><title>PHP Test</title></head><body>";
echo "<h1>PHP is Working!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<hr>";
echo "<h2>Testing Login System</h2>";

// Test session
session_start();
echo "<p>✓ Session started successfully</p>";

// Test form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<p style='color: green;'>✓ Form submitted successfully!</p>";
    echo "<p>Username: " . htmlspecialchars($_POST['username']) . "</p>";
    echo "<p>User Type: " . htmlspecialchars($_POST['userType']) . "</p>";
} else {
    echo "<p>No form submitted yet. Fill the form below:</p>";
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    .form-box {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    input, select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    button {
        width: 100%;
        padding: 12px;
        background: #004080;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background: #003060;
    }
    label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
    }
</style>

<div class="form-box">
    <h3>Test Login Form</h3>
    <form method="POST" action="test-login.php">
        <label>User Type:</label>
        <select name="userType" required>
            <option value="">Select Type</option>
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select>

        <label>Username:</label>
        <input type="text" name="username" placeholder="Enter username" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <button type="submit">Test Login</button>
    </form>
    
    <hr>
    <p><strong>Test Credentials:</strong></p>
    <ul>
        <li>Admin: admin / admin123</li>
        <li>Teacher: teacher / teacher123</li>
        <li>Student: student / student123</li>
    </ul>
</div>

</body>
</html>
