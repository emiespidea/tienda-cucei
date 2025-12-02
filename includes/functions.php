<?php
/* includes/functions.php */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escapar HTML (Seguridad XSS)
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

// Redirección segura
function redirect($url) {
    if (strpos($url, 'http') !== 0 && strpos($url, '/') !== 0) {
        $url = BASE_URL . $url;
    }
    header("Location: $url");
    exit();
}

// Verificar login
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Mensajes Flash
function setMsg($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

function displayMsg() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'];
        echo '<div class="alert alert-'.$type.' alert-dismissible fade show">'.e($_SESSION['flash']['msg']).'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['flash']);
    }
}
?>