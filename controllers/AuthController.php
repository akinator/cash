<?php
require_once('models/User.php');  // Ajoutez cette ligne au début

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }



    public function login() {
    // Vérifier si l'utilisateur est déjà connecté
    if(isset($_SESSION['user_id'])) {
        header('Location: index.php?action=dashboard');
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if(empty($email) || empty($password)) {
            $_SESSION['error'] = "Tous les champs sont obligatoires";
        } else {
            $result = $this->user->login($email, $password);
            if($result) {
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['role'] = $result['role'];

                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $_SESSION['error'] = "Email ou mot de passe incorrect";
            }
        }
    }
    
    require_once("views/auth/login.php");
}

    public function logout() {
    // Détruire toutes les variables de session
    $_SESSION = array();
    
    // Détruire la session
    session_destroy();
    
    // Rediriger vers la page de login
    header("Location: index.php?action=login");
    exit();
}
}