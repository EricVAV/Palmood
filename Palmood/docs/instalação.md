# Instalação do Palmood
## Requisitos
- XAMPP 8.2 ou superior (Apache + PHP 8.2 + MySQL/MariaDB)
- Navegador atualizado (Chrome, Firefox ou Edge)
## Passo a passo
1. Abra o XAMPP Control Panel e clique em **Start** no Apache e no MySQL.
2. Copie a pasta `Palmood` para `C:\xampp\htdocs\`.
3. Acesse http://localhost/phpmyadmin
4. Clique em **Novo** e crie o banco `Palmood`
(agrupamento `utf8mb4_general_ci`).
5. Com o banco selecionado, vá em **Importar** e escolha
`Palmood/palmood.sql`. Clique em **Executar**.
6. Acesse http://localhost/Palmood/index.php

## Teste
Entre com eric@email.com / 123456. Se a grade de produtos aparecer,
está tudo certo.
