<?php
session_start();
include "db.php";

// Fetch all orders for the table
$orders = $conn->query("
    SELECT o.*, c.username, c.mobilenum 
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY o.order_id DESC
");

// Fetch stats for dashboard
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$pendingOrders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];

// Update low stock count to use individual product limits
$lowStock = $conn->query("
    SELECT COUNT(*) as count 
    FROM products 
    WHERE stock < low_stock_limit
")->fetch_assoc()['count'];

// Fetch products for stock management - include low_stock_limit
$products = $conn->query("
    SELECT product_id, material_name, price, stock, low_stock_limit 
    FROM products 
    ORDER BY product_id ASC
");

// Fetch product list for product management - include low_stock_limit
$plist = $conn->query("
    SELECT product_id, material_name, price, stock, unit_type, image, low_stock_limit 
    FROM products 
    ORDER BY product_id ASC
");

// Fetch feedback with all necessary fields
$fb = $conn->query("
    SELECT f.*, c.username, c.mobilenum 
    FROM feedback f
    JOIN customers c ON f.customer_id = c.customer_id
    ORDER BY f.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="admin_dashboard.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* Ensure table container is scrollable */
    .card table {
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    /* Make sure all content is visible */
    #orders_section .card {
        overflow: visible;
    }
    
    /* Ensure main content doesn't cut off */
    .main-content {
        overflow-x: auto;
    }
    
    /* Style for low stock limit input */
    .limit-input {
        width: 80px;
        padding: 0.4rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    
    .stock-warning {
        color: #e53e3e;
        font-weight: 600;
    }
    
    .stock-normal {
        color: #38a169;
    }

    /* Feedback section styles */
    .feedback-message {
        background: #f8fafc;
        padding: 0.75rem;
        border-radius: 8px;
        border-left: 4px solid #4299e1;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .status-replied {
        background: #c6f6d5;
        color: #22543d;
    }
    
    .status-pending {
        background: #fed7d7;
        color: #742a2a;
    }
    
    .previous-reply {
        background: #ebf8ff;
        padding: 0.5rem;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        border-left: 3px solid #3182ce;
    }
</style>
<body>
<button class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i> Menu
</button>
<!-- Sidebar -->  
<div class="sidebar">
  <div class="sidebar-header">
    <h2>ADMIN PANEL</h2>
    <div class="sidebar-subtitle">PK Building Materials</div>
  </div>
  <a class="active" onclick="showSection('dashboard')">
    <i class="fas fa-tachometer-alt"></i> Dashboard
  </a>
  <a onclick="showSection('orders_section')">
    <i class="fas fa-shopping-cart"></i> Manage Orders
  </a>
  <a onclick="showSection('feedback_section')">
    <i class="fas fa-comments"></i> Feedback
  </a>
  <a onclick="showSection('stock_section')">
    <i class="fas fa-boxes"></i> Manage Stock
  </a>
  <a onclick="showSection('product_section')">
    <i class="fas fa-tags"></i> Manage Products
  </a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="topbar">
    <div class="topbar-content">
      <h3 id="page-title">Dashboard</h3>
      <button class="logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </div>
  </div>

  <div class="content">
 
    <!-- Dashboard -->
    <div id="dashboard" class="section active">
      <div class="card">
        <h3>Welcome Admin</h3>
        <p>Use the sidebar to manage orders, stock, products, and customer feedback.</p>
        
        <!-- Stats Overview -->
        <div class="stats-grid" style="margin-top: 2rem;">
          <div class="stat-card">
            <div class="stat-number"><?= $totalOrders ?></div>
            <div class="stat-label">Total Orders</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= $pendingOrders ?></div>
            <div class="stat-label">Pending Orders</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= $totalProducts ?></div>
            <div class="stat-label">Total Products</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= $lowStock ?></div>
            <div class="stat-label">Low Stock Items</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ORDERS SECTION -->
    <div id="orders_section" class="section" style="display:none;">
      <div class="card">
        <h3>Manage Orders</h3>
        
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>CUSTOMER</th>
              <th>MATERIAL</th>
              <th>QTY</th>
              <th>TOTAL</th>
              <th>STATUS</th>
              <th>EXPECTED DELIVERY</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            // Reset the orders pointer and fetch again
            $orders = $conn->query("
                SELECT o.*, c.username, c.mobilenum 
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                ORDER BY o.order_id DESC
            ");
            while ($o = $orders->fetch_assoc()): 
            ?>
            <tr>
              <!-- ID -->
              <td><strong>#<?= $o['order_id'] ?></strong></td>
              
              <!-- CUSTOMER -->
              <td class="customer-info">
                <div class="customer-name"><?= htmlspecialchars($o['username']) ?></div>
                <div class="customer-phone"><?= $o['mobilenum'] ?></div>
              </td>
              
              <!-- MATERIAL -->
              <td><?= htmlspecialchars($o['material_name']) ?></td>
              
              <!-- QUANTITY -->
              <td>
                <span class="quantity-badge"><?= $o['quantity'] ?></span>
              </td>
              
              <!-- TOTAL -->
              <td class="total-amount">₹<?= number_format($o['total_amount'], 2) ?></td>
              
              <!-- STATUS -->
              <td>
                <span class="status-badge status-<?= strtolower($o['status']) ?>">
                  <?= strtoupper($o['status']) ?>
                </span>
              </td>
              
              <!-- EXPECTED DELIVERY -->
              <td class="expected-box" id="delivery-<?= $o['order_id'] ?>">
                <?php 
                if ($o['expected_delivery_date']) {
                    $date = new DateTime($o['expected_delivery_date']);
                    echo "<span class='expected-date'>" . $date->format('d M Y') . "</span>";
                    
                    if ($o['expected_delivery_time']) {
                        // Convert time to proper format (remove seconds if present)
                        $time = $o['expected_delivery_time'];
                        if (strlen($time) > 5) {
                            $time = substr($time, 0, 5);
                        }
                        echo "<span class='expected-time'>" . date('h:i A', strtotime($time)) . "</span>";
                    } else {
                        echo "<span class='notset'>Time not set</span>";
                    }
                } else {
                    echo "<span class='notset'>Date not set</span>";
                }
                ?>
              </td>
              
              <!-- ACTIONS -->
              <td>
                <div class="action-form" id="form-<?= $o['order_id'] ?>">
                  <form method="POST" action="update_delivery.php" onsubmit="return updateDeliveryForm(<?= $o['order_id'] ?>, this)">
                    <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                    
                    <div class="form-group">
                      <input class="date-input" type="date" 
                             name="expected_delivery_date"
                             value="<?= $o['expected_delivery_date'] ?>"
                             title="Select delivery date">
                      
                      <?php 
                      $time_value = '';
                      if ($o['expected_delivery_time']) {
                          $time_value = $o['expected_delivery_time'];
                          if (strlen($time_value) > 5) {
                              $time_value = substr($time_value, 0, 5);
                          }
                      }
                      ?>
                      <input class="time-input" type="time" 
                             name="expected_delivery_time"
                             value="<?= $time_value ?>"
                             title="Select delivery time (HH:MM format)">
                    </div>
                    
                    <select class="status-select" name="status" id="status-<?= $o['order_id'] ?>">
                      <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                      <option value="shipped" <?= $o['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                      <option value="out_for_delivery" <?= $o['status'] == 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                      <option value="delivered" <?= $o['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                      <option value="cancelled" <?= $o['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    
                    <button type="submit" class="update-delivery-btn">
                      <i class="fas fa-calendar-check"></i> Update Delivery & Status
                    </button>
                  </form>
                  
                  <div class="quick-actions">
                    <button type="button" class="quick-action-btn pending" onclick="quickUpdateStatus(<?= $o['order_id'] ?>, 'pending')">PENDING</button>
                    <button type="button" class="quick-action-btn shipped" onclick="quickUpdateStatus(<?= $o['order_id'] ?>, 'shipped')">SHIPPED</button>
                    <button type="button" class="quick-action-btn out_for_delivery" onclick="quickUpdateStatus(<?= $o['order_id'] ?>, 'out_for_delivery')">OUT FOR DELIVERY</button>
                    <button type="button" class="quick-action-btn delivered" onclick="quickUpdateStatus(<?= $o['order_id'] ?>, 'delivered')">DELIVERED</button>
                    <button type="button" class="quick-action-btn cancelled" onclick="quickUpdateStatus(<?= $o['order_id'] ?>, 'cancelled')">CANCEL</button>
                  </div>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
            
            <?php if ($orders->num_rows == 0): ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 3rem;">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                <p>No orders found</p>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
       
<!-- STOCK MANAGEMENT -->
<div id="stock_section" class="section" style="display:none;">
  <div class="card">
    <h3>Manage Product Stock</h3>
    
  
    
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Material</th>
          <th>Price</th>
          <th>Current Stock</th>
          <th>Restock Level</th>
          <th>Update Stock (Add)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        // Reset products query to include unit_type and reorder_level
        // Assuming your database has a 'reorder_level' column
        // If not, we'll use low_stock_limit as reorder_level
        $products = $conn->query("
            SELECT product_id, material_name, price, stock, low_stock_limit as reorder_level, unit_type 
            FROM products 
            ORDER BY product_id ASC
        ");
        
        while ($p = $products->fetch_assoc()): 
          // Calculate critical level (20% of reorder level, but at least 1)
          $criticalLevel = max(1, floor($p['reorder_level'] * 0.2));
        ?>
        <tr>
          <td><?= $p['product_id'] ?></td>
          <td><?= htmlspecialchars($p['material_name']) ?></td>
          <td>₹<?= number_format($p['price']) ?></td>
          
          <!-- CURRENT STOCK with Color Coding based on Individual Restock Level -->
          <td>
            <?php
            // Determine stock status and color based on individual reorder level
            $stockStatus = '';
            $stockColor = '';
            $stockBgColor = '';
            
            if ($p['stock'] <= $criticalLevel) {
                // Critical low stock - Dark Red
                $stockStatus = 'CRITICAL';
                $stockColor = '#ffffff';
                $stockBgColor = '#c53030'; // Dark red
            } elseif ($p['stock'] <= $p['reorder_level']) {
                // Low stock (needs reorder) - Light Red
                $stockStatus = 'REORDER NEEDED';
                $stockColor = '#742a2a';
                $stockBgColor = '#fed7d7'; // Light red
            } else {
                // Normal stock - Green
                $stockStatus = 'Normal';
                $stockColor = '#22543d';
                $stockBgColor = '#c6f6d5'; // Light green
            }
            ?>
            
            <span class="stock-badge" style="
              display: inline-block;
              padding: 0.5rem 1rem;
              background: <?= $stockBgColor ?>;
              color: <?= $stockColor ?>;
              font-weight: 600;
              border-radius: 20px;
              min-width: 100px;
              text-align: center;
            ">
              <?= $p['stock'] ?> <?= $p['unit_type'] ?>
            </span>
            
            <?php if ($p['stock'] <= $p['reorder_level']): ?>
              <span style="
                display: inline-block;
                margin-left: 0.5rem;
                padding: 0.25rem 0.75rem;
                background: <?= $p['stock'] <= $criticalLevel ? '#c53030' : '#e53e3e' ?>;
                color: white;
                font-size: 0.75rem;
                font-weight: 600;
                border-radius: 12px;
                white-space: nowrap;
              ">
                ⚠️ <?= $stockStatus ?>
              </span>
            <?php endif; ?>
          </td>
          
          <!-- INDIVIDUAL RESTOCK LEVEL (Different for each product) -->
          <td>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <input type="number"
                     id="reorder_<?= $p['product_id'] ?>"
                     value="<?= $p['reorder_level'] ?>"
                     min="1"
                     class="limit-input"
                     style="width: 100px; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 8px; text-align: center; font-weight: 600;"
                     title="Set when to reorder this product">
              <span style="color: #4a5568; font-weight: 500;"><?= $p['unit_type'] ?></span>
            </div>
            
            
          
          </td>
          
          <!-- UPDATE STOCK (ADD) -->
          <td>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <input type="number"
                     id="add_stock_<?= $p['product_id'] ?>"
                     value="0"
                     min="0"
                     class="date-input" 
                     style="width: 100px; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 8px; text-align: center;"
                     placeholder="Qty">
              <span style="color: #4a5568; font-weight: 500;"><?= $p['unit_type'] ?></span>
            </div>
            <small style="display:block; color:#718096; margin-top: 0.25rem;">Enter quantity to add</small>
          </td>
          
          <!-- ACTION BUTTON -->
          <td>
            <button class="action-btn update-btn" 
                    style="
                      width: auto; 
                      padding: 0.5rem 1.5rem;
                      background: #4299e1;
                      color: white;
                      border: none;
                      border-radius: 8px;
                      font-weight: 600;
                      cursor: pointer;
                      transition: all 0.3s ease;
                      display: inline-flex;
                      align-items: center;
                      gap: 0.5rem;
                    "
                    onmouseover="this.style.background='#3182ce'"
                    onmouseout="this.style.background='#4299e1'"
                    onclick="addToStock(<?= $p['product_id'] ?>, <?= $p['stock'] ?>, '<?= $p['unit_type'] ?>')">
              <i class="fas fa-plus-circle"></i> Update stock
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
    <!-- PRODUCT MANAGEMENT -->
    <div id="product_section" class="section" style="display:none;">
      <div class="card">
        <h3>Add / Edit Product</h3>
        
        <form action="save_product.php" method="POST">
          <input type="hidden" name="product_id" id="product_id">
          
          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label>Material Name</label>
              <input type="text" name="material_name" required class="date-input">
            </div>
            
            <div>
              <label>Price (₹)</label>
              <input type="number" name="price" required class="date-input" step="0.01">
            </div>
          </div>
          
          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label>Stock</label>
              <input type="number" name="stock" min="0" required class="date-input">
            </div>
            
            <div>
              <label>Low Stock Limit</label>
              <input type="number" name="low_stock_limit" min="1" value="10" required class="date-input">
              <small style="color: var(--gray); font-size: 0.85rem;">Alert when stock falls below this limit</small>
            </div>
          </div>
          
          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label>Unit Type</label>
              <select name="unit_type" required class="status-select">
                <option value="">Select Unit</option>
                <option value="unit">Unit</option>
                <option value="pack">Pack</option>
                <option value="piece">Piece</option>
                <option value="kg">Kilogram</option>
                <option value="liter">Liter</option>
                <option value="bag">Bag</option>
                <option value="box">Box</option>
              </select>
            </div>
          </div>
          
          <div>
            <label>Image Path (relative path from root)</label>
            <input type="text" name="image" placeholder="static/example.jpg" class="date-input">
            <small style="color: var(--gray); font-size: 0.85rem;">Example: static/products/cement.jpg</small>
          </div>
          
          <button class="action-btn update-btn" type="submit" style="margin-top: 1.5rem; width: 100%;">
            <i class="fas fa-save"></i> Save Product
          </button>
        </form>
      </div>
      
      <!-- PRODUCT LIST -->
      <div class="card">
        <h3>Existing Products</h3>
        
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Material</th>
              <th>Price</th>
              <th>Current Stock</th>
              <th>Unit Type</th>
              <th>Low Stock Limit</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            // Reset product list query
            $plist = $conn->query("
                SELECT product_id, material_name, price, stock, unit_type, image, low_stock_limit 
                FROM products 
                ORDER BY product_id ASC
            ");
            while ($p = $plist->fetch_assoc()): 
            ?>
            <tr>
              <td><?= $p['product_id'] ?></td>
              <td><?= htmlspecialchars($p['material_name']) ?></td>
              <td>₹<?= number_format($p['price'], 2) ?></td>
              <td>
                <span class="quantity-badge" style="background: <?= $p['stock'] < $p['low_stock_limit'] ? '#fed7d7' : '#c6f6d5' ?>;">
                  <?= $p['stock'] ?> <?= $p['unit_type'] ?>
                </span>
              </td>
              <td>
                <span class="unit-badge" style="background: #e2e8f0; padding: 0.25rem 0.5rem; border-radius: 4px;">
                  <?= htmlspecialchars($p['unit_type']) ?>
                </span>
              </td>
              <td>
                <span class="limit-badge" style="background: #e2e8f0; padding: 0.25rem 0.5rem; border-radius: 4px;">
                  <?= $p['low_stock_limit'] ?> <?= $p['unit_type'] ?>
                </span>
              </td>
              <td>
                <button class="action-btn update-btn" style="width: auto; padding: 0.5rem 1rem;"
                  onclick="editMaterial(
                    '<?= $p['product_id'] ?>',
                    '<?= htmlspecialchars($p['material_name'], ENT_QUOTES) ?>',
                    '<?= $p['price'] ?>',
                    '<?= $p['stock'] ?>',
                    '<?= $p['unit_type'] ?>',
                    '<?= $p['image'] ?>',
                    '<?= $p['low_stock_limit'] ?>'
                  )">
                  <i class="fas fa-edit"></i> Edit
                </button>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Feedback -->
    <div id="feedback_section" class="section" style="display:none;">
      <div class="card">
        <h3>Customer Feedback</h3>
        
        <?php
        // Refresh the feedback query
        $fb = $conn->query("
            SELECT f.*, c.username, c.mobilenum 
            FROM feedback f
            JOIN customers c ON f.customer_id = c.customer_id
            ORDER BY f.created_at DESC
        ");
        
        if ($fb && $fb->num_rows > 0):
        ?>
        <div style="overflow-x: auto;">
          <table style="min-width: 1000px;">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Mobile</th>
                <th>Feedback</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Reply</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $fb->fetch_assoc()): ?>
              <tr>
                <td>
                  <div style="font-weight: 600; color: #2d3748;"><?= htmlspecialchars($row['username']) ?></div>
                  <div style="font-size: 0.85rem; color: #718096;">ID: <?= $row['customer_id'] ?></div>
                </td>
                
                <td>
                  <div style="color: #4a5568;"><?= htmlspecialchars($row['mobilenum'] ?? 'N/A') ?></div>
                </td>
                
                <td style="max-width: 300px;">
                  <div class="feedback-message">
                    "<?= htmlspecialchars($row['message']) ?>"
                  </div>
                </td>
                
                <td>
                  <div style="font-weight: 600; color: #4a5568;"><?= date("d M Y", strtotime($row['created_at'])) ?></div>
                  <div style="font-size: 0.85rem; color: #718096;"><?= date("h:i A", strtotime($row['created_at'])) ?></div>
                </td>
                
                <td>
                  <?php if (!empty($row['admin_reply'])): ?>
                    <span class="status-badge status-replied">
                      <i class="fas fa-check-circle"></i> Replied
                    </span>
                  <?php else: ?>
                    <span class="status-badge status-pending">
                      <i class="fas fa-clock"></i> Pending
                    </span>
                  <?php endif; ?>
                </td>
                
                <td>
                  <form action="reply_feedback.php" method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    
                    <?php if (!empty($row['admin_reply'])): ?>
                      <div class="previous-reply">
                        <strong style="color: #2c5282;">Previous:</strong> 
                        <?= htmlspecialchars($row['admin_reply']) ?>
                      </div>
                    <?php endif; ?>
                    
                    <textarea name="reply" required 
                      style="width: 100%; min-height: 80px; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 0.5rem; font-family: inherit;"
                      placeholder="Type your reply here..."><?= htmlspecialchars($row['admin_reply'] ?? '') ?></textarea>
                    <div>
                    <button type="submit" style="width: 100%; padding: 0.5rem; background: #4299e1; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                      <i class="fas fa-paper-plane"></i> 
                      <?= !empty($row['admin_reply']) ? 'Update Reply' : 'Send Reply' ?>
                    </button>
                    </div>
                  </form>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        
        <?php else: ?>
        <!-- No feedback message -->
        <div style="text-align: center; padding: 3rem;">
          <i class="fas fa-comments" style="font-size: 4rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
          <h3 style="color: #4a5568; margin-bottom: 0.5rem;">No Feedback Yet</h3>
          <p style="color: #718096;">Customer feedback will appear here once they submit reviews.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<script>

  
// Update Stock and Limit Function
// Update Stock and Limit Function
function updateStockAndLimit(product_id) {
    let stockValue = document.getElementById("stock" + product_id).value;
    let limitValue = document.getElementById("limit" + product_id).value;

    if (stockValue === "" || stockValue < 0) {
        alert("Invalid stock value");
        return;
    }
    
    if (limitValue === "" || limitValue < 1) {
        alert("Low stock limit must be at least 1");
        return;
    }

    let formData = new FormData();
    formData.append("product_id", product_id);
    formData.append("stock", stockValue);
    formData.append("low_stock_limit", limitValue);

    // Show loading state
    const updateBtn = event.target;
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    updateBtn.disabled = true;

    // Use the new ajax file
    fetch("update_stock_ajax.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("✅ " + data.message);
            location.reload();
        } else {
            alert("❌ Error: " + data.message);
            // Reset button
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;
        }
    })
    .catch(error => {
        alert("❌ Network error: " + error);
        // Reset button
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    });
}

// Update delivery form submission
function updateDeliveryForm(order_id, form) {
    event.preventDefault(); // Prevent default form submission
    
    // Validate time format
    const timeInput = form.querySelector('.time-input');
    if (timeInput.value && timeInput.value.length > 5) {
        alert('Please enter time in HH:MM format (e.g., 09:30, 14:45)');
        timeInput.focus();
        return false;
    }
    
    // Show loading
    const submitBtn = form.querySelector('.update-delivery-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    // Submit form normally
    form.submit();
    return true;
}

// Quick update status function
function quickUpdateStatus(order_id, status) {
    if (!confirm(`Change order #${order_id} status to ${status.toUpperCase()}?`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'quick_update_status.php';
    
    const orderIdInput = document.createElement('input');
    orderIdInput.type = 'hidden';
    orderIdInput.name = 'order_id';
    orderIdInput.value = order_id;
    
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    
    form.appendChild(orderIdInput);
    form.appendChild(statusInput);
    document.body.appendChild(form);
    form.submit();
}

// Switch between sections
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(s => s.style.display = "none");
    
    // Show selected section
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = "block";
    }

    // Update active menu item
    document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove("active"));
    event.target.closest('a').classList.add("active");

    // Update page title
    let titleMap = {
        'dashboard': 'Dashboard',
        'orders_section': 'Manage Orders',
        'feedback_section': 'Customer Feedback',
        'stock_section': 'Manage Stock',
        'product_section': 'Manage Products'
    };
    
    document.getElementById("page-title").innerText = titleMap[sectionId] || 'Dashboard';
}

// Logout
function logout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
    }
}

// Toggle Sidebar for Mobile
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.querySelector('.sidebar');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (window.innerWidth <= 1024 && 
        sidebar.classList.contains('active') && 
        !sidebar.contains(event.target) && 
        !menuToggle.contains(event.target)) {
        sidebar.classList.remove('active');
    }
});

// Edit Product Function
function editMaterial(id, name, price, stock, unit, image, lowStockLimit) {
    document.getElementById("product_id").value = id;
    document.querySelector("[name='material_name']").value = name;
    document.querySelector("[name='price']").value = price;
    document.querySelector("[name='stock']").value = stock;
    document.querySelector("[name='unit_type']").value = unit;
    document.querySelector("[name='image']").value = image;
    
    // Set low stock limit if the field exists
    const limitField = document.querySelector("[name='low_stock_limit']");
    if (limitField && lowStockLimit) {
        limitField.value = lowStockLimit;
    }
    
    // Scroll to form
    document.querySelector('#product_section .card:first-child').scrollIntoView({ behavior: 'smooth' });
}

// Show success/error messages
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('updated')) {
        const orderId = urlParams.get('order_id');
        alert(`✅ Order #${orderId} delivery information updated successfully!`);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (urlParams.has('status_updated')) {
        const orderId = urlParams.get('order_id');
        const status = urlParams.get('status');
        alert(`✅ Order #${orderId} status changed to ${status.toUpperCase()}!`);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (urlParams.has('feedback_replied')) {
        alert('✅ Reply sent successfully!');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (urlParams.has('error')) {
        const error = urlParams.get('error');
        if (error === 'empty_reply') {
            alert('❌ Reply cannot be empty!');
        } else if (error === 'db_error') {
            alert('❌ Database error occurred. Please try again.');
        } else if (error === 'no_record') {
            alert('❌ Feedback record not found.');
        }
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// Add to Stock Function with Email Notification
function addToStock(product_id, currentStock, unitType) {
    let addValue = document.getElementById("add_stock_" + product_id).value;
    let reorderLevel = document.getElementById("reorder_" + product_id).value;

    // Validate add value
    if (addValue === "" || addValue < 0) {
        alert("Please enter a valid quantity to add (0 or more)");
        return;
    }
    
    // Validate reorder level
    if (reorderLevel === "" || reorderLevel < 1) {
        alert("Reorder level must be at least 1");
        return;
    }

    // Calculate new stock value (current + added)
    let newStock = parseInt(currentStock) + parseInt(addValue);
    
    // Check if stock will be below reorder level after update
    let warningMessage = "";
    if (newStock < reorderLevel) {
        warningMessage = `\n\n⚠️ WARNING: Stock will be BELOW reorder level (${reorderLevel} ${unitType})!\nAn email alert will be sent.`;
    }
    
    // Confirm with user
    if (!confirm(`Add ${addValue} ${unitType} to ${document.querySelector(`#reorder_${product_id}`).closest('tr').querySelector('td:nth-child(2)').innerText}?\n\n` +
                 `Current Stock: ${currentStock} ${unitType}\n` +
                 `New Stock: ${newStock} ${unitType}\n` +
                 `Reorder Level: ${reorderLevel} ${unitType}${warningMessage}`)) {
        return;
    }

    let formData = new FormData();
    formData.append("product_id", product_id);
    formData.append("stock", newStock);
    formData.append("low_stock_limit", reorderLevel);

    // Show loading state
    const updateBtn = event.target;
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    updateBtn.disabled = true;

    fetch("update_stock_ajax.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            let message = data.message;
            
            // Show email status if applicable
            if (data.email_status === 'sent') {
                message += '\n\n📧 Low stock alert email sent to admin!';
            } else if (data.email_status === 'failed') {
                message += '\n\n⚠️ Warning: Stock is below limit but email could not be sent.';
            }
            
            alert(message);
            location.reload();
        } else {
            alert("❌ Error: " + data.message);
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;
        }
    })
    .catch(error => {
        alert("❌ Network error: " + error);
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    });
}
</script>
</body>
  </html>