<?php

require_once __DIR__ . '/../models/Item.php';

class CatalogController {

    private $itemModel;

    public function __construct($pdo) {
        $this->itemModel = new Item($pdo);
    }

    // Obtener todos los artículos
    public function getAllItems() {
        return $this->itemModel->getAll();
    }

    // Obtener un artículo por ID
    public function getItemById($id) {
        return $this->itemModel->getById($id);
    }
}
