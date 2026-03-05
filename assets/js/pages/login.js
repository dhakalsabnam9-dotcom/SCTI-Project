// Login page content
const loginPage = `
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
`;
