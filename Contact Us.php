<?php
require_once 'db.php';
// Fetch notices for marquee
$marqueeSql = "SELECT title FROM notices 
               ORDER BY published_at DESC, created_at DESC 
               LIMIT 5";
$marqueeResult = $conn->query($marqueeSql);
$marqueeNotices = [];
if ($marqueeResult && $marqueeResult->num_rows > 0) {
    while ($row = $marqueeResult->fetch_assoc()) {
        $marqueeNotices[] = htmlspecialchars($row['title']);
    }
}
if (empty($marqueeNotices)) {
    $marqueeNotices = ['Welcome to SCTI - Sindhuli Community Technical Institute'];
}
$marqueeText = implode(' | ', $marqueeNotices);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCTI - Contact </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    :root {
        --form-blue: #3498db;
        --form-bg: #f4f4f4;
        --text-dark: #333;
        --border-color: #ccc;
        --error-color: #e74c3c;
        --success-color: #2ecc71;
    }

    .contact-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .contact-container h1 {
        text-align: center;
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .contact-container p {
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--form-blue);
    }

    .validation-message {
        color: var(--error-color);
        font-size: 0.9rem;
        margin-top: 0.5rem;
        display: none;
    }

    .form-group.success input {
        border-color: var(--success-color);
    }

    .form-group.error input {
        border-color: var(--error-color);
    }

    .submit-btn {
        display: block;
        width: 100%;
        padding: 1rem;
        background-color: #2c3e50;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 1.2rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .submit-btn:hover {
        background-color: #3498db;
    }

    @media (max-width: 600px) {
        .contact-container {
            margin: 1rem;
            padding: 1.5rem;
        }
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    .section {
        padding: 40px 0;
    }

    .bg-grey {
        background: #f2f2f2;
    }

    /* ===== TOP HEADER ===== */
    .top-header {
        background: #00264d;
        color: white;
        padding: 8px;
    }

    /* ===== HEADER ===== */
    .header {
        background: #004080;
    }

    .header-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .logo img {
        width: 120px;
    }

    /* ===== LOGO CIRCLE FIX ===== */
    .logo img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
    }

    .menu ul {
        list-style: none;
        display: flex;
    }

    .menu ul li {
        margin-left: 20px;
    }

    .menu ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    .menu-toggle {
        display: none;
        font-size: 26px;
        color: white;
        cursor: pointer;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .menu-toggle {
            display: block;
        }

        .menu {
            display: none;
            width: 100%;
        }

        .menu ul {
            flex-direction: column;
            background: #004080;
            text-align: center;
        }

        .menu ul li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .menu.show {
            display: block;
        }
    }

    .container {
        width: 90%;
        margin: auto;
    }

    /* ===== SECTION ===== */
    .section {
        padding: 40px 0;
    }

    .section h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #004080;
    }

    /* ===== NOTICE BOARD ===== */
    .notice-list {
        background: #ffffff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .notice-list ul {
        list-style: none;
    }

    .notice-list ul li {
        padding: 12px 0;
        border-bottom: 1px solid #ddd;
    }

    .notice-list ul li:last-child {
        border-bottom: none;
    }

    /* ===== FOOTER ===== */
    .footer {
        background: #00264d;
        color: #fff;
        text-align: center;
        padding: 15px 0;
        margin-top: 40px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .menu ul {
            flex-direction: column;
            background: #ffffff;
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .menu ul li {
            margin: 0;
            border-bottom: 1px solid #eee;
        }

        .menu ul li a {
            display: block;
            padding: 12px;
        }

        .menu-toggle {
            display: block;
        }

        .menu.show ul {
            display: block;
        }
    }
</style>
</head>

<body>

    <div class="top-header">
        <marquee>
            <?php echo $marqueeText; ?>
        </marquee>
    </div>

    <!-- ===== HEADER & MENU ===== -->
    <header class="header">
        <div class="container header-flex">

            <div class="logo">
                <img src="scti logo.jpeg" alt="SCTI Logo">
            </div>

            <div class="menu-toggle" id="menu-toggle">
                <i class="fa fa-bars"></i>
            </div>

            <nav class="menu" id="menu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="Programs.php">Programs</a></li>
                    <li><a href="Gallery.php">Gallery</a></li>
                    <li><a href="Notice Board.php">Notice Board</a></li>
                    <li><a href="Contact Us.php">Contact Us</a></li>
                </ul>
            </nav>

        </div>
    </header>

    <main class="contact-container">
        <h1>Contact Us</h1>
        <p>We would love to hear from you! Please fill out the form below to get in touch with our team.</p>
        <form id="contact-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <div class="validation-message" id="name-error">Please enter a valid name.</div>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <div class="validation-message" id="email-error">Please enter a valid email address.</div>
            </div>

            <div class="form-group">
                <label for="number">Phone Number:</label>
                <input type="tel" id="number" name="number" required>
                <div class="validation-message" id="number-error">Please enter a valid phone number.</div>
            </div>

            <div class="form-group">
                <label for="message">Your Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
                <div class="validation-message" id="message-error">Please enter your message.</div>
            </div>

            <button type="submit" class="submit-btn">Send Message</button>
        </form>

        <div id="form-message" style="display: none; margin-top: 20px; padding: 15px; border-radius: 5px; text-align: center;"></div>

    </main>

    <script>
        document.getElementById('contact-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.submit-btn');
            const messageDiv = document.getElementById('form-message');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
            messageDiv.style.display = 'none';
            
            try {
                const response = await fetch('contact_handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                messageDiv.style.display = 'block';
                if (result.success) {
                    messageDiv.style.backgroundColor = '#d4edda';
                    messageDiv.style.color = '#155724';
                    messageDiv.style.border = '1px solid #c3e6cb';
                    messageDiv.textContent = result.message;
                    this.reset();
                } else {
                    messageDiv.style.backgroundColor = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    messageDiv.style.border = '1px solid #f5c6cb';
                    messageDiv.textContent = result.message;
                }
            } catch (error) {
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#f8d7da';
                messageDiv.style.color = '#721c24';
                messageDiv.style.border = '1px solid #f5c6cb';
                messageDiv.textContent = 'An error occurred. Please try again.';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message';
            }
        });
    </script>

</body>

</html>
