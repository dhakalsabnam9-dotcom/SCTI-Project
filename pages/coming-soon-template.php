<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login-simple.php");
    exit();
}

// Get page title from URL parameter
$pageTitle = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'Feature';
$pageIcon = isset($_GET['icon']) ? htmlspecialchars($_GET['icon']) : 'fa-cog';

// Define features for each page
$features = [
    'Manage Students' => [
        'Add new student registrations',
        'View and edit student profiles',
        'Track student attendance',
        'Manage student grades and results',
        'Generate student ID cards',
        'Export student data to Excel/PDF'
    ],
    'Manage Teachers' => [
        'Add and manage teacher profiles',
        'Assign subjects and classes',
        'Track teacher attendance',
        'Manage salary and payroll',
        'View teacher performance reports',
        'Schedule management'
    ],
    'Manage Programs' => [
        'Add new courses and programs',
        'Update course curriculum',
        'Manage course fees and duration',
        'Assign teachers to courses',
        'Track program enrollment',
        'Generate program reports'
    ],
    'Manage Notices' => [
        'Create and publish notices',
        'Schedule notice publication',
        'Categorize notices (Urgent, Event, Exam)',
        'Send email notifications',
        'Archive old notices',
        'View notice analytics'
    ],
    'View Reports' => [
        'Student enrollment reports',
        'Financial reports and analytics',
        'Attendance reports',
        'Performance analytics',
        'Custom report builder',
        'Export reports in multiple formats'
    ],
    'Settings' => [
        'System configuration',
        'User role management',
        'Email and SMS settings',
        'Backup and restore database',
        'Theme customization',
        'Security settings'
    ]
];

$currentFeatures = isset($features[$pageTitle]) ? $features[$pageTitle] : [
    'Full CRUD operations',
    'Advanced search and filtering',
    'Data export capabilities',
    'Real-time updates',
    'Detailed analytics'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle; ?> - SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  
  <style>
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
    .admin-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
      padding: 20px 0;
      margin-bottom: 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .admin-header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .admin-header h1 {
      margin: 0;
      font-size: 24px;
    }
    
    .admin-header a {
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      background: rgba(255,255,255,0.2);
      border-radius: 5px;
      transition: all 0.3s;
      margin-left: 10px;
    }
    
    .admin-header a:hover {
      background: rgba(255,255,255,0.3);
    }
    
    .coming-soon-container {
      max-width: 800px;
      margin: 100px auto;
      text-align: center;
      padding: 60px 40px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .coming-soon-icon {
      width: 120px;
      height: 120px;
      margin: 0 auto 30px;
      background: linear-gradient(135deg, #004080, #0059b3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 60px;
      color: white;
      animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(0, 64, 128, 0.7);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 20px rgba(0, 64, 128, 0);
      }
    }
    
    .coming-soon-container h2 {
      font-size: 36px;
      color: #004080;
      margin-bottom: 20px;
    }
    
    .coming-soon-container p {
      font-size: 18px;
      color: #666;
      line-height: 1.8;
      margin-bottom: 40px;
    }
    
    .feature-list {
      text-align: left;
      max-width: 500px;
      margin: 40px auto;
      padding: 30px;
      background: #f8f9fa;
      border-radius: 10px;
    }
    
    .feature-list h3 {
      color: #004080;
      margin-bottom: 20px;
      font-size: 20px;
    }
    
    .feature-list ul {
      list-style: none;
      padding: 0;
    }
    
    .feature-list li {
      padding: 10px 0;
      padding-left: 30px;
      position: relative;
      color: #555;
    }
    
    .feature-list li::before {
      content: "✓";
      position: absolute;
      left: 0;
      color: #28a745;
      font-weight: bold;
      font-size: 18px;
    }
    
    .back-btn {
      display: inline-block;
      padding: 15px 40px;
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
      text-decoration: none;
      border-radius: 30px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s;
      box-shadow: 0 5px 15px rgba(0, 64, 128, 0.3);
    }
    
    .back-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 64, 128, 0.4);
    }
    
    .back-btn i {
      margin-right: 8px;
    }
  </style>
</head>

<body>

<!-- Admin Header -->
<div class="admin-header">
  <div class="container">
    <h1><i class="fa <?php echo $pageIcon; ?>"></i> <?php echo $pageTitle; ?></h1>
    <div>
      <a href="../dashboards/admin-dashboard.php"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
      <a href="../includes/logout.php" style="background: #dc3545;"><i class="fa fa-sign-out"></i> Logout</a>
    </div>
  </div>
</div>

<!-- Coming Soon Content -->
<div class="coming-soon-container">
  <div class="coming-soon-icon">
    <i class="fa <?php echo $pageIcon; ?>"></i>
  </div>
  
  <h2><?php echo $pageTitle; ?></h2>
  <p>This feature is currently under development and will be available soon. We're working hard to bring you the best experience!</p>
  
  <div class="feature-list">
    <h3>Planned Features:</h3>
    <ul>
      <?php foreach ($currentFeatures as $feature): ?>
        <li><?php echo htmlspecialchars($feature); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  
  <a href="../dashboards/admin-dashboard.php" class="back-btn">
    <i class="fa fa-arrow-left"></i> Back to Dashboard
  </a>
</div>

</body>
</html>
