<?php
// cerrar_sesion.php
session_start();
session_unset();    // Elimina las variables
session_destroy();  // Destruye la sesión
header('Location: index.php'); // Te manda al login limpio
exit;