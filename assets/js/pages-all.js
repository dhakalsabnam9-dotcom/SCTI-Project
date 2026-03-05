// Menu toggle for mobile
document.getElementById("menu-toggle").onclick = function () {
  document.getElementById("menu").classList.toggle("show");
};

// Page content templates
const pages = {
  home: `
    <!-- ===== BANNER ===== -->
    <div class="banner">
      <img src="assets/images/banner.png" alt="SCTI Banner">
    </div>

    <!-- ===== NOTICE & EVENTS ===== -->
    <section class="section">
      <section class="updates">
        <div class="update-card notice">
          <h3><i class="fa fa-bullhorn"></i> Notice Board</h3>
          <ul>
            <li><span class="date">2025/26</span> Admission Open</li>
            <li><span class="date">Nov 10</span> Orientation Program</li>
            <li><span class="date">Nov 12</span> Classes Begin</li>
            <li><span class="date">Dec 20–25</span> Sports Week</li>
          </ul>
        </div>

        <div class="update-card events">
          <h3><i class="fa fa-calendar"></i> Recent Events</h3>
          <ul>
            <li><span class="date">Sept 14</span> AI Workshop</li>
            <li><span class="date">Aug 30</span> Sports Week</li>
            <li><span class="date">June 10</span> Project Exhibition</li>
          </ul>
        </div>
      </section>
    </section>

    <!-- ===== CAMPUS INFO ===== -->
    <section class="section bg-grey">
      <div class="container campus-flex">
        <div class="campus-img">
          <img src="assets/images/work.jpg" alt="Campus Image">
        </div>
        <div class="campus-text">
          <h2>Campus Information</h2>
          <p>
            Sindhuli Community Technical Institute (SCTI) was established in 2014 AD as a
            nonprofit community-based technical institution supported by DCC Sindhuli,
            Kamalamai Municipality, and CTEVT. The institute provides quality technical
            education in engineering, agriculture, and health with a strong focus on
            practical skills and community needs.
          </p>
        </div>
      </div>
    </section>

    <!-- ===== COURSES ===== -->
    <section class="section">
      <div class="container">
        <h2>Our Popular Courses</h2>
        <div class="course-grid">
          <div class="course">B.Tech Ed in IT</div>
          <div class="course">B.Tech Ed in Civil</div>
          <div class="course">Diploma in Civil Engineering</div>
          <div class="course">Diploma in Animal Science</div>
        </div>
      </div>
    </section>

    <!-- ===== TEACHERS ===== -->
    <section class="section bg-grey">
      <div class="container">
        <h2>Meet Our Teachers</h2>
        <div class="teacher-grid">
          <div class="teacher">
            <img src="assets/images/tej.png">
            <h4>Tej Bikram Thapa</h4>
            <p>Chairman</p>
          </div>
          <div class="teacher">
            <img src="assets/images/santosh.png">
            <h4>Santosh Sapkota</h4>
            <p>Administrative</p>
          </div>
          <div class="teacher">
            <img src="assets/images/bibek sir.jpg">
            <h4>Bibek Bhandari</h4>
            <p>Invigilator</p>
          </div>
        </div>
      </div>
    </section>
  `,

  programs: `
    <section class="section">
      <div class="container">
        <div class="section-header">
          <h2><i class="fa fa-graduation-cap"></i> SCTI Programs & Courses</h2>
          <p>Explore our comprehensive technical education programs designed for your success</p>
        </div>

        <div class="program-grid">
          <div class="program-card">
            <div class="program-header">
              <i class="fa fa-paw"></i>
              <h3>Diploma in Animal Husbandry</h3>
            </div>
            <div class="program-body">
              <p class="program-desc">Comprehensive training in animal health, management, and breeding techniques.</p>
              <h4><i class="fa fa-book"></i> Course Content:</h4>
              <ul>
                <li>Animal nutrition and feeding</li>
                <li>Veterinary science basics</li>
                <li>Livestock management and breeding</li>
                <li>Animal health and disease prevention</li>
                <li>Farm management and economics</li>
              </ul>
              <div class="program-info">
                <span><i class="fa fa-clock"></i> Duration: 3 years</span>
                <span><i class="fa fa-chart-bar"></i> Assessment: 50% Internal + 50% External</span>
              </div>
            </div>
          </div>

          <div class="program-card">
            <div class="program-header">
              <i class="fa fa-laptop-code"></i>
              <h3>B.Tech. Ed. in Information Technology</h3>
            </div>
            <div class="program-body">
              <p class="program-desc">Advanced IT education with focus on software development and educational technology.</p>
              <h4><i class="fa fa-book"></i> Course Content:</h4>
              <ul>
                <li>Programming and software development</li>
                <li>Database management and cloud computing</li>
                <li>Network security and cybersecurity</li>
                <li>Educational technology integration</li>
                <li>IT project management</li>
              </ul>
              <div class="program-info">
                <span><i class="fa fa-clock"></i> Duration: 4 years</span>
                <span><i class="fa fa-chart-bar"></i> Assessment: 30% Internal + 70% External</span>
              </div>
            </div>
          </div>

          <div class="program-card">
            <div class="program-header">
              <i class="fa fa-hard-hat"></i>
              <h3>B.Tech. Ed. in Civil Engineering</h3>
            </div>
            <div class="program-body">
              <p class="program-desc">Professional civil engineering education with pedagogical training.</p>
              <h4><i class="fa fa-book"></i> Course Content:</h4>
              <ul>
                <li>Advanced structural analysis and design</li>
                <li>Transportation and highway engineering</li>
                <li>Environmental engineering and sustainability</li>
                <li>Construction technology and management</li>
                <li>Pedagogical approaches in engineering</li>
              </ul>
              <div class="program-info">
                <span><i class="fa fa-clock"></i> Duration: 4 years</span>
                <span><i class="fa fa-chart-bar"></i> Assessment: 30% Internal + 70% External</span>
              </div>
            </div>
          </div>

          <div class="program-card">
            <div class="program-header">
              <i class="fa fa-building"></i>
              <h3>Diploma in Civil Engineering</h3>
            </div>
            <div class="program-body">
              <p class="program-desc">Practical civil engineering training for construction and infrastructure projects.</p>
              <h4><i class="fa fa-book"></i> Course Content:</h4>
              <ul>
                <li>Building construction technology</li>
                <li>Surveying and leveling</li>
                <li>Structural mechanics</li>
                <li>Construction materials and testing</li>
                <li>AutoCAD and design software</li>
              </ul>
              <div class="program-info">
                <span><i class="fa fa-clock"></i> Duration: 3 years</span>
                <span><i class="fa fa-chart-bar"></i> Assessment: 50% Internal + 50% External</span>
              </div>
            </div>
          </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
          <a href="signup.php" class="cta-button">
            <i class="fa fa-user-plus"></i> Apply Now
          </a>
          <a href="#contact" onclick="loadPage('contact'); return false;" class="cta-button cta-button-outline">
            <i class="fa fa-envelope"></i> Contact Us
          </a>
        </div>
      </div>
    </section>

    <section class="section bg-grey">
      <div class="container">
        <div class="section-header">
          <h2>Admission & Benefits</h2>
          <p>Everything you need to know about joining SCTI</p>
        </div>

        <div class="admission-grid">
          <div class="admission-card">
            <div class="admission-icon">
              <i class="fa fa-user-check"></i>
            </div>
            <h3>Eligibility & Admission</h3>
            <ul>
              <li>Minimum SEE GPA of 2.0 for Diploma programs</li>
              <li>Minimum +2 or equivalent for B.Tech. Ed. programs</li>
              <li>Strong foundation in Mathematics and Science</li>
              <li>Interest in technical and vocational fields</li>
            </ul>
          </div>

          <div class="admission-card">
            <div class="admission-icon">
              <i class="fa fa-star"></i>
            </div>
            <h3>Why Choose SCTI?</h3>
            <ul>
              <li>Industry-aligned curriculum</li>
              <li>Hands-on training with modern equipment</li>
              <li>Experienced faculty and industry experts</li>
              <li>Career guidance and job placement support</li>
              <li>Focus on practical and technical skills</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  `,

  gallery: `
    <section class="section">
      <div class="container">
        <div class="section-header">
          <h2><i class="fa fa-images"></i> Photo Gallery</h2>
          <p>Capturing moments of learning, growth, and achievement at SCTI</p>
        </div>

        <div class="gallery-grid">
          <div class="gallery-item">
            <img src="assets/images/img 2.jpg" alt="Workshop">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-tools"></i>
                <h4>Workshop</h4>
              </div>
            </div>
          </div>

          <div class="gallery-item">
            <img src="assets/images/img 3.jpg" alt="Lab Practice">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-flask"></i>
                <h4>Lab Practice</h4>
              </div>
            </div>
          </div>

          <div class="gallery-item">
            <img src="assets/images/img 4.jpg" alt="Graduation">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-graduation-cap"></i>
                <h4>Graduation</h4>
              </div>
            </div>
          </div>

          <div class="gallery-item">
            <img src="assets/images/img5.jpg" alt="Sports Week">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-trophy"></i>
                <h4>Sports Week</h4>
              </div>
            </div>
          </div>

          <div class="gallery-item">
            <img src="assets/images/work.jpg" alt="Project Exhibition">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-project-diagram"></i>
                <h4>Project Exhibition</h4>
              </div>
            </div>
          </div>

          <div class="gallery-item">
            <img src="assets/images/img1.jpg" alt="Campus Life">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-university"></i>
                <h4>Campus Life</h4>
              </div>
            </div>
          </div>

          <div class="gallery-item">
            <img src="assets/images/img 2.jpg" alt="Cultural Event">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-music"></i>
                <h4>Cultural Event</h4>
              </div>
            </div>
          </div>

          <div class="gallery-item">
            <img src="assets/images/img 3.jpg" alt="Field Visit">
            <div class="gallery-overlay">
              <div class="gallery-text">
                <i class="fa fa-map-marked-alt"></i>
                <h4>Field Visit</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  `,

  notices: `
    <section class="section">
      <div class="container">
        <div class="section-header">
          <h2><i class="fa fa-bullhorn"></i> Notice Board</h2>
          <p>Stay updated with the latest announcements and important dates</p>
        </div>

        <div class="notice-grid">
          <div class="notice-card urgent">
            <div class="notice-badge">Urgent</div>
            <div class="notice-date">
              <i class="fa fa-calendar"></i> 2025/26
            </div>
            <h3>Admission Open</h3>
            <p>Applications for 2025/26 academic year are now open. Submit your forms before the deadline.</p>
            <a href="#" class="notice-link">Read More <i class="fa fa-arrow-right"></i></a>
          </div>

          <div class="notice-card">
            <div class="notice-date">
              <i class="fa fa-calendar"></i> Nov 10, 2025
            </div>
            <h3>Orientation Program</h3>
            <p>Scheduled on Nov 10, 2025 for all new students. Attendance is mandatory.</p>
            <a href="#" class="notice-link">Read More <i class="fa fa-arrow-right"></i></a>
          </div>

          <div class="notice-card">
            <div class="notice-date">
              <i class="fa fa-calendar"></i> Nov 12, 2025
            </div>
            <h3>Classes Begin</h3>
            <p>Nov 12, 2025. Please check your timetable online and prepare accordingly.</p>
            <a href="#" class="notice-link">Read More <i class="fa fa-arrow-right"></i></a>
          </div>

          <div class="notice-card">
            <div class="notice-date">
              <i class="fa fa-calendar"></i> Dec 20–25, 2025
            </div>
            <h3>Sports Week</h3>
            <p>Dec 20–25, 2025. Registration for events is open. Join us for exciting competitions!</p>
            <a href="#" class="notice-link">Read More <i class="fa fa-arrow-right"></i></a>
          </div>

          <div class="notice-card">
            <div class="notice-date">
              <i class="fa fa-calendar"></i> Sept 14, 2025
            </div>
            <h3>AI Workshop</h3>
            <p>Sept 14, 2025 – Limited seats available. Register soon to secure your spot.</p>
            <a href="#" class="notice-link">Read More <i class="fa fa-arrow-right"></i></a>
          </div>

          <div class="notice-card">
            <div class="notice-date">
              <i class="fa fa-calendar"></i> June 10, 2025
            </div>
            <h3>Project Exhibition</h3>
            <p>June 10, 2025 – Showcasing student projects. All are welcome to attend.</p>
            <a href="#" class="notice-link">Read More <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </section>
  `,

  contact: `
    <section class="section">
      <div class="container">
        <div class="section-header">
          <h2><i class="fa fa-envelope"></i> Contact Us</h2>
          <p>We would love to hear from you! Reach out for any inquiries</p>
        </div>

        <div class="contact-wrapper">
          <div class="contact-info">
            <h3>Get In Touch</h3>
            <p>We would love to hear from you! Reach out to us for any inquiries.</p>

            <div class="contact-item">
              <div class="contact-icon">
                <i class="fa fa-map-marker-alt"></i>
              </div>
              <div class="contact-details">
                <h4>Address</h4>
                <p>Kamalamai Municipality, Sindhuli<br>Province 3, Nepal</p>
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon">
                <i class="fa fa-phone"></i>
              </div>
              <div class="contact-details">
                <h4>Phone</h4>
                <p>+977-9841234567<br>+977-9841234568</p>
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon">
                <i class="fa fa-envelope"></i>
              </div>
              <div class="contact-details">
                <h4>Email</h4>
                <p>info@scti.edu.np<br>admin@scti.edu.np</p>
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon">
                <i class="fa fa-clock"></i>
              </div>
              <div class="contact-details">
                <h4>Office Hours</h4>
                <p>Sunday - Friday: 10:00 AM - 5:00 PM<br>Saturday: Closed</p>
              </div>
            </div>
          </div>

          <div class="contact-form-wrapper">
            <h3>Send Us a Message</h3>
            <form id="contactForm" class="contact-form" onsubmit="handleContactSubmit(event)">
              <div class="form-group">
                <label for="name"><i class="fa fa-user"></i> Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                <span class="error-message" id="nameError"></span>
              </div>

              <div class="form-group">
                <label for="email"><i class="fa fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <span class="error-message" id="emailError"></span>
              </div>

              <div class="form-group">
                <label for="phone"><i class="fa fa-phone"></i> Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                <span class="error-message" id="phoneError"></span>
              </div>

              <div class="form-group">
                <label for="subject"><i class="fa fa-tag"></i> Subject</label>
                <input type="text" id="subject" name="subject" placeholder="Enter subject" required>
              </div>

              <div class="form-group">
                <label for="message"><i class="fa fa-comment"></i> Message</label>
                <textarea id="message" name="message" rows="5" placeholder="Enter your message" required></textarea>
                <span class="error-message" id="messageError"></span>
              </div>

              <button type="submit" class="submit-btn">
                <i class="fa fa-paper-plane"></i> Send Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  `,

  login: `
    <section class="login-section">
      <div class="login-container">
        
        <div class="login-box">
          <div class="login-header">
            <i class="fa fa-user-circle"></i>
            <h2>Login to SCTI Portal</h2>
            <p>Access your student/staff account</p>
          </div>

          <form class="login-form" id="loginForm" onsubmit="handleLoginSubmit(event)">
            
            <div class="credentials-info">
              <strong><i class="fa fa-info-circle"></i> Test Credentials:</strong>
              <ul>
                <li><strong>Admin:</strong> username: admin, password: admin123</li>
                <li><strong>Teacher:</strong> username: teacher, password: teacher123</li>
                <li><strong>Student:</strong> username: student, password: student123</li>
              </ul>
            </div>
            
            <div id="loginMessage"></div>
            
            <div class="form-group">
              <label for="userType">
                <i class="fa fa-users"></i> User Type
              </label>
              <select id="userType" name="userType" required>
                <option value="">Select User Type</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Administrator</option>
              </select>
            </div>

            <div class="form-group">
              <label for="username">
                <i class="fa fa-user"></i> Username
              </label>
              <input 
                type="text" 
                id="username" 
                name="username" 
                placeholder="Enter your username"
                required
              >
            </div>

            <div class="form-group">
              <label for="password">
                <i class="fa fa-lock"></i> Password
              </label>
              <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter your password"
                required
              >
            </div>

            <div class="form-options">
              <label class="remember-me">
                <input type="checkbox" name="remember"> Remember Me
              </label>
              <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="login-btn">
              <i class="fa fa-sign-in"></i> Login
            </button>

            <div class="login-footer">
              <p>Don't have an account? <a href="#" onclick="loadPage('signup'); return false;">Register Here</a></p>
            </div>

          </form>
        </div>

      </div>
    </section>
  `,

  signup: `
    <section class="login-section">
      <div class="login-container">
        
        <div class="login-box">
          <div class="login-header">
            <i class="fa fa-user-plus"></i>
            <h2>Create SCTI Account</h2>
            <p>Register as a new user</p>
          </div>

          <form class="login-form" id="signupForm" onsubmit="handleSignupSubmit(event)">
            
            <div id="signupMessage"></div>
            
            <div class="form-group">
              <label for="signupUserType">
                <i class="fa fa-users"></i> User Type
              </label>
              <select id="signupUserType" name="userType" required>
                <option value="">Select User Type</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Administrator</option>
              </select>
            </div>

            <div class="form-group">
              <label for="fullName">
                <i class="fa fa-id-card"></i> Full Name
              </label>
              <input 
                type="text" 
                id="fullName" 
                name="fullName" 
                placeholder="Enter your full name"
                required
              >
            </div>

            <div class="form-group">
              <label for="signupUsername">
                <i class="fa fa-user"></i> Username
              </label>
              <input 
                type="text" 
                id="signupUsername" 
                name="username" 
                placeholder="Choose a username"
                required
              >
            </div>

            <div class="form-group">
              <label for="signupEmail">
                <i class="fa fa-envelope"></i> Email
              </label>
              <input 
                type="email" 
                id="signupEmail" 
                name="email" 
                placeholder="Enter your email"
                required
              >
            </div>

            <div class="form-group">
              <label for="signupPassword">
                <i class="fa fa-lock"></i> Password
              </label>
              <input 
                type="password" 
                id="signupPassword" 
                name="password" 
                placeholder="Create a password (min 6 characters)"
                required
              >
            </div>

            <div class="form-group">
              <label for="confirmPassword">
                <i class="fa fa-lock"></i> Confirm Password
              </label>
              <input 
                type="password" 
                id="confirmPassword" 
                name="confirmPassword" 
                placeholder="Confirm your password"
                required
              >
            </div>

            <button type="submit" class="login-btn">
              <i class="fa fa-user-plus"></i> Sign Up
            </button>

            <div class="login-footer">
              <p>Already have an account? <a href="#" onclick="loadPage('login'); return false;">Login Here</a></p>
            </div>

          </form>
        </div>

      </div>
    </section>
  `
};
