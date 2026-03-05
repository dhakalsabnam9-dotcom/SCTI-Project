// Programs page content
const programsPage = `
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
        <a href="#" onclick="loadPage('signup'); return false;" class="cta-button">
          <i class="fa fa-user-plus"></i> Apply Now
        </a>
        <a href="#" onclick="loadPage('contact'); return false;" class="cta-button cta-button-outline">
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
`;
