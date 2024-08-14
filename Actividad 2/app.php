<?php
// Datos del cajero automático
define('USUARIO_VALIDO', 'GENERICO');
define('PIN_VALIDO', '4326');
define('SALDO_INICIAL', 10000); // Define un saldo inicial en pesos

// Variables para almacenar el saldo y el estado de la sesión
session_start();
if (!isset($_SESSION['saldo'])) {
    $_SESSION['saldo'] = SALDO_INICIAL;
}

// Función para autenticar al usuario
function autenticarUsuario($usuario, $pin) {
    return $usuario === USUARIO_VALIDO && $pin === PIN_VALIDO;
}

// Función para procesar las transacciones
function procesarTransaccion($tipo, $monto) {
    if ($tipo === 'extraccion') {
        if ($monto <= $_SESSION['saldo']) {
            $_SESSION['saldo'] -= $monto;
            return "Extracción exitosa. Saldo actual: " . $_SESSION['saldo'] . " pesos.";
        } else {
            return "Fondos insuficientes. Saldo actual: " . $_SESSION['saldo'] . " pesos.";
        }
    } elseif ($tipo === 'deposito') {
        $_SESSION['saldo'] += $monto;
        return "Depósito exitoso. Saldo actual: " . $_SESSION['saldo'] . " pesos.";
    } else {
        return "Tipo de transacción no válido.";
    }
}

// Procesamiento de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $pin = $_POST['pin'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $monto = floatval($_POST['monto'] ?? 0);

    if (empty($usuario) || empty($pin)) {
        echo "Por favor, ingrese usuario y pin.";
    } elseif (!autenticarUsuario($usuario, $pin)) {
        echo "El pin o usuario es incorrecto.";
    } elseif ($tipo !== 'extraccion' && $tipo !== 'deposito') {
        echo "Tipo de transacción no válido.";
    } elseif ($monto <= 0) {
        echo "El monto debe ser mayor a cero.";
    } else {
        echo procesarTransaccion($tipo, $monto);
    }
} else {
    // Mostrar el formulario de login
    ?>

    <form method="post" action="">
        <h2>Login</h2>
        Usuario: <input type="text" name="usuario" required><br>
        PIN: <input type="password" name="pin" required><br>
        <input type="submit" value="Ingresar">
    </form>

    <?php
    if (isset($_SESSION['saldo'])) {
        ?>

        <form method="post" action="">
            <h2>Operaciones</h2>
            Tipo de transacción:
            <select name="tipo" required>
                <option value="extraccion">Extracción</option>
                <option value="deposito">Depósito</option>
            </select><br>
            Monto: <input type="number" step="0.01" name="monto" required><br>
            <input type="submit" value="Realizar Transacción">
        </form>

        <?php
    }
}
?>
