# Painel de Usuários — Plano Técnico

> **Sistema:** ChronosEstudos
> **Stack:** Laravel 12 + PHP 8.2 + MariaDB 10.11, Laravel Breeze 2.4 (Blade), Tailwind CSS v3 + Alpine.js
> **Data do plano:** 08/09/2026
> **Status:** Planejado (pendente de implementação)

---

## 1. Objetivo

Criar um **painel administrativo** acessível **somente por administradores** para gerenciar os usuários do sistema:

- Listar usuários com busca, filtro e paginação.
- Bloquear / ativar contas.
- Editar nome e e-mail.
- Promover / rebaixar usuários a administrador.
- Excluir usuários.
- Visualizar detalhes de um usuário (dados + resumo de estudos).

Além disso, preparar a estrutura de dados para o **controle de pagamento semestral** que será implementado futuramente (sem lógica/interface por enquanto).

---

## 2. Regras de Negócio

### 2.1 Administradores fixos (protegidos)

Os seguintes e-mails são **sempre administradores** e não podem ser modificados por nenhum outro administrador:

| E-mail |
|--------|
| `carloafernandes@gmail.com` |
| `admin@chronos.br` |

Regras aplicáveis aos administradores fixos:

- **Não podem ser bloqueados.**
- **Não podem ser rebaixados** (remover flag de admin).
- **Não podem ser excluídos.**
- **Não podem ter nome/e-mail editados** pelo painel.
- A verificação é **case-insensitive** (e-mail em minúsculas na comparação).

### 2.2 Promoção/rebaixamento de administradores

- Além dos 2 e-mails fixos, outros usuários **podem ser promovidos a admin** (flag `is_admin`).
- Um admin **não pode rebaixar a si mesmo** nem os admins fixos.
- Um admin **não pode bloquear a si mesmo**.

### 2.3 Bloqueio de conta

- O bloqueio **impede apenas novos logins**.
- Sessões ativas continuam funcionando até expirarem.
- Um usuário bloqueado, ao tentar logar, recebe a mensagem **"Sua conta está bloqueada."**.

### 2.4 Pagamento semestral (futuro)

- Nesta fase, será criado **apenas o schema** (campos no banco).
- A lógica de "em dia / inadimplente por semestre" e a interface serão implementadas em uma fase posterior.

---

## 3. Modelo de Dados

### 3.1 Nova migration

**Arquivo:** `database/migrations/XXXX_XX_XX_XXXXXX_add_admin_and_payment_fields_to_users_table.php`

Campos adicionados à tabela `users`:

| Campo | Tipo | Default | Observação |
|-------|------|---------|------------|
| `is_admin` | `boolean` | `false` | Indica se o usuário é administrador |
| `is_active` | `boolean` | `true` | `false` = conta bloqueada |
| `payment_status` | `string(20)` | `'pending'` | Valores futuros: `pending`, `paid`, `overdue` |
| `payment_valid_until` | `date` (nullable) | `null` | Fim do semestre vigente |

### 3.2 Model `User`

**Arquivo:** `app/Models/User.php`

- Adicionar **casts**:
  - `is_admin` → `boolean`
  - `is_active` → `boolean`
  - `payment_valid_until` → `date`
- **NÃO** adicionar os novos campos a `$fillable` (serão definidos via propriedade no controller admin, evitando mass assignment).
- Adicionar helpers:

```php
public function isAdmin(): bool
{
    return $this->is_admin || $this->isProtectedAdmin();
}

public function isProtectedAdmin(): bool
{
    return in_array(Str::lower($this->email), config('chronos.admin_emails'), true);
}

public function isActive(): bool
{
    return $this->is_active;
}
```

### 3.3 Configuração dos e-mails fixos

**Arquivo:** `config/chronos.php`

```php
return [
    'admin_emails' => [
        'carloafernandes@gmail.com',
        'admin@chronos.br',
    ],
];
```

---

## 4. Autorização

### 4.1 Gate global (admin bypass)

**Arquivo:** `app/Providers/AppServiceProvider.php` — método `boot()`:

```php
Gate::before(function (User $user, string $ability) {
    return $user->isAdmin() ? true : null;
});
```

Efeito: administradores passam por **todas** as policies (existentes e futuras), incluindo `SubjectPolicy` e `TopicPolicy`.

### 4.2 Middleware de admin

**Arquivo:** `app/Http/Middleware/EnsureUserIsAdmin.php`

- Retorna **403 Forbidden** caso o usuário não seja admin.

Registro do alias em `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => EnsureUserIsAdmin::class,
    ]);
})
```

### 4.3 Policy de usuário

**Arquivo:** `app/Policies/UserPolicy.php` (auto-descoberta para o model `User`):

| Método | Regra |
|--------|-------|
| `viewAny` | `$user->isAdmin()` |
| `view` | `$user->isAdmin()` |
| `update` | `$user->isAdmin() && !$target->isProtectedAdmin()` |
| `toggleActive` | `$user->isAdmin() && !$target->isProtectedAdmin() && $user->isNot($target)` |
| `toggleAdmin` | `$user->isAdmin() && !$target->isProtectedAdmin() && $user->isNot($target)` |
| `delete` | `$user->isAdmin() && !$target->isProtectedAdmin() && $user->isNot($target)` |

---

## 5. Backend do Painel

### 5.1 Form Request

**Arquivo:** `app/Http/Requests/Admin/UpdateUserRequest.php`

- Valida `name` (required) e `email` (required, e-mail, único ignorando o próprio usuário).
- Mensagens de erro em português.

### 5.2 Controller

**Arquivo:** `app/Http/Controllers/Admin/UserController.php`

| Método | Ação | Autorização |
|--------|------|-------------|
| `index(Request)` | Lista com busca `q` (nome/e-mail), filtro `status` (all/active/blocked/admin) e paginação (15/pág) | `viewAny` |
| `show(User)` | Detalhes + resumo de estudos (disciplinas, tópicos, recursos, revisões pendentes) | `view` |
| `update(UpdateUserRequest, User)` | Atualiza nome/e-mail | `update` |
| `toggleActive(User)` | Inverte `is_active` | `toggleActive` |
| `toggleAdmin(User)` | Inverte `is_admin` | `toggleAdmin` |
| `destroy(User)` | Exclui o usuário | `delete` |

> As chaves estrangeiras já usam `cascadeOnDelete`, então a exclusão remove também disciplinas, tópicos, recursos, revisões e favoritos do usuário.

### 5.3 Rotas

**Arquivo:** `routes/web.php`

```php
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::patch('users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
```

---

## 6. Bloqueio no Login

**Arquivo:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php` — método `store()`:

Após `$request->authenticate()` e antes do `regenerate()` da sessão:

```php
if (! $request->user()->isActive()) {
    Auth::guard('web')->logout();
    throw ValidationException::withMessages([
        'email' => 'Sua conta está bloqueada.',
    ]);
}
```

---

## 7. Interface (Views)

### 7.1 Listagem

**Arquivo:** `resources/views/admin/users/index.blade.php`

- Tabela (baseada no padrão de `resources/views/reports/index.blade.php`).
- Campo de busca (`q`) e abas de filtro por status: **Todos / Ativos / Bloqueados / Admins**.
- Colunas: nome, e-mail, status (ativo/bloqueado), papel (admin/comum), ações.
- Ações inline: ver, editar (modal), bloquear/ativar, promover/rebaixar, excluir (com confirmação e `@method('DELETE')`).
- Paginação com `{{ $users->links() }}`.

### 7.2 Detalhes

**Arquivo:** `resources/views/admin/users/show.blade.php`

- Dados do usuário + badges de status/papel.
- Resumo de estudos: quantidade de disciplinas, tópicos, recursos e revisões pendentes.
- Botões de ação (bloquear/ativar, promover/rebaixar, editar, excluir).

### 7.3 Navegação

Adicionar link condicional **"Administração"** em:

- `resources/views/layouts/sidebar.blade.php` (desktop)
- `resources/views/layouts/bottom-nav.blade.php` (mobile)

```blade
@if (Auth::user()->isAdmin())
    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
        Administração
    </x-nav-link>
@endif
```

---

## 8. Seeder

**Arquivo:** `database/seeders/AdminUserSeeder.php`

- Garante (upsert) os 2 administradores fixos com `is_admin = true` e `is_active = true`.

Chamada em `database/seeders/DatabaseSeeder.php`:

```php
$this->call(AdminUserSeeder::class);
```

---

## 9. Testes

### 9.1 Feature tests

**Arquivo:** `tests/Feature/AdminUserManagementTest.php`

- Não-admin acessando `/admin/users` → **403**.
- Admin bloqueia/ativa usuário.
- Admin promove/rebaixa usuário.
- Não é possível bloquear/rebaixar/excluir os admins fixos.
- Não é possível bloquear/rebaixar a si mesmo.
- Usuário bloqueado não consegue logar (recebe a mensagem correta).

### 9.2 Unit tests

**Arquivo:** `tests/Unit/UserAdminTest.php`

- `isAdmin()` retorna `true` para os e-mails fixos (case-insensitive).
- `isProtectedAdmin()` identifica corretamente os e-mails fixos.
- Usuário comum com `is_admin = true` também é admin.

---

## 10. Arquivos Envolvidos

### Criar

| Arquivo | Finalidade |
|---------|------------|
| `database/migrations/XXXX_..._add_admin_and_payment_fields_to_users_table.php` | Novos campos em `users` |
| `config/chronos.php` | E-mails dos admins fixos |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Middleware de admin |
| `app/Policies/UserPolicy.php` | Regras de autorização do painel |
| `app/Http/Requests/Admin/UpdateUserRequest.php` | Validação de nome/e-mail |
| `app/Http/Controllers/Admin/UserController.php` | Controller do painel |
| `resources/views/admin/users/index.blade.php` | Listagem de usuários |
| `resources/views/admin/users/show.blade.php` | Detalhes do usuário |
| `database/seeders/AdminUserSeeder.php` | Seed dos admins fixos |
| `tests/Feature/AdminUserManagementTest.php` | Testes de funcionalidade |
| `tests/Unit/UserAdminTest.php` | Testes unitários do model |

### Modificar

| Arquivo | Alteração |
|---------|-----------|
| `app/Models/User.php` | Casts + helpers `isAdmin()`, `isProtectedAdmin()`, `isActive()` |
| `app/Providers/AppServiceProvider.php` | `Gate::before()` |
| `bootstrap/app.php` | Alias do middleware `admin` |
| `routes/web.php` | Rotas do grupo `admin.*` |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Bloqueio no login |
| `resources/views/layouts/sidebar.blade.php` | Link condicional "Administração" |
| `resources/views/layouts/bottom-nav.blade.php` | Link condicional "Administração" |
| `database/seeders/DatabaseSeeder.php` | Chamar `AdminUserSeeder` |

---

## 11. Roteiro de Verificação

1. `php artisan migrate` — cria os 4 novos campos na tabela `users`.
2. `php artisan db:seed` — garante os 2 administradores fixos.
3. `php artisan route:list` — confirma as rotas `admin.*` com middleware `auth` + `admin`.
4. `php artisan test` — testes de admin e login passam.
5. **Manual — acesso:**
   - Login como `admin@chronos.br` → link "Administração" visível.
   - Login como usuário comum → sem link; `/admin/users` retorna **403**.
6. **Manual — bloqueio:**
   - Bloquear usuário → mantém a sessão atual, mas não consegue logar novamente.
   - Tentar bloquear/rebaixar/excluir os 2 e-mails fixos → ação negada.
7. **Assets:** se os links de paginação não estilizarem, publicar as views:
   `php artisan vendor:publish --tag=laravel-pagination`.

---

## 12. Próximos Passos (futuro)

1. **Controle de pagamento semestral:**
   - Reusar `payment_status` e `payment_valid_until`.
   - Criar `PaymentService` (cálculo do semestre e status "em dia/inadimplente").
   - Tela admin para marcar usuários como "em dia" por semestre.
   - Regra opcional de bloqueio automático por inadimplência.

2. **Configurabilidade dos e-mails fixos:**
   - Migrar os e-mails de `config/chronos.php` para override via `.env` se necessário.

3. **Experiência de edição:**
   - Manter edição de nome/e-mail via modal Alpine (consistente com o restante do app).

---

## 13. Observações Técnicas

- O projeto **não possui** infraestrutura de roles/permissões hoje — a flag `is_admin` será a base única de autorização.
- As policies existentes (`SubjectPolicy`, `TopicPolicy`) são de **propriedade** (owner) e serão automaticamente ignoradas por admins via `Gate::before`.
- O model `User` **não** implementa `MustVerifyEmail`; a verificação de e-mail está efetivamente desabilitada (rotas existem, mas não são aplicadas).
- Não há paginação em lugar algum do projeto atualmente — esta será a primeira feature a usá-la.
- O padrão visual de tabela a ser seguido está em `resources/views/reports/index.blade.php`.
