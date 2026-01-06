<?php

require_once __DIR__ . '/../models/User.php';

class AuthController {

    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);

        // Iniciar sesión solo si no hay sesión activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Registrar usuario
    public function register($name, $email, $password) {
        // Rol por defecto: cliente
        $role = "cliente";

        // Crear usuario en la base de datos
        $this->userModel->create($role, $name, $email, $password);

        return true;
    }

    // Login
    public function login($email, $password) {
        $user = $this->userModel->getByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Guardar datos en sesión
            $_SESSION['user'] = [
                'id' => $user['id'],
                'role' => $user['role'],
                'name' => $user['name']
            ];
            return true;
        }

        return false;
    }

    // Logout
    public function logout() {
        session_destroy();
    }
}
?>