<?php
require_once __DIR__ . '/../models/Item.php';

class ItemController {

    private $itemModel;

    public function __construct($pdo) {
        $this->itemModel = new Item($pdo);
    }

    public function getAllItems() {
        return $this->itemModel->getAll();
    }

    public function getItem($id) {
        return $this->itemModel->getById($id);
    }

    public function createItem($name, $description, $brand, $price, $stock, $image = null) {
        return $this->itemModel->create($name, $description, $brand, $price, $stock, $image);
    }

    public function updateItem($id, $name, $description, $brand, $price, $stock, $image = null) {
        return $this->itemModel->update($id, $name, $description, $brand, $price, $stock, $image);
    }

    public function deleteItem($id)
{
    try {
        return $this->itemModel->delete($id);

    } catch (PDOException $e) {

        // Si es error por clave foránea, devolvemos un mensaje amigable
        if ($e->getCode() == '23000') {
            return "FOREIGN_KEY_BLOCK";
        }

        // Otros errores
        return false;
    }
}

}
?>