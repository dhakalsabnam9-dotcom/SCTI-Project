// Signup page content
const signupPage = `
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
`;
