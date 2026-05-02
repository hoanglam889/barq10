    <?php
include('include/config.php');
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$customer_id = (int)($_GET['id']);

try {
    $stmt = $conn->prepare(
        "SELECT customer_id, customer_name, customer_phone
         FROM customer 
         WHERE customer_id = :id
         LIMIT 1"
    );
    $stmt->execute([':id' => $customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($customer ?: null);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
