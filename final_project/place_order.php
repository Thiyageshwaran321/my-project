<?php
session_start();
// require the DB connection file from the same directory – use __DIR__ for a reliable path
require_once __DIR__ . '/db.php';

// If db.php failed to create $conn, stop early with a helpful message instead of calling methods on null
if (!isset($conn) || !($conn instanceof mysqli)) {
    echo "<h2>Database connection error</h2><p>Could not connect to the database. Please check <code>db.php</code> and the MySQL server.</p>";
    exit();
}

// ✅ Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php"); // redirect to login or homepage
    exit();
}

// ✅ Get logged-in user ID
$customer_id = $_SESSION['customer_id'];

// ✅ Determine product: prefer explicit product_id, otherwise allow a `material` name from older forms
// Accepts GET or POST
$product_id = null;
if (isset($_REQUEST['product_id'])) {
    $product_id = (int)$_REQUEST['product_id'];
} elseif (isset($_REQUEST['material'])) {
    // Try to find a matching product by name (case-insensitive)
    $material_name = trim($_REQUEST['material']);
    if ($material_name !== '') {
        $lookup = "SELECT * FROM products WHERE name = ? LIMIT 1";
        $stmtL = $conn->prepare($lookup);
        $stmtL->bind_param("s", $material_name);
        $stmtL->execute();
        $resL = $stmtL->get_result();
        if ($resL && $resL->num_rows > 0) {
            $prodRow = $resL->fetch_assoc();
            $product_id = (int)$prodRow['id'];
        } else {
            // No product matched the material name
            echo "<script>alert('❌ Product not found for material: " . addslashes($material_name) . "'); window.location.href='material.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('⚠️ No product selected.'); window.location.href='material.php';</script>";
        exit();
    }
}

if ($product_id !== null) {

    // ✅ Fetch product details
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // ✅ Insert order details into the `orders` table
        $insert = "INSERT INTO orders (customer_id, product_id, quantity, total_price, order_date, status)
                   VALUES (?, ?, ?, ?, NOW(), 'Pending')";
        $stmt2 = $conn->prepare($insert);

        // Read quantity from request (product.php sends via GET). Default to 1 if missing or invalid.
        $quantity = 1;
        if (isset($_REQUEST['quantity'])) {
            $q = (int)$_REQUEST['quantity'];
            if ($q > 0) {
                $quantity = $q;
            }
        }

        $total_price = $product['price'] * $quantity;

        $stmt2->bind_param("iiid", $customer_id, $product_id, $quantity, $total_price);
        if ($stmt2->execute()) {
            echo "<script>
                    alert('✅ Order placed successfully!');
                    window.location.href = 'customer_dashboard.php';
                  </script>";
        } else {
            echo "<script>alert('❌ Failed to place order. Try again.');</script>";
        }
    } else {
        echo "<script>alert('❌ Product not found.'); window.location.href='products.php';</script>";
    }
} else {
    // If we reach here, no product id could be determined
    echo "<script>alert('⚠️ No product selected.'); window.location.href='products.php';</script>";
}
?>

<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: /index.php');
    exit;
}
$customer_id = (int)$_SESSION['customer_id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=your_db;charset=utf8mb4','db_user','db_pass',[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Fetch cart items
    $stmt = $pdo->prepare("
        SELECT c.product_id, c.quantity, p.price
        FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.customer_id = ?
    ");
    $stmt->execute([$customer_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($items)) {
        header('Location: /myaccount/cart.php?msg=empty');
        exit;
    }

    // Calculate total
    $total = 0;
    foreach ($items as $it) $total += $it['price'] * $it['quantity'];

    $pdo->beginTransaction();
    $stmtOrder = $pdo->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
    $stmtOrder->execute([$customer_id, $total]);
    $order_id = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($items as $it) {
        $stmtItem->execute([$order_id, $it['product_id'], $it['quantity'], $it['price']]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE customer_id = ?");
    $stmt->execute([$customer_id]);

    $pdo->commit();
    header('Location: /myaccount/order_success.php?order_id='.$order_id);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log($e->getMessage());
    header('Location: /myaccount/cart.php?msg=error');
    exit;
}

