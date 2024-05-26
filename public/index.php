<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Casa de Cambio</title>
<script src="https://accounts.google.com/gsi/client" async></script>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}
h1 {
    color: #333;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    text-align: left;
}
.form-group input, .form-group select {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
}
.btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px;
    cursor: pointer;
    width: 100%;
    border-radius: 5px;
    font-size: 16px;
}
.btn:hover {
    background-color: #0056b3;
}
.btn.red {
    background-color: #ff0044;
}
.btn.red:hover {
    background-color: #d50a3f;
}
.result {
    margin: 15px 0;
    font-size: 18px;
    font-weight: bold;
}
.picture {
    display: block;
    margin: 5px auto;
}
.info {
    text-align: left;
    border-radius: 8px;
    background-color: #F0F0F0;
    padding: 5px;
    margin-bottom: 10px;
}
</style>
</head>
<body>
<div class="container">
    <h1>Casa de Cambio</h1>
    <p>1 USD = 3.8 PEN</p>
    <form id="exchangeForm">
        <div class="form-group">
            <label for="amount">Cantidad</label>
            <input type="number" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="currencyPair">Moneda</label>
            <select id="currencyPair" name="currencyPair">
                <option value="usdpen">Dólares a Soles</option>
                <option value="penusd">Soles a Dólares</option>
            </select>
        </div>
        <button type="button" class="btn" onclick="convertCurrency()">Convertir</button>
    </form>
    <div class="result" id="result"></div>

<?php
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
?>
    <div class="info">
        <strong>Bienvenido(a):</strong><br>
        <img class="picture" src="<?=$user['picture']?>" alt="<?=$user['google_name']?>" width="48" height="48">
        <strong>Nombre:</strong> <?=$user['first_name']?> <br />
        <strong>Apellido:</strong> <?=$user['last_name']?> <br />
        <strong>Email:</strong> <?=$user['email']?> <br />
        <strong>Google ID:</strong> <code><?=$user['google_id']?></code> <br />
    </div>
    <button type="button" class="btn red" onclick="logout()">Cerrar Sesión</button>
<?php } else { ?>
    <!-- Si no hay sesión, se muestra el botón de login con google -->
    <div id="g_id_onload"
         data-client_id="<?=$_ENV['GOOGLE_CLIENT_ID']?>"
         data-context="use"
         data-ux_mode="popup"
         data-login_uri="<?=$_ENV['APP_HOST']?>/login.php"
         data-itp_support="false">
    </div>
    <div class="g_id_signin"
         data-type="standard"
         data-shape="rectangular"
         data-theme="filled_blue"
         data-text="signin_with"
         data-size="large"
         data-locale="es-419"
         data-logo_alignment="left"
         data-width="300">
    </div>
<?php } ?>
</div>

<script>
function convertCurrency() {
  const amount = document.getElementById('amount').value;
  const currencyPair = document.getElementById('currencyPair').value;
  const result = document.getElementById('result');

  let convertedAmount;

  // Tasas de cambio ficticias
  const usdToPenRate = 3.8;
  const penToUsdRate = 1 / usdToPenRate;

  if (currencyPair === 'usdpen') {
    convertedAmount = amount * usdToPenRate;
    result.innerText = `${amount} USD son ${convertedAmount.toFixed(2)} PEN.`;
  } else {
    convertedAmount = amount * penToUsdRate;
    result.innerText = `${amount} PEN son ${convertedAmount.toFixed(2)} USD.`;
  }
}

function logout() {
  window.location.href = '/logout.php';
}
</script>
</body>
</html>
