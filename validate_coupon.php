<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$coupon_code = sanitizeInput($_POST['coupon_code'] ?? '');
$total_amount = floatval($_POST['total_amount'] ?? 0);

if (!$coupon_code) {
    echo json_encode(['success' => false, 'message' => 'Please enter a coupon code']);
    exit;
}

try {
    // Validate coupon
    $stmt = $pdo->prepare("
        SELECT * FROM coupons 
        WHERE code = ? AND status = 'active' 
        AND CURDATE() BETWEEN valid_from AND valid_until
        AND (usage_limit IS NULL OR used_count < usage_limit)
    ");
    $stmt->execute([$coupon_code]);
    $coupon = $stmt->fetch();
    
    if (!$coupon) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code']);
        exit;
    }
    
    // Check minimum amount requirement
    if ($total_amount < $coupon['min_amount']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Minimum booking amount of $' . number_format($coupon['min_amount'], 2) . ' required for this coupon'
        ]);
        exit;
    }
    
    // Calculate discount
    $discount_amount = 0;
    if ($coupon['discount_type'] === 'percentage') {
        $discount_amount = ($total_amount * $coupon['discount_value']) / 100;
        if ($coupon['max_discount'] && $discount_amount > $coupon['max_discount']) {
            $discount_amount = $coupon['max_discount'];
        }
    } else {
        $discount_amount = $coupon['discount_value'];
    }
    
    // Ensure discount doesn't exceed total amount
    if ($discount_amount > $total_amount) {
        $discount_amount = $total_amount;
    }
    
    $final_amount = $total_amount - $discount_amount;
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Coupon applied successfully! You saved $' . number_format($discount_amount, 2),
        'coupon_name' => $coupon['name'],
        'discount_amount' => $discount_amount,
        'final_amount' => $final_amount,
        'discount_type' => $coupon['discount_type'],
        'discount_value' => $coupon['discount_value']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error validating coupon: ' . $e->getMessage()]);
}
?>
