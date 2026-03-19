// Login page content
const loginPage = `
  <style>
    .credentials-info {
      background: linear-gradient(135deg, #d1ecf1, #bee5eb);
      color: #0c5460;
      padding: 18px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-size: 13px;
      border-left: 4px solid #17a2b8;
      box-shadow: 0 2px 10px rgba(23, 162, 184, 0.2);
      animation: fadeIn 0.5s ease-out;
    }
    
    .credentials-info strong {
      display: block;
      margin-bottom: 10px;
      font-size: 14px;
    }
    
    .credentials-info ul {
      margin: 8px 0;
      padding-left: 20px;
      line-height: 1.8;
    }
    
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
  </style>
  
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
            <strong><i class="fa fa-info-circle"></i> Login Information:</strong>
            <p>Use your username and password provided by the administration to login.</p>
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
            <div class="password-wrapper">
              <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter your password"
                required
              >
              <i class="fa fa-eye password-toggle" id="togglePassword" onclick="toggleLoginPassword()"></i>
            </div>
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
            <p>Contact your administrator if you need access.</p>
          </div>

        </form>
      </div>

    </div>
  </section>
`;

// Toggle password visibility for login
function toggleLoginPassword() {
  const passwordInput = document.getElementById('password');
  const toggleIcon = document.getElementById('togglePassword');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleIcon.classList.remove('fa-eye');
    toggleIcon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    toggleIcon.classList.remove('fa-eye-slash');
    toggleIcon.classList.add('fa-eye');
  }
}
