// Load page function
function loadPage(pageName) {
  const contentArea = document.getElementById('content-area');
  contentArea.innerHTML = pages[pageName] || pages.home;
  
  // Initialize signup validation if on signup page
  if (pageName === 'signup') {
    setTimeout(() => {
      initSignupValidation();
    }, 100);
  }

  // Initialize programs after DOM is injected
  if (pageName === 'programs') {
    initPrograms();
  }

  // Initialize gallery after DOM is injected
  if (pageName === 'gallery') {
    initGallery();
  }

  // Initialize notices after DOM is injected
  if (pageName === 'notices') {
    initNotices();
  }
  
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
  
  const form = e.target;
  const formData = new FormData(form);
  const submitBtn = form.querySelector('.submit-btn');
  const originalBtnText = submitBtn.innerHTML;
  
  // Disable button and show loading
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
  
  // Send AJAX request
  fetch('pages/contact-submit.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Show success message
      alert('✓ ' + data.message);
      form.reset();
    } else {
      // Show error message
      alert('✗ ' + data.message);
    }
  })
  .catch(error => {
    alert('✗ An error occurred. Please try again later.');
    console.error('Error:', error);
  })
  .finally(() => {
    // Re-enable button
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalBtnText;
  });
  
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
    console.log('Login response:', data); // Debug log
    
    // Check if login was successful (redirect happened)
    if (data.includes('Invalid username') || data.includes('Invalid password') || data.includes('All fields are required') || data.includes('alert-danger')) {
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
    console.error('Login error:', error); // Debug log
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
  const email = formData.get('email');
  
  // Validate all fields
  if (!formData.get('userType') || !formData.get('fullName') || !formData.get('username') || !email || !password || !confirmPassword) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Please fill in all fields!</div>';
    return false;
  }
  
  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Please enter a valid email address!</div>';
    return false;
  }
  
  // Validate password length
  if (password.length < 7 || password.length > 12) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Password must be between 7 and 12 characters!</div>';
    return false;
  }
  
  // Validate password requirements
  if (!/[A-Z]/.test(password)) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Password must contain at least one uppercase letter!</div>';
    return false;
  }
  
  if (!/[a-z]/.test(password)) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Password must contain at least one lowercase letter!</div>';
    return false;
  }
  
  if (!/[0-9]/.test(password)) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Password must contain at least one number!</div>';
    return false;
  }
  
  if (!/[!@#$%^&*]/.test(password)) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Password must contain at least one special character (!@#$%^&*)!</div>';
    return false;
  }
  
  // Check passwords match
  if (password !== confirmPassword) {
    messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Passwords do not match!</div>';
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
