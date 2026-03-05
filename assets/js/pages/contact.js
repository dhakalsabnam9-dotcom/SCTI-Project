// Contact page content
const contactPage = `
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
`;
