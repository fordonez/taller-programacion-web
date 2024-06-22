<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Casa de Cambio</title>
<link rel="stylesheet" href="./css/styles.css">
<script src="https://accounts.google.com/gsi/client" async></script>
</head>
<body>
<div class="container">
    <h1>Casa de Cambio</h1>
    <p id="prices" style="display:none"></p>
    <form id="exchangeForm">
        <div class="form-group">
            <label for="currencyPair">Moneda</label>
            <select id="currencyPair" name="currencyPair">
                <option value="usdpen">Dólares a Soles</option>
                <option value="penusd">Soles a Dólares</option>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Cantidad</label>
            <input type="number" id="amount" name="amount" required>
        </div>
        <button type="button" class="btn" onclick="convertCurrency()">Calcular</button>
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
const global = {
  price: 1,
}

async function fetchPrices() {
  try {
    const response = await fetch('/prices.php', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    console.log(data);
    return data;
  } catch (error) {
    console.error('Error fetching prices:', error);
  }
}

function convertCurrency() {
  const session = <?=isset($_SESSION['user']) ? 'true' : 'false'; ?>;
  const amount = document.getElementById('amount').value;
  const currencyPair = document.getElementById('currencyPair').value;
  const result = document.getElementById('result');

  let convertedAmount;

  // Tasas de cambio ficticias
  const usdToPenRate = global.price;
  const penToUsdRate = 1 / usdToPenRate;

  const addExchangeButton = pair => {
    let m = '', rate = ''
    if (currencyPair === 'usdpen') {
      convertedAmount = amount * usdToPenRate;
      m = `${amount} USD son ${convertedAmount.toFixed(2)} PEN`;
      rate = usdToPenRate
    } else {
      convertedAmount = amount * penToUsdRate;
      m = `${amount} PEN son ${convertedAmount.toFixed(2)} USD`;
      rate = penToUsdRate
    }

    return session
      ? `<button class="btn exchange"
            data-pair="${pair}"
            data-amount="${amount}"
            data-rate="${rate}"
        >${m}</button>`
      : `${m}`
  }

  result.innerHTML = addExchangeButton(currencyPair)
}

function logout() {
  window.location.href = '/logout.php';
}

document.addEventListener('DOMContentLoaded', async _event => {
  const element = document.getElementById('prices');
  const { price } = await fetchPrices()

  global.price = price
  element.innerText = `1 USD = ${price} PEN`;
  element.removeAttribute('style')
});
</script>
</body>
</html>
