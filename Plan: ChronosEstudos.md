# Plan: ChronosEstudos — Cronograma com Repetição Espaçada

> **Stack real:** Laravel 12 + MariaDB 10.11 + Docker, PHP 8.2-apache, Tailwind CSS + Alpine.js
> **URL:** http://localhost:8080 | **DB:** `chronosestudos` (user: `chronos`)

---

## Progresso

| Fase | Status |
|------|--------|
| FASE 1 — Ambiente Docker | ✅ Concluído |
| FASE 2 — Projeto Laravel + Autenticação | ✅ Concluído |
| FASE 3 — Migrations e Models | ✅ Concluído |
| FASE 4 — Lógica Repetição Espaçada | ✅ Concluído |
| FASE 5 — Controllers e Rotas | ✅ Concluído |
| FASE 6 — Interface UI/UX | ✅ Concluído |
| FASE 7 — Segurança, Polimento e Verificação | ✅ Concluído |


---

## ✅ FASE 1 — Ambiente Docker

- [x] `docker-compose.yml` — 3 serviços: `web`, `db`, `node`
- [x] `docker/php/Dockerfile` — PHP 8.2-apache + pdo_mysql + zip + Composer 2
- [x] `docker/php/apache.conf` — VirtualHost → `/var/www/html/public`
- [x] `docker/php/php.ini` — timezone `America/Sao_Paulo`
- [x] `.env.example` / `.dockerignore`

## ✅ FASE 2 — Projeto Laravel + Autenticação

- [x] Laravel 12 instalado via Composer no container `web`
- [x] `.env` configurado com MariaDB (`DB_HOST=db`)
- [x] Laravel Breeze (Blade) instalado e scaffolding aplicado
- [x] `npm install && npm run build` no container `node`
- [x] Migrations padrão rodadas: `users`, `cache`, `jobs`
- [x] APP_NAME=ChronosEstudos, APP_LOCALE=pt_BR
- [x] http://localhost:8080 respondendo HTML

## ✅ FASE 3 — Banco de Dados: Migrations e Models

- [x] Migration `create_subjects_table` — id, user_id (FK), name, color_code
- [x] Migration `create_topics_table` — id, subject_id (FK), title, notes (longText)
- [x] Migration `create_resources_table` — id, topic_id (FK), type (enum), url, title
- [x] Migration `create_study_schedules_table` — id, topic_id (FK unique), last_studied_at, next_review_at, interval_days, status (enum)
- [x] `php artisan migrate` executado — 4 tabelas criadas
- [x] Model `Subject` — fillable + belongsTo(User) + hasMany(Topic)
- [x] Model `Topic` — fillable + belongsTo(Subject) + hasMany(Resource) + hasOne(StudySchedule)
- [x] Model `Resource` — fillable + belongsTo(Topic)
- [x] Model `StudySchedule` — fillable + belongsTo(Topic)
- [x] `User` — hasMany(Subject)
- [x] `TopicObserver::created` — auto-cria StudySchedule com next_review_at = today, status = pending
- [x] Observer registrado no `AppServiceProvider::boot()`

## ✅ FASE 4 — Lógica de Repetição Espaçada

- [x] `app/Services/SpacedRepetitionService.php` — serviço com constantes e métodos:
  - [x] `INTERVALS = [1, 7, 15, 30, 45]` — progressão fixa de intervalos
  - [x] `markAsReviewed(StudySchedule, int $nextIntervalDays)` — atualiza last_studied_at, next_review_at = today + N, status = completed
  - [x] `getDueToday(User): Collection` — schedules com next_review_at <= today AND status = pending, eager-load topic.subject + topic.resources, ordenado por next_review_at
  - [x] `resetDueSchedules(User): int` — reseta completed → pending quando a data de revisão chegou (uso diário)
- [x] Singleton registrado no `AppServiceProvider::register()`

## ✅ FASE 5 — Controllers e Rotas

- [x] `DashboardController` — `__invoke()` chama `resetDueSchedules` + `getDueToday`, agrupa por disciplina
- [x] `SubjectController` — index, show, store, update, destroy com `authorize()`
- [x] `TopicController` — show, store, update, destroy (aninhado em subjects, shallow)
- [x] `ResourceController` — store (POST) e destroy (DELETE) com `authorize()`
- [x] `StudyScheduleController` — review (marca revisado + próximo intervalo) e updateInterval
- [x] `SubjectPolicy` — view/update/delete: `$user->id === $subject->user_id`
- [x] `TopicPolicy` — view/create/update/delete: `$user->id === $topic->subject->user_id`
- [x] `routes/web.php` — 15 rotas registradas (subjects resource, topics nested shallow, resources, schedules)
- [x] Todas as rotas protegidas por `auth` middleware
- [x] Verificado: `php artisan route:list` — todas as rotas OK

## ✅ FASE 6 — Interface UI/UX

- [x] `layouts/app.blade.php` — sidebar colapsável (desktop) + bottom navigation bar (mobile) + @stack('styles') + @stack('scripts')
- [x] `layouts/sidebar.blade.php` — logo Chronos, links navegação, seção usuário com logout
- [x] `layouts/bottom-nav.blade.php` — nav móvel: Início/Disciplinas/Perfil
- [x] `dashboard.blade.php` — cards resumo (A revisar hoje / Disciplinas / Próxima), revisões agrupadas por disciplina, botão "Marcar Revisado"
- [x] `subjects/index.blade.php` — grid de cards coloridos + modal Alpine para criar/editar disciplina
- [x] `subjects/show.blade.php` — lista de tópicos + modal Alpine criar/editar (com `json_encode` seguro)
- [x] `topics/show.blade.php` — editor EasyMDE (`x-if` + `$nextTick`), lista de recursos, embed YouTube, ações de revisão
- [x] Design: Indigo 600, cards rounded-2xl, shadow-sm, hover suave, color_code como badge
- [x] Markdown: `Str::markdown()` (CommonMark nativo) na exibição, EasyMDE no editor
- [x] YouTube: regex extrai video_id → iframe youtube-nocookie.com
- [x] Recursos: store (POST) e destroy (DELETE) com modal Alpine para adicionar
- [x] Alpine.js confirmado funcional (modais, x-if, x-init, x-show, x-text)

**⚠️ Known issues:**
- EasyMDE toolbar icons mostram como texto Unicode (Font Awesome não renderiza). Funcional mas estética afetada.
- Dashboard "Próxima revisão" mostra `—` quando não há pendentes (precisa exibir a data mais próxima).
- Status badge usa lógica baseada em data (não em `status` do DB) — pode confundir.
- Sem dark mode, sem transições animadas.

## ✅ FASE 7 — Segurança, Polimento e Verificação

- [x] **6 FormRequests** — validação centralizada:
  - `StoreSubjectRequest` / `UpdateSubjectRequest` — name (required|max:255), color_code (#hex de 7 chars)
  - `StoreTopicRequest` / `UpdateTopicRequest` — title (required|max:255), notes (nullable|max:50000)
  - `StoreResourceRequest` — type (in:youtube,wikipedia,link), url (required|url|max:2048)
  - `ReviewScheduleRequest` — interval_days (required|integer|min:1|max:365)
- [x] **HTMLPurifier** (`ezyang/htmlpurifier` v4.19) — sanitização de HTML na exibição Markdown
- [x] `MarkdownSanitizer` service — cache em `storage/app/htmlpurifier/`, singleton no AppServiceProvider
- [x] **Controllers atualizados** — todos usam FormRequests (sem `$request->validate()` inline)
- [x] **TopicController** — injeção de `MarkdownSanitizer`, `show()` retorna `$notesHtml` sanitizado
- [x] **ResourceController::destroy** — corrigido para `$resource->topic` (rota shallow sem `Topic $topic`)
- [x] **Permissions** — `storage/` owned by `www-data:www-data` (775)
- [x] Font Awesome CDN no layout para ícones (sidebar/bottom-nav)
- [x] `@stack('styles')` no `<head>` — permite views adicionarem CSS (EasyMDE)
- [x] `@stack('scripts')` no fim do `<body>` — para JS de views específicas

**Verificação final:**
- [x] `docker compose up --build` — 3 serviços healthy
- [x] http://localhost:8080 — login/register funcional
- [x] CRUD disciplinas + tópicos + recursos + revisões — testado via browser
- [x] Markdown renderizado com segurança (HTMLPurifier)
- [x] YouTube embed funcional via regex no backend
- [x] EasyMDE editor funcional (x-if + $nextTick)
- [x] Alpine.js modais (criar/editar/remover) — sem reload
- [x] Test user: estudante@teste.com / password

**⚠️ Melhorias futuras:**
- EasyMDE toolbar icons (Font Awesome glyphs) — CSS não carrega corretamente
- Dark mode
- Testes automatizados (`php artisan test`)
- Progressão SM-2 (em vez de intervalos fixos)
- Responsividade refinada para telas menores que 375px


---

## 🔄 FASE 8 — Seed de Dados e Refinamentos (10/07/2026)

- [x] **Seed completo** — 10 disciplinas de Direito + 23 tópicos + 23 recursos (YouTube, links oficiais, Wikipedia)
  - Disciplinas: Constitucional, Administrativo, Civil, Penal, Processual Civil, Processual Penal, Tributário, Trabalho, Ambiental, Direitos Humanos
  - Admin: `admin@chronos.br` (id=2) com todo o conteúdo vinculado
- [x] **Datas diversificadas** — `created_at` espalhado entre 26/06 e 08/07, `next_review_at` entre 30/06 e 15/07
  - Tópicos com datas de criação variadas (não mais todos na mesma data)
  - Revisões com distribuição: algumas vencidas, algumas para hoje, outras futuras
- [x] **Dashboard: layout em linhas** — cada revisão ocupa uma linha horizontal (não mais cards)
  - Layout compacto: bolinha colorida | título | últ. revisão | próx. revisão | intervalo | ações
  - Agrupado por disciplina com contador de pendências
  - ~~Coluna "Criado" removida~~ — linhas mais enxutas, sem data de criação
- [x] **Dashboard: botão editar data de revisão** — ✏️ expande formulário inline com input de data (formato brasileiro)
  - Alpine.js: `x-data` + `x-show` + fetch PATCH via `schedules.next-review`
  - Input `type="text"` com placeholder `dd/mm/aaaa` e conversão `dd/mm/yyyy → YYYY-MM-DD` no save
  - Validação: data obrigatória, >= hoje
  - Rota: `PATCH schedules/{schedule}/next-review` → `StudyScheduleController::updateNextReview()`
  - `StudySchedule::$casts` — `next_review_at` e `last_studied_at` como `date`/`datetime`
- [x] **Dashboard: coluna Última Revisão** — exibe `last_studied_at` formatado (d/m/Y) ou `—` se nunca revisado
- [x] **Correção: formato data no flash message** — `Carbon::parse()->format('d/m/Y')` no controller de revisão
  - Antes exibia `YYYY-MM-DD`, agora exibe `dd/mm/aaaa` (padrão brasileiro)
- [x] **Correção: input de data** — trocado `type="date"` (locale do navegador) por `type="text"` com placeholder `dd/mm/aaaa`
- [x] **Ajuste: intervalo inicial de revisão** — alterado de 1 dia para 7 dias
  - Novo tópico: `interval_days = 7`, `next_review_at = today + 7 dias`
  - Progressão: 1ª revisão → 7d, 2ª revisão → 15d, 3ª → 30d, 4ª → 45d
- [x] **Ajuste: exibir todas as revisões pendentes** — dashboard mostra **todas** as pendentes, não apenas as de hoje
  - Novo método: `SpacedRepetitionService::getPending()` (sem filtro de data)
  - Título do header alterado: "Revisões de Hoje" → "Revisões Pendentes"
- [x] **Campo study_starts_at** — permite definir data de início do estudo de um tópico
  - Migration: `add_study_starts_at_to_study_schedules` — coluna `date nullable`
  - Model: `StudySchedule::$fillable` + `$casts['study_starts_at' => 'date']`
  - FormRequest: validação `study_starts_at.date_format:Y-m-d` (nullable)
  - TopicObserver: lógica para calcular `next_review_at`
    - Se `study_starts_at` informado: `parse(study_starts_at).addDays(7)`
    - Se vazio: `now().addDays(7)`
  - UI: input `type="date"` no modal de criar/editar tópico
    - Label: "Data de Início do Estudo (opcional)"
    - Campo vinculado ao objeto Alpine `editTopic.study_starts_at`
  - Testes: confirmado que data de início + 7 dias = próxima revisão correta
  - Permite ver revisões agendadas para datas futuras no mesmo dashboard
- [x] **Atualização automática de interval_days ao marcar como revisado**
  - Quando marca como revisado, `interval_days` é atualizado para o próximo na sequência
  - Exemplo: 1d → 7d → 15d → 30d → 45d
  - Modificação: `markAsReviewed()` agora atualiza `interval_days` além de `last_studied_at` e `next_review_at`
  - Efeito: Quando clica no lápis novamente, a sugestão será baseada no novo intervalo
  - Fluxo completo:
    1. Tópico com interval_days=1d, data=13/07
    2. Clica "✓ Revisado" → interval_days vira 7d, próxima revisão=20/07
    3. Clica no lápis → sugestão aparece com 20/07 + 7 = 27/07 ✓
  - Método Alpine.js `openEdit()`: abre formulário e pré-preenche com `suggestedDate`
  - Sugestões baseadas em `interval_days` + data atual do tópico:
    - 1ª revisão (interval_days == 7): sugere data_atual + 7 dias
    - 2ª revisão (interval_days == 15): sugere data_atual + 14 dias
    - 3ª revisão (interval_days == 30): sugere data_atual + 30 dias
    - Demais revisões (interval_days >= 45): mantém data atual (manual)
  - Exemplos testados:
    - Penas e Medidas de Segurança: 20/07 + 1d → mantém 20/07 ✓
    - Aplicacao da lei penal: 24/07 + 7d → sugere 31/07 ✓
    - Tópico com Início 25 de Julho: 25/07 + 7d → sugere 01/08 ✓
  - Usuário pode aceitar a sugestão ou alterar manualmente
- [x] **Barra de progresso visual** — "Progresso de Estudos" abaixo dos cards de resumo
  - Card com: contador "X/Y tópicos" e barra gradiente azul
  - Cálculo: total de tópicos com `last_studied_at !== null` / total de tópicos do usuário
  - Percentual exibido embaixo da barra
  - Atualiza em tempo real ao marcar tópico como revisado
  - Estilo: gradiente linear(to right, #4f46e5, #4338ca) com animação suave
- [x] **Tópicos continuam visíveis após marcar como revisado**
  - Modificação: `getPending()` agora retorna TODOS os schedules (não filtra por status)
  - Antes: tópicos revisados desapareciam da lista
  - Agora: tópicos continuam exibidos mesmo após marcar como revisado
  - Mostra "Últ. revisão [data]" quando tópico foi revisado
  - Mostra "—" quando tópico nunca foi revisado
  - Permite editar data de próxima revisão de qualquer tópico
  - Progresso conta apenas tópicos que foram revisados (last_studied_at !== null)