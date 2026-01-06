<?php

class ItemSize
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todas las tallas y stock de un producto
     */
    public function getSizesByItem($itemId)
    {
        $sql = "
            SELECT id, size, stock
            FROM item_sizes
            WHERE item_id = :item_id
            ORDER BY size ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':item_id' => $itemId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener el nombre de la talla por su ID
     */
    public function getSizeById($id)
{
    $stmt = $this->pdo->prepare(
        "SELECT size FROM item_sizes WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetchColumn();
}


    /**
     * Actualizar stock de una talla concreta
     */
    public function updateStock($id, $stock)
    {
        $sql = "UPDATE item_sizes SET stock = :stock WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':stock' => $stock
        ]);
    }

    /**
     * Crear una talla nueva para un producto
     */
    public function create($itemId, $size, $stock)
    {
        $sql = "
            INSERT INTO item_sizes (item_id, size, stock)
            VALUES (:item_id, :size, :stock)
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':item_id' => $itemId,
            ':size'    => $size,
            ':stock'   => $stock
        ]);
    }
}