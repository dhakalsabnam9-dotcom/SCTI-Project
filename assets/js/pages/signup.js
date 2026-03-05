// Signup page content
const signupPage = `
  <style>
    .password-wrapper {
      position: relative;
    }
    
    .password-wrapper input {
      padding-right: 45px;
    }
    
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
      font-size: 18px;
      transition: all 0.3s;
      z-index: 10;
    }
    
    .password-toggle:hover {
      color: #004080;
      transform: translateY(-50%) scale(1.1);
    }
    
    .password-strength {
      margin-top: 10px;
      font-size: 12px;
      animation: fadeIn 0.3s ease-out;
    }
    
    .strength-bar {
      height: 6px;
      background: #e0e0e0;
      border-radius: 3px;
      margin-top: 8px;
      overflow: hidden;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .strength-bar-fill {
      height: 100%;
      transition: all 0.4s ease;
      width: 0%;
      border-radius: 3px;
    }
    
    .strength-weak { 
      background: linear-gradient(90deg, #dc3545, #ff6b6b);
      width: 33%;
      box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
    }
    
    .strength-medium { 
      background: linear-gradient(90deg, #ffc107, #ffdd57);
      width: 66%;
      box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }
    
    .strength-strong { 
      background: linear-gradient(90deg, #28a745, #5cb85c);
      width: 100%;
      box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
    }
    
    .validation-list {
      margin-top: 12px;
      padding: 15px;
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      border-radius: 8px;
      font-size: 12px;
      border: 1px solid #dee2e6;
      animation: fadeIn 0.3s ease-out;
    }
    
    .validation-list li {
      padding: 6px 0;
      color: #666;
      transition: all 0.3s;
      display: flex;
      align-items: center;
    }
    
    .validation-list li.valid {
      color: #28a745;
      font-weight: 600;
    }
    
    .validation-list li.invalid {
      color: #dc3545;
    }
    
    .validation-list li i {
      margin-right: 8px;
      font-size: 14px;
    }
    
    #matchMessage {
      font-size: 13px;
      margin-top: 8px;
      display: block;
      font-weight: 600;
    }
  </style>
  
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
            <div class="password-wrapper">
              <input 
                type="password" 
                id="signupPassword" 
                name="password" 
                placeholder="Create a password (7-12 characters)"
                required
              >
              <i class="fa fa-eye password-toggle" id="toggleSignupPassword" onclick="toggleSignupPasswordVisibility('signupPassword', 'toggleSignupPassword')"></i>
            </div>
            <div class="password-strength" id="passwordStrength" style="display: none;">
              <div class="strength-bar">
                <div class="strength-bar-fill" id="strengthBar"></div>
              </div>
              <span id="strengthText"></span>
            </div>
            <ul class="validation-list" id="passwordValidation">
              <li id="lengthCheck"><i class="fa fa-circle"></i> 7-12 characters</li>
              <li id="uppercaseCheck"><i class="fa fa-circle"></i> At least one uppercase letter</li>
              <li id="lowercaseCheck"><i class="fa fa-circle"></i> At least one lowercase letter</li>
              <li id="numberCheck"><i class="fa fa-circle"></i> At least one number</li>
              <li id="specialCheck"><i class="fa fa-circle"></i> At least one special character (!@#$%^&*)</li>
            </ul>
          </div>

          <div class="form-group">
            <label for="confirmPassword">
              <i class="fa fa-lock"></i> Confirm Password
            </label>
            <div class="password-wrapper">
              <input 
                type="password" 
                id="confirmPassword" 
                name="confirmPassword" 
                placeholder="Confirm your password"
                required
              >
              <i class="fa fa-eye password-toggle" id="toggleConfirmPassword" onclick="toggleSignupPasswordVisibility('confirmPassword', 'toggleConfirmPassword')"></i>
            </div>
            <span id="matchMessage"></span>
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
`;

// Toggle password visibility for signup
function toggleSignupPasswordVisibility(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);
  
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

// Initialize password validation after page loads
function initSignupValidation() {
  const passwordInput = document.getElementById('signupPassword');
  const confirmPasswordInput = document.getElementById('confirmPassword');
  
  if (!passwordInput || !confirmPasswordInput) return;
  
  // Validation checks
  const checks = {
    length: { element: document.getElementById('lengthCheck'), regex: /^.{7,12}$/ },
    uppercase: { element: document.getElementById('uppercaseCheck'), regex: /[A-Z]/ },
    lowercase: { element: document.getElementById('lowercaseCheck'), regex: /[a-z]/ },
    number: { element: document.getElementById('numberCheck'), regex: /[0-9]/ },
    special: { element: document.getElementById('specialCheck'), regex: /[!@#$%^&*]/ }
  };
  
  // Real-time password validation
  passwordInput.addEventListener('input', function() {
    const password = this.value;
    let validCount = 0;
    
    // Show strength indicator
    document.getElementById('passwordStrength').style.display = 'block';
    
    // Check each validation rule
    for (let key in checks) {
      const check = checks[key];
      const isValid = check.regex.test(password);
      
      if (isValid) {
        check.element.classList.remove('invalid');
        check.element.classList.add('valid');
        check.element.querySelector('i').classList.remove('fa-circle');
        check.element.querySelector('i').classList.add('fa-check-circle');
        validCount++;
      } else {
        check.element.classList.remove('valid');
        check.element.classList.add('invalid');
        check.element.querySelector('i').classList.remove('fa-check-circle');
        check.element.querySelector('i').classList.add('fa-circle');
      }
    }
    
    // Update strength bar
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    strengthBar.className = 'strength-bar-fill';
    
    if (validCount <= 2) {
      strengthBar.classList.add('strength-weak');
      strengthText.textContent = 'Weak';
      strengthText.style.color = '#dc3545';
    } else if (validCount <= 4) {
      strengthBar.classList.add('strength-medium');
      strengthText.textContent = 'Medium';
      strengthText.style.color = '#ffc107';
    } else {
      strengthBar.classList.add('strength-strong');
      strengthText.textContent = 'Strong';
      strengthText.style.color = '#28a745';
    }
    
    // Check password match
    checkPasswordMatch();
  });
  
  // Check password match
  confirmPasswordInput.addEventListener('input', checkPasswordMatch);
  
  function checkPasswordMatch() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    const matchMessage = document.getElementById('matchMessage');
    
    if (confirmPassword === '') {
      matchMessage.textContent = '';
      return;
    }
    
    if (password === confirmPassword) {
      matchMessage.innerHTML = '<i class="fa fa-check-circle" style="color: #28a745;"></i> Passwords match';
      matchMessage.style.color = '#28a745';
    } else {
      matchMessage.innerHTML = '<i class="fa fa-times-circle" style="color: #dc3545;"></i> Passwords do not match';
      matchMessage.style.color = '#dc3545';
    }
  }
}
