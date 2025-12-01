# Manual de Instalação e Configuração do Sistema ERP/CRM

Este manual detalha os passos necessários para instalar e configurar o sistema corrigido em seu ambiente de desenvolvimento ou produção.

## 1. Pré-requisitos

Certifique-se de que seu ambiente de hospedagem atende aos seguintes requisitos:

*   **Servidor Web:** Apache, Nginx ou similar.
*   **PHP:** Versão 7.4 ou superior (Recomendado PHP 8.x).
*   **Banco de Dados:** MySQL ou MariaDB.
*   **Extensões PHP:** `pdo_mysql` (geralmente ativas por padrão).

## 2. Configuração dos Arquivos do Sistema

1.  **Descompactar os Arquivos:**
    *   Descompacte o arquivo `sistema_corrigido.zip` que você recebeu.
    *   Você encontrará os arquivos principais (`index.php`, `core.php`, `Bancodedados.sql`) e a pasta `views`.

2.  **Transferir para o Servidor:**
    *   Transfira todos os arquivos e pastas descompactados para o diretório raiz do seu servidor web (ex: `htdocs`, `www` ou `public_html`).

3.  **Ajustar Credenciais do Banco de Dados (`core.php`):**
    *   Abra o arquivo `core.php` em um editor de texto.
    *   Localize a seção de **CONFIGURAÇÕES & CONSTANTES** (próximo à linha 13) e ajuste as credenciais do banco de dados conforme o seu ambiente:

    ```php
    // ----------------------------------------------------
    // 1. CONFIGURAÇÕES & CONSTANTES
    // ----------------------------------------------------
    define('SITE_NAME', 'DespachoSys PRO (ERP/CRM)'); // Nome do seu sistema
    define('DB_HOST', 'localhost'); // Host do seu banco de dados
    define('DB_NAME', 'teste2'); // Nome do banco de dados (será criado no passo 3)
    define('DB_USER', 'root'); // Usuário do banco de dados
    define('DB_PASS', 'root'); // Senha do banco de dados
    // ----------------------------------------------------
    ```

## 3. Configuração do Banco de Dados

Você precisará de acesso a uma ferramenta de gerenciamento de banco de dados (como phpMyAdmin, MySQL Workbench ou linha de comando).

1.  **Criar o Banco de Dados:**
    *   Crie um novo banco de dados com o nome que você definiu na constante `DB_NAME` no arquivo `core.php` (ex: `teste2`).

2.  **Importar o Esquema e Dados:**
    *   Selecione o banco de dados recém-criado.
    *   Importe o arquivo `Bancodedados.sql`. Este arquivo contém a estrutura de todas as tabelas e dados iniciais, incluindo o usuário administrador.

## 4. Acesso Inicial ao Sistema

Após a conclusão dos passos 2 e 3, o sistema estará pronto para uso.

1.  **Acessar o Sistema:**
    *   Abra seu navegador e acesse a URL onde você instalou os arquivos (ex: `http://localhost/` ou `http://seusistema.com.br/`).

2.  **Credenciais de Acesso (Administrador Padrão):**
    *   O arquivo `Bancodedados.sql` inclui um usuário administrador padrão para o primeiro acesso:
        *   **Email:** `info@kld.com.br`
        *   **Senha:** `123456` (Esta senha é um hash no banco de dados, mas o valor de texto simples é `123456`).

    **IMPORTANTE:** Por questões de segurança, acesse o menu **ADMINISTRAÇÃO > Usuários** ou **ADMINISTRAÇÃO > Perfil** imediatamente após o login e altere a senha do usuário administrador.

## 5. Verificação Pós-Instalação

*   Verifique se o menu de navegação lateral está completo, incluindo todos os módulos (WMS & LOGÍSTICA, CRM & VENDAS, FINANCEIRO, ADMINISTRAÇÃO).
*   Acesse **ADMINISTRAÇÃO > Configurações** para verificar e ajustar as permissões de acesso ao menu para os diferentes perfis de usuário (`admin`, `despachante`, `visualizador`).
*   Teste o cadastro de um novo item em cada módulo (ex: Mercadorias, Clientes, Contas a Pagar) para confirmar que a conexão com o banco de dados e as funções de CRUD estão operando corretamente.
