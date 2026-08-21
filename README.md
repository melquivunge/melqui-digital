# Melqui Digital

[Português](#português) · [English](#english)

Tema WordPress próprio de [melquivunge.com.br](https://melquivunge.com.br). Custom WordPress theme for [melquivunge.com.br](https://melquivunge.com.br).

---

## Português

Hierarquia canónica de templates, CPTs, campos ACF registados em PHP, SEO/GEO e build SCSS/JS via Grunt. Sem page builder.

### Requisitos

- WordPress 6.4+
- PHP 8.1+
- [ACF Pro](https://www.advancedcustomfields.com/) — field groups e a página de opções vivem em `inc/fields.php` e `inc/builder.php`. Não os edites pela UI: a alteração não persiste.

### Desenvolvimento

```sh
npm install
npm run build   # sass → autoprefixer → cssnano → uglify
npm run watch   # recompila ao gravar
```

Fonte em `src/scss/` e `src/js/`. A saída (`assets/css/`, `assets/js/`) está fora do git em `main` — a pipeline é que a gera e a publica em `production`. Ícones em `assets/img/` estão versionados.

`style.css` existe só pelo cabeçalho exigido pelo WordPress. Os estilos reais estão em `assets/css/main.css`.

### Conteúdo

| Tipo | Slug | Notas |
|---|---|---|
| Projetos | `md_project` | arquivo `/projetos/` |
| Serviços | `md_service` | arquivo `/servicos/` |
| Experiência | `md_experience` | sem URL pública; renderiza na home e no Sobre |

Taxonomias: `md_project_category` e `md_stack` (partilhada pelos três CPTs). Menus: `primary` e `footer`.

### Templates

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

### Git e deploy

| Branch | O que é |
|---|---|
| `main` | Fonte. SCSS, JS, Gruntfile, `package.json`. |
| `production` | O que a Hostinger serve. PHP e assets compilados. Nada mais. |

`production` é um artefacto derivado: a pipeline reconstrói-o e dá force-push a cada push em `main`. Não se trabalha nele.

A Hostinger faz `git pull` e não corre build. O branch de deploy **tem de ser `production`**. Apontado a `main`, o site fica sem CSS.

O pacote é montado por lista de inclusão explícita (`*.php`, `style.css`, `screenshot.png`, `assets/**`). `src/`, `package.json` e `.github/` não entram no webroot.

#### Pipelines

`security.yml` corre em push e PR para `main`, e é chamado pelo deploy como portão:

- gitleaks sobre o histórico completo
- portão de ficheiros sensíveis (`.env`, `wp-config.php`, chaves)
- sintaxe PHP 8.1 e guarda `ABSPATH` em todos os `.php`
- funções proibidas (`eval`, `shell_exec`, …)
- `npm audit` — dependências de produção bloqueiam; as de build avisam

`deploy.yml` compila, confirma que o CSS/JS não saíram vazios, monta o pacote e publica em `production`. Tags `v*` carimbam o `Version:` do `style.css`.

Fluxo de trabalho: branch → pull request → merge em `main`. Não se faz commit direto na `main`.

### Licença

GPL-2.0-or-later. Ver o cabeçalho de `style.css`.

---

## English

Canonical WordPress template hierarchy, custom post types, ACF fields registered in PHP, SEO/GEO, and SCSS/JS built with Grunt. No page builder.

### Requirements

- WordPress 6.4+
- PHP 8.1+
- [ACF Pro](https://www.advancedcustomfields.com/) — field groups and the options page live in `inc/fields.php` and `inc/builder.php`. Do not edit them in the UI: the change will not persist.

### Development

```sh
npm install
npm run build   # sass → autoprefixer → cssnano → uglify
npm run watch   # rebuild on save
```

Source lives in `src/scss/` and `src/js/`. Output (`assets/css/`, `assets/js/`) is gitignored on `main` — the pipeline generates it and publishes it to `production`. Icons in `assets/img/` are versioned.

`style.css` exists only for the header WordPress requires. Real styles are in `assets/css/main.css`.

### Content

| Type | Slug | Notes |
|---|---|---|
| Projects | `md_project` | archive `/projetos/` |
| Services | `md_service` | archive `/servicos/` |
| Experience | `md_experience` | no public URL; rendered on home and About |

Taxonomies: `md_project_category` and `md_stack` (shared across the three CPTs). Menus: `primary` and `footer`.

### Templates

| File | Covers |
|---|---|
| `front-page.php` | home |
| `page.php` | generic pages |
| `page-sobre.php` | About |
| `page-contato.php` | Contact |
| `page-en.php` | English one-pager (`/en/`) |
| `archive-md_project.php` | projects |
| `archive-md_service.php` | services |
| `single-md_project.php` | case study |
| `single-md_service.php` | service |
| `single.php` | article |
| `home.php` | blog index |
| `index.php` | search, taxonomies, fallback |
| `404.php` | not found |

Reusable parts live in `template-parts/`. Structured data (Person, WebSite, Service, FAQPage, Article, BreadcrumbList) and `/llms.txt` are in `inc/seo.php`.

### Git and deploy

| Branch | What it is |
|---|---|
| `main` | Source. SCSS, JS, Gruntfile, `package.json`. |
| `production` | What Hostinger serves. PHP and compiled assets. Nothing else. |

`production` is a derived artefact: the pipeline rebuilds it and force-pushes on every push to `main`. Do not work on it.

Hostinger runs `git pull` and no build. The deploy branch **must be `production`**. Pointed at `main`, the site has no CSS.

The package is assembled from an explicit include list (`*.php`, `style.css`, `screenshot.png`, `assets/**`). `src/`, `package.json` and `.github/` do not enter the webroot.

#### Pipelines

`security.yml` runs on push and PR to `main`, and is called by deploy as a gate:

- gitleaks over the full history
- forbidden-file gate (`.env`, `wp-config.php`, keys)
- PHP 8.1 syntax and a required `ABSPATH` guard in every `.php` file
- banned functions (`eval`, `shell_exec`, …)
- `npm audit` — production dependencies block; build dependencies warn

`deploy.yml` compiles, confirms CSS/JS are not empty, assembles the package and publishes to `production`. `v*` tags stamp `Version:` in `style.css`.

Workflow: branch → pull request → merge to `main`. No commits directly on `main`.

### License

GPL-2.0-or-later. See the header in `style.css`.
