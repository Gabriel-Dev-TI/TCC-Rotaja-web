
---

# RotaJá — Plataforma de Entregas

O **RotaJá** é uma aplicação web desenvolvida em **Laravel** projetada para conectar empresas/estabelecimentos comerciais a entregadores parceiros de forma rápida, eficiente e simplificada.

[Rota já]([https://exemplo.com](https://rotaja.wasmer.app/)) <https://rotaja.wasmer.app/>

---

## 🛠️ Tecnologias Utilizadas

* **API's:** 
    * **`ArcGIS`**: Pega as cordenadas do endereço
    * **`ViaCEP`**: Pega os dados do endereço pelo CEP
    * **`Leaflet`**:controla o mapa na página
    * **`OpenStreetMap`**: fornece as imagens do mapa
    * **`OSRM`**: calcula a rota pelas ruas entre origem e destino
    
    
* **Banco de Dados:** MySQL 
* **Autenticação:** Breeze

---

## 📌 Funcionalidades Principais

### Módulo de todos os Usuários

* Login
* Cadastro (Exeto para o Administrador)
* Dashboard
* Visualização e edição do perifl
* Histórico de pedidos


## 🛵 Módulo do Entregador 

* Painel do entregador com lista de corridas disponíveis.
* Aceite e gerenciamento de entregas em andamento.

## 🏢 Módulo do Empresa

* Painel da empresa com lista de entregas em andamento.
* Páginas para a criação de entregas e cadastro de endereços.

---

## 🗄️ Estrutura do Banco de Dados

A arquitetura baseia-se em uma tabela centralizada de acessos (`usuarios`) vinculada às tabelas de perfis específicos:

* **`usuarios`**: Guarda as credenciais de login (`email`, `senha`, `cargo`, `telefone`).
* **`empresas`**: Guarda os dados específicos do estabelecimento (`cnpj`, `usuario_id`).
* **`entregadores`**: Guarda os dados específicos do entregador (`cpf`, `tipo_veiculo`, `usuario_id`).
* **`entregas`**: Gerencia as solicitações de entrega, status e vínculo entre empresa e entregador.
* **`enderecos`**: Guarda os endereços das empresas e das entregas.

---

## 📄 Licença

Este projeto é de uso acadêmico/pessoal. Sinta-se à vontade para contribuir com melhorias!
