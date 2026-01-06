<?php 
require_once __DIR__ . '/../models/Order.php';

class OrdersController {
    private $pdo;
    private $orderModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
    }

    /**
     * Borrar pedido por ID
     */
    public function deleteOrder($orderId) {
        // Borrar primero los items del pedido
        $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);

        // Luego borrar el pedido
        $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
    }

    /**
     * Obtener todos los pedidos con detalles, incluyendo talla del producto
     */
    public function getAllOrders() {
    $sql = "SELECT o.id AS order_id, o.user_id, o.total, o.created_at, u.name AS user_name, u.email AS user_email
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.id DESC";
    $stmt = $this->pdo->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as &$order) {
        $stmtItems = $this->pdo->prepare(
            "SELECT oi.quantity, oi.unit_price, i.name, s.size
             FROM order_items oi
             JOIN items i ON oi.item_id = i.id
             JOIN item_sizes s ON oi.size_id = s.id
             WHERE oi.order_id = :order_id"
        );
        $stmtItems->execute([':order_id' => $order['order_id']]);
        $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    }

    return $orders;
}
}
?>