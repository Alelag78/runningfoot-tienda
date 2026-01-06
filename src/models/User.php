<?php

class User {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Crear usuario
    public function create($role, $name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (role, name, email, password) 
                VALUES (:role, :name, :email, :password)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':role' => $role,
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        return $this->pdo->lastInsertId();
    }

    // Obtener usuario por ID
    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por email
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los usuarios
    public function getAll() {
        $sql = "SELECT id, role, name, email FROM users ORDER BY id DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar usuario
    public function update($id, $role, $name, $email, $password = null) {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET role = :role, name = :name, email = :email, password = :password WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':role' => $role,
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);
        } else {
            $sql = "UPDATE users SET role = :role, name = :name, email = :email WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':role' => $role,
                ':name' => $name,
                ':email' => $email
            ]);
        }
    }

    // Eliminar usuario
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}