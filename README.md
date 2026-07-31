
---

# 🚀 RotaJá — Plataforma de Entregas Expressas

O **RotaJá** é uma aplicação web desenvolvida em **Laravel** projetada para conectar empresas/estabelecimentos comerciais a entregadores parceiros de forma rápida, eficiente e simplificada.

---

## 🛠️ Tecnologias Utilizadas

* **Backend:** PHP 8.3+ | Laravel 13.x
* **Frontend:** Blade Templates, Bootstrap 5, Lucide Icons
* **Banco de Dados:** MySQL / MariaDB
* **Autenticação:** Custom Auth (Tabela `usuarios`)

---

## 📌 Funcionalidades Principais

### 🏢 Módulo da Empresa

* Cadastro de empresa integrado ao perfil de acesso.
* Solicitação e acompanhamento de entregas em tempo real.
* Histórico de pedidos realizados.
* Gestão e atualização do perfil.

### 🛵 Módulo do Entregador

* Cadastro de entregador parceiro (com seleção de veículo: Moto, Carro, Bicicleta).
* Painel do entregador com lista de corridas disponíveis na região.
* Aceite e gerenciamento de entregas em andamento.

---

## 🚀 Como Rodar o Projeto Localmente

### 1️⃣ Pré-requisitos

Certifique-se de ter instalado em sua máquina:

* [PHP 8.3+](https://www.php.net/)
* [Composer](https://getcomposer.org/)
* [MySQL](https://www.mysql.com/)
* [Node.js & NPM](https://nodejs.org/) *(Opcional, caso utilize compiladores de assets)*

---

### 2️⃣ Passo a Passo

1. **Clonar o repositório:**
```bash
git clone https://github.com/seu-usuario/rotaja-web.git
cd rotaja-web

```


2. **Instalar as dependências do PHP:**
```bash
composer install

```


3. **Configurar as Variáveis de Ambiente:**
Copie o arquivo `.env.example` para `.env`:
```bash
cp .env.example .env

```


4. **Gerar a chave da aplicação:**
```bash
php artisan key:generate

```


5. **Configurar o Banco de Dados:**
Abra o arquivo `.env` e configure suas credenciais do MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=entregas
DB_USERNAME=root
DB_PASSWORD=sua_senha

```


6. **Executar as Migrations:**
```bash
php artisan migrate

```


7. **Iniciar o Servidor Local:**
```bash
php artisan serve

```


Acesse a aplicação em: `http://localhost:8000`

---

## 🗄️ Estrutura do Banco de Dados

A arquitetura baseia-se em uma tabela centralizada de acessos (`usuarios`) vinculada às tabelas de perfis específicos:

* **`usuarios`**: Guarda as credenciais de login (`email`, `senha`, `cargo`, `telefone`).
* **`empresas`**: Guarda os dados específicos do estabelecimento (`cnpj`, `usuario_id`).
* **`entregadores`**: Guarda os dados específicos do entregador (`cpf`, `tipo_veiculo`, `usuario_id`).
* **`entregas`**: Gerencia as solicitações de entrega, status e vínculo entre empresa e entregador.

---

## 📄 Licença

Este projeto é de uso acadêmico/pessoal. Sinta-se à vontade para contribuir com melhorias!