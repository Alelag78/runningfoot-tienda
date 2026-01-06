<?php

class Item {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todos los productos
    public function getAll() {
        $stmt = $this->pdo->query(
            "SELECT 
                 i.id,
                 i.name,
                 i.description,
                 i.brand,
                 i.price,
                 i.image,
                 COALESCE(SUM(s.stock), 0) AS total_stock
        FROM items i
        LEFT JOIN item_sizes s ON i.id = s.item_id
        GROUP BY i.id
        ORDER BY i.id DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un producto por ID
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, description, brand, price, stock, image FROM items WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo producto
    public function create($name, $description, $brand, $price, $stock, $image = null) {
    $stmt = $this->pdo->prepare(
        "INSERT INTO items (name, description, brand, price, stock, image, created_at, updated_at)
         VALUES (:name, :description, :brand, :price, :stock, :image, NOW(), NOW())"
    );

    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':brand' => $brand,
        ':price' => $price,
        ':stock' => $stock,
        ':image' => $image
    ]);

    return $this->pdo->lastInsertId();
}


    // Actualizar un producto existente
    public function update($id, $name, $description, $brand, $price, $stock, $image = null) {
        $stmt = $this->pdo->prepare(
            "UPDATE items 
             SET name = :name, description = :description, brand = :brand, price = :price, stock = :stock, image = :image, updated_at = NOW()
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':description' => $description,
            ':brand' => $brand,
            ':price' => $price,
            ':stock' => $stock,
            ':image' => $image
        ]);
    }

    // Eliminar un producto
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>