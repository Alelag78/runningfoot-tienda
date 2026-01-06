<?php

require_once __DIR__ . '/../models/User.php';

class UserController {

    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    // Obtener lista completa
    public function getAllUsers() {
        return $this->userModel->getAll();
    }

    // Obtener usuario por ID
    public function getUser($id) {
        return $this->userModel->findById($id);
    }

    // Crear usuario
    public function createUser($role, $name, $email, $password) {
        return $this->userModel->create($role, $name, $email, $password);
    }

    // Actualizar usuario
    public function updateUser($id, $role, $name, $email) {
        return $this->userModel->update($id, $role, $name, $email);
    }

    // Eliminar usuario
    public function deleteUser($id) {
        return $this->userModel->delete($id);
    }
}
