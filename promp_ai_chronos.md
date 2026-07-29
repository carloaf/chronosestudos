chronosestudos

Você agirá como um Engenheiro de Software Full-Stack Sênior, especialista em Laravel, PHP, Docker e desenvolvimento de interfaces responsivas, reconhecido profissional por implementar designs bonitos e atrativos. 

Preciso criar uma aplicação web chamada "ChronosEstudos", que será um cronograma de estudos para concursos públicos baseado no método de Repetição Espaçada. A aplicação deve rodar inteiramente em containers Docker.

### 1. REQUISITOS TÉCNICOS:
- **Backend:** PHP 8.2+ com o framework Laravel (versão estável mais recente).
- **Servidor Web:** Apache (pode ser configurado no próprio container do PHP ou em um container separado).
- **Banco de Dados:** MariaDB.
- **Frontend:** HTML5, CSS3 (recomendo Tailwind CSS para agilizar a estilização) e JavaScript nativo (ou Alpine.js para interações simples).
- **Ambiente:** Docker Compose contendo os serviços: `web` (Apache + PHP), `db` (MariaDB) e opcionalmente um container para gerenciar assets/Node (se necessário para o Tailwind).
- **Responsividade:** Deve ser 100% amigável para dispositivos móveis, tablets e desktops (Mobile-First).

### 2. PRINCIPAIS FUNCIONALIDADES:
- **Gerenciamento de Disciplinas:** CRUD (Criar, Ler, Atualizar, Deletar) de matérias (ex: Direito Constitucional, Português).
- **Gerenciamento de Conteúdos/Tópicos:** Cada disciplina terá vários tópicos associados (ex: "Artigo 5º da CF" dentro de Direito Constitucional).
- **Configuração de Repetição Espaçada:** Cada tópico de estudo deve ter uma programação de revisão configurável (ex: repetir em 1 dia, 7 dias, 15 dias, 30 dias, 45 dias, ou um intervalo personalizado em dias).
- **Painel de Revisões Diárias (Dashboard):** Tela inicial que mostra quais tópicos o usuário deve revisar hoje, com base no cálculo da data da última revisão + intervalo programado.
- **Anexos de Recursos por Tópico:** Em cada tópico, o usuário deve poder cadastrar links externos de apoio, como:
  - Links do YouTube (se possível, exibir o player embutido/iframe se for um link de vídeo).
  - Links da Wikipedia ou outros portais de conteúdo.
  - Espaço para anotações rápidas em texto Markdown ou Rich Text simples.

### 3. ESQUEMA DE BANCO DE DADOS (SUGESTÃO):
- `users`: (padrão do Laravel).
- `subjects` (Disciplinas): `id`, `user_id`, `name`, `color_code` (para organização visual), timestamps.
- `topics` (Conteúdos): `id`, `subject_id`, `title`, `notes` (texto), `created_at`, `updated_at`.
- `resources` (Recursos de apoio): `id`, `topic_id`, `type` (youtube, wikipedia, link, etc), `url`, `title`, timestamps.
- `study_schedules` (Agendamento de Revisões): `id`, `topic_id`, `last_studied_at`, `next_review_at`, `interval_days` (ex: 7, 15, 30), `status` (pendente, concluído), timestamps.

### 4. JA FOI FEITO :
Para iniciar vamos: 

**Etapa 1:** Escreva os arquivos de configuração do ambiente Docker para este projeto:
1. `docker-compose.yml` (Configurando os serviços do Apache/PHP e MariaDB).
2. `Dockerfile` para o serviço PHP/Apache (incluindo as extensões necessárias para o Laravel e MariaDB, além do Composer).
3. Breve instrução de como inicializar o projeto Laravel dentro desse ambiente Docker.

Aguarde minhas instruções após concluir a Etapa 1 para passarmos para as Migrations e Models do Laravel.

### 5. O QUE PRECISO QUE VOCÊ FAÇA AGORA
Melhorar o layout da pagina de login, pois esta com um design muito simples e pouco atrativo. Sugiro implementar um layout moderno, com cores agradáveis, tipografia legível e elementos visuais que transmitam organização e foco nos estudos (de acordo com o restante do projeto). Podemos utilizar Tailwind CSS para facilitar a estilização e garantir responsividade.

