<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Library | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(111,66,193,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .search-bar {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;
      display: flex; gap: 10px;
    }
    .search-input {
      flex: 1; padding: 12px 20px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 16px;
    }
    .search-input:focus {
      outline: none; border-color: #6f42c1;
    }
    .search-btn {
      padding: 12px 30px; background: linear-gradient(135deg, #6f42c1, #e83e8c);
      color: white; border: none; border-radius: 6px;
      cursor: pointer; font-size: 16px; transition: all 0.3s;
    }
    .search-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(111,66,193,0.3);
    }
    
    .stats-row {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    .stat-box {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;
    }
    .stat-box h3 { font-size: 36px; color: #6f42c1; margin: 10px 0; }
    
    .book-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 25px;
    }
    
    .book-card {
      background: white; border-radius: 10px; overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: all 0.3s;
    }
    .book-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 25px rgba(111,66,193,0.2);
    }
    
    .book-cover {
      height: 200px; background: linear-gradient(135deg, #6f42c1, #e83e8c);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: 48px;
    }
    
    .book-body {
      padding: 20px;
    }
    .book-title {
      font-size: 18px; font-weight: 600; color: #333;
      margin-bottom: 8px;
    }
    .book-author {
      color: #666; font-size: 14px; margin-bottom: 12px;
    }
    .book-meta {
      display: flex; gap: 15px; margin: 12px 0;
      font-size: 13px; color: #666;
    }
    .book-meta i { color: #6f42c1; }
    
    .book-status {
      padding: 6px 12px; border-radius: 20px; font-size: 12px;
      font-weight: 600; display: inline-block; margin: 10px 0;
    }
    .status-available { background: #d4edda; color: #155724; }
    .status-borrowed { background: #fff3cd; color: #856404; }
    .status-unavailable { background: #f8d7da; color: #721c24; }
    
    .book-actions {
      display: flex; gap: 10px; margin-top: 15px;
    }
    .btn {
      flex: 1; padding: 10px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-align: center;
    }
    .btn-primary {
      background: linear-gradient(135deg, #6f42c1, #e83e8c);
      color: white;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(111,66,193,0.3);
    }
    .btn-outline {
      background: white; color: #6f42c1;
      border: 2px solid #6f42c1;
    }
    .btn-outline:hover {
      background: #6f42c1; color: white;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Library - Browse, borrow, and explore our extensive collection of books</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-book"></i> Library</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Library
    </div>
  </div>

  <div class="search-bar">
    <input type="text" class="search-input" placeholder="Search books by title, author, or ISBN...">
    <button class="search-btn"><i class="fa fa-search"></i> Search</button>
  </div>

  <div class="stats-row">
    <div class="stat-box">
      <i class="fa fa-book" style="font-size: 32px; color: #6f42c1;"></i>
      <h3>2</h3>
      <p>Books Borrowed</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-clock" style="font-size: 32px; color: #ffc107;"></i>
      <h3>5</h3>
      <p>Days Until Return</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-history" style="font-size: 32px; color: #28a745;"></i>
      <h3>15</h3>
      <p>Books Read</p>
    </div>
  </div>

  <h2 style="color: #6f42c1; margin-bottom: 20px;"><i class="fa fa-star"></i> Available Books</h2>

  <div class="book-grid">
    
    <div class="book-card">
      <div class="book-cover">
        <i class="fa fa-book-open"></i>
      </div>
      <div class="book-body">
        <div class="book-title">Introduction to Algorithms</div>
        <div class="book-author">by Thomas H. Cormen</div>
        <div class="book-meta">
          <span><i class="fa fa-barcode"></i> ISBN: 978-0262033848</span>
        </div>
        <div class="book-meta">
          <span><i class="fa fa-layer-group"></i> Computer Science</span>
        </div>
        <span class="book-status status-available">Available</span>
        <div class="book-actions">
          <button class="btn btn-primary" onclick="alert('Borrow book coming soon!');">
            <i class="fa fa-hand-holding"></i> Borrow
          </button>
          <button class="btn btn-outline" onclick="alert('View details coming soon!');">
            <i class="fa fa-eye"></i> Details
          </button>
        </div>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover" style="background: linear-gradient(135deg, #28a745, #20c997);">
        <i class="fa fa-database"></i>
      </div>
      <div class="book-body">
        <div class="book-title">Database System Concepts</div>
        <div class="book-author">by Abraham Silberschatz</div>
        <div class="book-meta">
          <span><i class="fa fa-barcode"></i> ISBN: 978-0078022159</span>
        </div>
        <div class="book-meta">
          <span><i class="fa fa-layer-group"></i> Database</span>
        </div>
        <span class="book-status status-borrowed">Borrowed - Due Mar 12</span>
        <div class="book-actions">
          <button class="btn btn-outline" onclick="alert('Renew coming soon!');">
            <i class="fa fa-redo"></i> Renew
          </button>
        </div>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover" style="background: linear-gradient(135deg, #fd7e14, #ffc107);">
        <i class="fa fa-code"></i>
      </div>
      <div class="book-body">
        <div class="book-title">Clean Code</div>
        <div class="book-author">by Robert C. Martin</div>
        <div class="book-meta">
          <span><i class="fa fa-barcode"></i> ISBN: 978-0132350884</span>
        </div>
        <div class="book-meta">
          <span><i class="fa fa-layer-group"></i> Software Engineering</span>
        </div>
        <span class="book-status status-available">Available</span>
        <div class="book-actions">
          <button class="btn btn-primary" onclick="alert('Borrow book coming soon!');">
            <i class="fa fa-hand-holding"></i> Borrow
          </button>
          <button class="btn btn-outline" onclick="alert('View details coming soon!');">
            <i class="fa fa-eye"></i> Details
          </button>
        </div>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover" style="background: linear-gradient(135deg, #004080, #0059b3);">
        <i class="fa fa-laptop-code"></i>
      </div>
      <div class="book-body">
        <div class="book-title">JavaScript: The Good Parts</div>
        <div class="book-author">by Douglas Crockford</div>
        <div class="book-meta">
          <span><i class="fa fa-barcode"></i> ISBN: 978-0596517748</span>
        </div>
        <div class="book-meta">
          <span><i class="fa fa-layer-group"></i> Web Development</span>
        </div>
        <span class="book-status status-borrowed">Borrowed - Due Mar 10</span>
        <div class="book-actions">
          <button class="btn btn-outline" onclick="alert('Renew coming soon!');">
            <i class="fa fa-redo"></i> Renew
          </button>
        </div>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
        <i class="fa fa-brain"></i>
      </div>
      <div class="book-body">
        <div class="book-title">Design Patterns</div>
        <div class="book-author">by Gang of Four</div>
        <div class="book-meta">
          <span><i class="fa fa-barcode"></i> ISBN: 978-0201633610</span>
        </div>
        <div class="book-meta">
          <span><i class="fa fa-layer-group"></i> Software Design</span>
        </div>
        <span class="book-status status-unavailable">Unavailable</span>
        <div class="book-actions">
          <button class="btn btn-outline" onclick="alert('Reserve coming soon!');">
            <i class="fa fa-bookmark"></i> Reserve
          </button>
        </div>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover" style="background: linear-gradient(135deg, #20c997, #28a745);">
        <i class="fa fa-network-wired"></i>
      </div>
      <div class="book-body">
        <div class="book-title">Computer Networks</div>
        <div class="book-author">by Andrew S. Tanenbaum</div>
        <div class="book-meta">
          <span><i class="fa fa-barcode"></i> ISBN: 978-0132126953</span>
        </div>
        <div class="book-meta">
          <span><i class="fa fa-layer-group"></i> Networking</span>
        </div>
        <span class="book-status status-available">Available</span>
        <div class="book-actions">
          <button class="btn btn-primary" onclick="alert('Borrow book coming soon!');">
            <i class="fa fa-hand-holding"></i> Borrow
          </button>
          <button class="btn btn-outline" onclick="alert('View details coming soon!');">
            <i class="fa fa-eye"></i> Details
          </button>
        </div>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Student Portal</p>
</footer>

</body>
</html>
