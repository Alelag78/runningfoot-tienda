<?php
class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crear un pedido
     * @param int $userId ID del usuario
     * @param array $cart Carrito con items, cantidad y talla
     * @return int ID del pedido creado
     */
    public function createOrder($userId, $cart) {
        // Calcular total del pedido
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Insertar pedido en tabla 'orders'
        $stmt = $this->pdo->prepare(
            "INSERT INTO orders (user_id, total) VALUES (:user_id, :total)"
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':total'   => $total
        ]);

        $orderId = $this->pdo->lastInsertId();

        // Insertar cada producto en 'order_items', incluyendo talla
        $stmtItem = $this->pdo->prepare(
            "INSERT INTO order_items (order_id, item_id, quantity, unit_price, size_id) 
             VALUES (:order_id, :item_id, :quantity, :unit_price, :size_id)"
        );

        foreach ($cart as $item) {
            $stmtItem->execute([
                ':order_id'   => $orderId,
                ':item_id'    => $item['id'],
                ':quantity'   => $item['quantity'],
                ':unit_price' => $item['price'],
                ':size_id'    => $item['size_id'] // Almacenar talla
            ]);
        }

        return $orderId;
    }
}
?>