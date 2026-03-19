// Home page content
const homePage = `
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
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">2025/26</span>
            <span class="item-text">Admission Open</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Nov 10</span>
            <span class="item-text">Orientation Program</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Nov 12</span>
            <span class="item-text">Classes Begin</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Dec 20–25</span>
            <span class="item-text">Sports Week</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
        </ul>
        <div class="card-footer-link" onclick="loadPage('notices')">
          <span>View All Notices</span> <i class="fa fa-arrow-right"></i>
        </div>
      </div>

      <div class="update-card events">
        <h3><i class="fa fa-calendar-alt"></i> Recent Events</h3>
        <ul>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Sept 14</span>
            <span class="item-text">AI Workshop</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Aug 30</span>
            <span class="item-text">Sports Week</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">June 10</span>
            <span class="item-text">Project Exhibition</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
        </ul>
        <div class="card-footer-link" onclick="loadPage('notices')">
          <span>View All Events</span> <i class="fa fa-arrow-right"></i>
        </div>
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
`;
