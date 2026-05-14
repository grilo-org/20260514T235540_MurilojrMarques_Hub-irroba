# HUB de Integração SQS

API construída em Laravel 11 para recebimento e processamento assíncrono de atualizações de produtos (estoque, preço, descrição, imagens e tags) via Amazon SQS.

Este projeto foi desenhado com foco em alta disponibilidade e resiliência, simulando um ambiente real de e-commerce onde integrações de ERPs disparam milhares de requisições por minuto.

---

# Decisões de Arquitetura

Para garantir a estabilidade do servidor durante picos de tráfego, algumas decisões arquiteturais foram tomadas:

- **Segurança e Autenticação:**  
  A API é estritamente protegida utilizando Laravel Sanctum (Bearer Tokens). Apenas sistemas autorizados (ERPs integrados) com tokens válidos conseguem interagir com os endpoints.

- **Processamento Assíncrono (Status 202):**  
  A API atua apenas como um "mensageiro". Ela valida a integridade do payload e despacha a tarefa para a AWS SQS, retornando `202 Accepted`. O banco de dados não é consultado na requisição principal, evitando gargalos de I/O.

- **Idempotência:**  
  O sistema de filas garante que o mesmo payload não seja processado duas vezes. Foi implementada uma trava (`ShouldBeUnique`) gerando um hash MD5 baseado no SKU, tipo de atualização e dados enviados.

- **Tolerância a Falhas e Logs:**  
  Jobs que falham devido a regras de negócio (ex: SKU não encontrado) são tentados 3 vezes com `backoff` exponencial. Após isso, são direcionados para a tabela `failed_jobs` e um alerta crítico é gravado nos logs da aplicação para análise da equipe de infraestrutura.

- **Rate Limiting:**  
  As rotas da API estão protegidas pelo middleware `throttle` (`600 requisições/minuto`) para evitar que falhas em ERPs de clientes causem DDoS interno no HUB.

- **Prevenção de Injeção:**  
  O framework lida com a proteção de SQL Injection nativamente via Eloquent, reforçado por regras estritas nos `FormRequests` que tipam e limitam os dados de entrada.

---

# Pré-requisitos

- Docker e Docker Compose instalados.
- Conta na AWS com uma fila SQS criada e chaves de acesso (IAM) geradas.

---

# Como configurar e rodar o projeto

## 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/hub-integracao-sqs.git
cd hub-integracao-sqs
```

---

## 2. Crie o arquivo de ambiente

```bash
cp .env.example .env
```

Preencha as credenciais da AWS SQS no arquivo `.env`:

```env
AWS_ACCESS_KEY_ID=sua_chave
AWS_SECRET_ACCESS_KEY=seu_secret
AWS_DEFAULT_REGION=us-east-1
SQS_PREFIX=sua_url_base_sqs
SQS_QUEUE=sua_url_completa_da_fila
```

---

## 3. Suba os containers Docker

```bash
docker-compose up -d
```

---

## 4. Instale as dependências e gere a chave da aplicação

```bash
docker exec -it hub-irroba-app composer install

docker exec -it hub-irroba-app php artisan key:generate
```

---

## 5. Rode as migrations e popule o banco

```bash
docker exec -it hub-irroba-app php artisan migrate --seed
```

---

# Gerando o Token de Autenticação (Bearer Token)

Para consumir a API, é necessário gerar um token de acesso para simular o sistema ERP.

## Abra o Tinker do Laravel

```bash
docker exec -it hub-irroba-app php artisan tinker
```

## Crie um usuário de integração e gere o token

```php
$user = App\Models\User::create([
    'name' => 'Irroba',
    'email' => 'irroba@teste.com',
    'password' => bcrypt('senha123')
]);

echo $user->createToken('TokenERP')->plainTextToken;
```

Copie o token gerado no terminal e utilize-o no header:

```http
Authorization: Bearer {token}
```

Ou utilize diretamente na interface do Swagger.

Para sair do Tinker:

```bash
exit
```

---

# Iniciando as Filas e Agendamentos

## Iniciar o Worker da fila SQS

> Altere o nome da fila caso utilize outra configuração.

```bash
docker exec -it hub-irroba-app php artisan queue:work sqs --queue=hub-irroba-produtos
```

---

## Rodar agendamentos manualmente (Opcional)

A aplicação possui rotinas agendadas para limpeza de falhas e logs.

```bash
docker exec -it hub-irroba-app php artisan schedule:run
```

---

# Testes Automatizados

A aplicação possui testes cobrindo:

- Validações da API
- Segurança contra injeção de dados
- Despacho de filas (`Queue::fake`)
- Idempotência

Execute a suíte de testes:

```bash
docker exec -it hub-irroba-app php artisan test
```

---

# Documentação da API (Swagger)

A documentação interativa da API foi gerada utilizando OpenAPI/Swagger.

## Gerar ou atualizar a documentação

```bash
docker exec -it hub-irroba-app php artisan l5-swagger:generate
```

## Acessar a documentação

```txt
http://localhost:8000/api/documentation
```

---