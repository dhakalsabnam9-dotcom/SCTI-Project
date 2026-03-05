// Load page function
function loadPage(pageName) {
  const contentArea = document.getElementById('content-area');
  contentArea.innerHTML = pages[pageName] || pages.home;
  
  // Scroll to top smoothly
  window.scrollTo({ top: 0, behavior: 'smooth' });
  
  // Close mobile menu if open
  document.getElementById("menu").classList.remove("show");
  
  // Update active menu item
  document.querySelectorAll('.menu ul li a').forEach(link => {
    link.classList.remove('active');
  });
  event.target.classList.add('active');
}

// Contact form handler
function handleContactSubmit(e) {
  e.preventDefault();
  alert('Thank you for contacting us! We will get back to you soon.');
  e.target.reset();
  return false;
}

// Login form handler
function handleLoginSubmit(e) {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  const messageDiv = document.getElementById('loginMessage');
  
  // Show loading state
  messageDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Logging in...</div>';
  
  // Send AJAX request
  fetch('pages/login-simple.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    // Check if login was successful (redirect happened)
    if (data.includes('Invalid username') || data.includes('All fields are required')) {
      // Extract error message
      const parser = new DOMParser();
      const doc = parser.parseFromString(data, 'text/html');
      const errorElement = doc.querySelector('.alert-danger');
      const errorMessage = errorElement ? errorElement.textContent.trim() : 'Login failed. Please try again.';
      
      messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + errorMessage + '</div>';
    } else {
      // Success - redirect to appropriate dashboard
      messageDiv.innerHTML = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Login successful! Redirecting...</div>';
      
      // Redirect based on user type
      const userType = formData.get('userType');
      setTimeout(() => {
        if (userType === 'admin') {
          window.location.href = 'dashboards/admin-dashboard.php';
        } else if (userType === 'teacher') {
          window.location.href = 'dashboards/teacher-dashboard.php';
        } else if (userType === 'student') {
          window.location.href = 'dashboards/student-dashboard.php';
        }
      }, 1000);
    }
  })
  .catch(error => {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> An error occurred. Please try again.</div>';
  });
  
  return false;
}

// Signup form handler
function handleSignupSubmit(e) {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  const messageDiv = document.getElementById('signupMessage');
  
  // Client-side validation
  const password = formData.get('password');
  const confirmPassword = formData.get('confirmPassword');
  
  if (password !== confirmPassword) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Passwords do not match!</div>';
    return false;
  }
  
  if (password.length < 6) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Password must be at least 6 characters long!</div>';
    return false;
  }
  
  // Show loading state
  messageDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Creating account...</div>';
  
  // Send AJAX request
  fetch('pages/signup.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    // Check if signup was successful
    if (data.includes('already exists') || data.includes('already registered') || data.includes('All fields are required')) {
      // Extract error message
      const parser = new DOMParser();
      const doc = parser.parseFromString(data, 'text/html');
      const errorElement = doc.querySelector('.alert-danger');
      const errorMessage = errorElement ? errorElement.textContent.trim() : 'Signup failed. Please try again.';
      
      messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + errorMessage + '</div>';
    } else {
      // Success - redirect to appropriate dashboard
      messageDiv.innerHTML = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Account created successfully! Redirecting...</div>';
      
      // Redirect based on user type
      const userType = formData.get('userType');
      setTimeout(() => {
        if (userType === 'admin') {
          window.location.href = 'dashboards/admin-dashboard.php';
        } else if (userType === 'teacher') {
          window.location.href = 'dashboards/teacher-dashboard.php';
        } else if (userType === 'student') {
          window.location.href = 'dashboards/student-dashboard.php';
        }
      }, 1000);
    }
  })
  .catch(error => {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> An error occurred. Please try again.</div>';
  });
  
  return false;
}

// Load home page by default
window.onload = function() {
  loadPage('home');
};
