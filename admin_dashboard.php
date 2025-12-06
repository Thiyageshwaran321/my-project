<?php
session_start();
include "db.php";

// REMOVE LOGIN CHECK BECAUSE YOU SAID YOU DON'T HAVE admin_login.php
// If you want to add later, I will create it for you.
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: admin_login.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<style>
* {
  margin: 0; padding: 0;
  font-family: Poppins, sans-serif;
}

body {
  display: flex;
  background: #f7e9d5;
  color: #4b3b25;
  height: 100vh;
}

/* Sidebar */
.sidebar {
  width: 230px;
  background: #9c7742;
  color: white;
  padding: 20px 0;
  border-right: 3px solid #c9a66b;
}
.sidebar h2 { text-align: center; margin-bottom: 30px; }
.sidebar a {
  display: block; padding: 15px 25px;
  color: white; text-decoration: none;
  cursor: pointer;
}
.sidebar a:hover, .sidebar a.active {
  background: #7a5c30;
}

/* Topbar */
.main-content { flex: 1; display: flex; flex-direction: column; }
.topbar {
  background: #fff7ec;
  padding: 15px 25px;
  display: flex; justify-content: space-between;
  border-bottom: 2px solid #d4b68a;
}
.logout-btn {
  background: #9c7742;
  color: white;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
}

/* Content */
.content { padding: 25px; overflow-y: auto; }
.card {
  background: #fffdf9;
  border: 1px solid #e0c6a1;
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 20px;
}

table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; border-bottom: 1px solid #e0c6a1; }
th { background: #9c7742; color: white; }

textarea {
  width: 100%; padding: 6px;
  border-radius: 4px; border: 1px solid #ccc;
}
.btn-deliver { background: #3e8a3e; color: white; padding: 6px 12px; border-radius: 4px; }

.btn-ship { background:#b38b59; }
.btn-out { background:#d28b16; }
.btn-deliver { background:#3e8a3e; }
.status-btn {
    background:#9c7742;
    color:white;
    padding:6px 12px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    margin-right:5px;
}

.status-btn:hover {
    background:#7a5c30;
}


</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
  <h2>ADMIN PANEL</h2>
  <a class="active" onclick="showSection('dashboard')">Dashboard</a>
  <a onclick="showSection('orders')">Manage Orders</a>
  <a onclick="showSection('feedback_section')">Feedback</a>
</div>

<!-- Main Content -->
<div class="main-content">

  <div class="topbar">
    <h3 id="page-title">Dashboard</h3>
    <button class="logout-btn" onclick="logout()">Logout</button>
  </div>

  <div class="content">

    <!-- Dashboard -->
    <div id="dashboard" class="section active">
      <div class="card">
        <h3>Welcome Admin</h3>
        <p>Use the sidebar to manage orders and feedback.</p>
      </div>
    </div>

    <!-- Orders -->
    <div id="orders" class="section" style="display:none;">
      <div class="card">
        <h3>Manage Orders</h3>

        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Material</th>
              <th>Qty</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody id="orderData">
            <tr><td colspan="6" style="text-align:center;">Loading...</td></tr>
          </tbody>
        </table>

      </div>
    </div>

    <!-- Feedback -->
    <div id="feedback_section" class="section" style="display:none;">
      <div class="card">
        <h3>Customer Feedback</h3>

        <table>
          <tr>
            <th>User</th>
            <th>Feedback</th>
            <th>Date</th>
            <th>Reply</th>
          </tr>

          <?php
          $fb = $conn->query("
             SELECT f.*, c.username 
             FROM feedback f
             JOIN customers c ON f.customer_id = c.customer_id
             ORDER BY f.id DESC
          ");

          while ($row = $fb->fetch_assoc()):
          ?>
          <tr>
            <td><?= $row['username'] ?></td>
            <td><?= $row['message'] ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <form action="reply_feedback.php" method="POST">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <textarea name="reply" required><?= $row['admin_reply'] ?></textarea><br>
                <button class="btn-deliver">Send Reply</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </table>

      </div>
    </div>

  </div>
</div>


<script>
    
// Switch between sections
function showSection(sectionId) {
  document.querySelectorAll('.section').forEach(s => s.style.display = "none");
  document.getElementById(sectionId).style.display = "block";

  document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove("active"));
  event.target.classList.add("active");

  document.getElementById("page-title").innerText =
      sectionId.charAt(0).toUpperCase() + sectionId.slice(1);

  if (sectionId === "orders") loadOrders();
}

// Load orders dynamically
function loadOrders() {
  fetch("load_orders.php")
    .then(res => res.text())
    .then(data => document.getElementById("orderData").innerHTML = data);
}

// Logout
function logout() {
  window.location.href = "index.html";
}
</script>

</body>
</html>
