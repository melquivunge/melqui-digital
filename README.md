# Melqui Digital

Tema WordPress próprio de [melquivunge.com.br](https://melquivunge.com.br): hierarquia canónica de templates, CPTs, campos ACF registados em PHP, SEO/GEO e build SCSS/JS via Grunt. Sem page builder.

## Requisitos

- WordPress 6.4+
- PHP 8.1+
- [ACF Pro](https://www.advancedcustomfields.com/) — field groups e a página de opções vivem em `inc/fields.php` e `inc/builder.php`. Não os edites pela UI: a alteração não persiste.

## Desenvolvimento

```sh
npm install
npm run build   # sass → autoprefixer → cssnano → uglify
npm run watch   # recompila ao gravar
```

Fonte em `src/scss/` e `src/js/`. A saída (`assets/css/`, `assets/js/`) está fora do git em `main` — a pipeline é que a gera e a publica em `production`. Ícones em `assets/img/` estão versionados.

`style.css` existe só pelo cabeçalho exigido pelo WordPress. Os estilos reais estão em `assets/css/main.css`.

## Conteúdo

| Tipo | Slug | Notas |
|---|---|---|
| Projetos | `md_project` | arquivo `/projetos/` |
| Serviços | `md_service` | arquivo `/servicos/` |
| Experiência | `md_experience` | sem URL pública; renderiza na home e no Sobre |

Taxonomias: `md_project_category` e `md_stack` (partilhada pelos três CPTs). Menus: `primary` e `footer`.

## Templates

| Ficheiro | Cobre |
|---|---|
| `front-page.php` | home |
| `page.php` | páginas genéricas |
| `page-sobre.php` | Sobre |
| `page-contato.php` | Contato |
| `page-en.php` | one-pager em inglês (`/en/`) |
| `archive-md_project.php` | projetos |
| `archive-md_service.php` | serviços |
| `single-md_project.php` | case study |
| `single-md_service.php` | serviço |
| `single.php` | artigo |
| `home.php` | índice do blog |
| `index.php` | busca, taxonomias, fallback |
| `404.php` | não encontrado |

Partes reutilizáveis em `template-parts/`. Dados estruturados (Person, WebSite, Service, FAQPage, Article, BreadcrumbList) e `/llms.txt` em `inc/seo.php`.

## Git e deploy

| Branch | O que é |
|---|---|
| `main` | Fonte. SCSS, JS, Gruntfile, `package.json`. |
| `production` | O que a Hostinger serve. PHP e assets compilados. Nada mais. |

`production` é um artefacto derivado: a pipeline reconstrói-o e dá force-push a cada push em `main`. Não se trabalha nele.

A Hostinger faz `git pull` e não corre build. O branch de deploy **tem de ser `production`**. Apontado a `main`, o site fica sem CSS.

O pacote é montado por lista de inclusão explícita (`*.php`, `style.css`, `screenshot.png`, `assets/**`). `src/`, `package.json` e `.github/` não entram no webroot.

### Pipelines

`security.yml` corre em push e PR para `main`, e é chamado pelo deploy como portão:

- gitleaks sobre o histórico completo
- portão de ficheiros sensíveis (`.env`, `wp-config.php`, chaves)
- sintaxe PHP 8.1 e guarda `ABSPATH` em todos os `.php`
- funções proibidas (`eval`, `shell_exec`, …)
- `npm audit` — dependências de produção bloqueiam; as de build avisam

`deploy.yml` compila, confirma que o CSS/JS não saíram vazios, monta o pacote e publica em `production`. Tags `v*` carimbam o `Version:` do `style.css`.

Fluxo de trabalho: branch → pull request → merge em `main`. Não se faz commit direto na `main`.

## Licença

GPL-2.0-or-later. Ver o cabeçalho de `style.css`.
