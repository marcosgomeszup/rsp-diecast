<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="RSP Diecast – Racing Collection. Sistema em desenvolvimento." />
  <title>RSP Diecast | Racing Collection</title>

  <style>
    :root {
      --azul-escuro: #00205B;
      --azul-claro: #00AEEF;
      --branco: #FFFFFF;
      --cinza: #9FA3A9;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Montserrat", sans-serif;
    }

    body {
      background: var(--azul-escuro);
      color: var(--branco);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
      text-align: center;
    }

    .logo {
      max-width: 340px;
      margin-bottom: 40px;
      animation: fadeIn 1.2s ease-in-out;
      filter: drop-shadow(0 0 10px rgba(0, 174, 239, 0.3));
    }

    p {
      font-size: 1.3rem;
      color: var(--azul-claro);
      font-weight: 600;
      letter-spacing: 0.5px;
      animation: fadeIn 2s ease-in-out;
    }

    footer {
      position: absolute;
      bottom: 25px;
      font-size: 0.85rem;
      color: var(--cinza);
      letter-spacing: 0.5px;
    }

    footer a {
      color: var(--azul-claro);
      text-decoration: none;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <!-- LOGO CENTRAL -->
  <img src="docs/imagens/logo.png" alt="Logo RSP Diecast" class="logo" />

  <p>Sistema em desenvolvimento.</p>

  <footer>
    © 2025 RSP Diecast • <a href="mailto:contato@rspdiecast.com.br">contato@rspdiecast.com.br</a>
  </footer>

</body>
</html>
