# Tâches de développement — Tool

> Chaque tâche est dans l'ordre de développement. Les dépendances sont respectées.
> Statut : `[x]` = fait — `[ ]` = à faire

---

## Phase 1 — Fondations (Base de données & Admin)

### 1.1 — Packages & Configuration

- [ ] **T-001 — Installer le package `laravel/ai`**
  - **Description** : Installer le package officiel Laravel AI via Composer et publier sa configuration.
  - **Critères d'acceptation** :
    - `composer require laravel/ai` exécuté sans erreur
    - Le fichier `config/ai.php` est publié
    - La clé `OPENAI_API_KEY` est ajoutée dans `.env` et `.env.example`
    - Le provider OpenAI est configuré comme provider par défaut

- [ ] **T-002 — Configurer le filesystem pour le stockage des logos**
  - **Description** : Configurer un disque dédié dans `config/filesystems.php` pour stocker les logos des outils dans `storage/app/public/logos/`. Créer le lien symbolique storage si non existant.
  - **Critères d'acceptation** :
    - Un disque `logos` est configuré dans `filesystems.php` pointant vers `storage/app/public/logos`
    - Le dossier `storage/app/public/logos/` existe
    - `php artisan storage:link` fonctionne
    - Les logos sont accessibles publiquement via `/storage/logos/`

- [ ] **T-003 — Configurer le driver de queue pour les jobs**
  - **Description** : S'assurer que le driver de queue est configuré (database driver, table jobs déjà existante) et que le worker peut traiter les jobs.
  - **Critères d'acceptation** :
    - `QUEUE_CONNECTION=database` dans `.env`
    - La table `jobs` existe déjà (migration présente)
    - `php artisan queue:work` démarre sans erreur

### 1.2 — Enum & Classes utilitaires

- [ ] **T-004 — Créer l'enum `GenerationStatus`**
  - **Description** : Créer un enum PHP `App\Enums\GenerationStatus` avec les valeurs : `Pending`, `Generating`, `Completed`, `Failed`. Utilisé par `Tool` et `Comparison`.
  - **Critères d'acceptation** :
    - L'enum est dans `app/Enums/GenerationStatus.php`
    - Cas : `Pending`, `Generating`, `Completed`, `Failed`
    - Les valeurs string sont `pending`, `generating`, `completed`, `failed`
    - L'enum est typé `string` (backed enum)

### 1.3 — Modèle Category

- [ ] **T-005 — Créer la migration `categories`**
  - **Description** : Créer la migration pour la table `categories` avec tous les champs définis dans PLAN.md.
  - **Critères d'acceptation** :
    - Colonnes : `id` (ulid), `name` (string), `slug` (string unique), `description` (text), `icon` (string), `sort_order` (integer, default 0), `meta_title` (string nullable), `meta_description` (text nullable), `timestamps`
    - Index unique sur `slug`
    - La migration s'exécute sans erreur

- [ ] **T-006 — Créer le modèle `Category` avec factory**
  - **Description** : Créer le modèle Eloquent `Category` avec ses relations, casts, fillable, et la factory associée.
  - **Critères d'acceptation** :
    - Modèle dans `app/Models/Category.php`
    - Utilise le trait `HasFactory` et le concern `HasUlids`
    - Relation `hasMany(Tool::class)`
    - `$fillable` contient tous les champs éditables
    - Méthode `casts()` pour les types appropriés
    - Le slug est généré automatiquement à partir du `name` (via boot ou observer)
    - `CategoryFactory` dans `database/factories/CategoryFactory.php`
    - La factory génère des données réalistes avec Faker
    - Route model binding via `slug` pour les routes publiques

- [ ] **T-007 — Créer le seeder `CategorySeeder` avec les catégories prédéfinies**
  - **Description** : Créer un seeder qui insère les 15 catégories dev-oriented définies dans PLAN.md.
  - **Critères d'acceptation** :
    - Seeder dans `database/seeders/CategorySeeder.php`
    - Les 15 catégories sont créées : IDE & Éditeurs de code, CI/CD & DevOps, Hébergement & Cloud, Bases de données, Monitoring & Observabilité, Gestion de projet, Communication & Collaboration, Design & Prototypage, Outils IA & Assistants de code, Sécurité & Tests, API & Backend, Frontend & Frameworks, Open Source, No-Code / Low-Code, Documentation & Knowledge
    - Chaque catégorie a un `slug` auto-généré, une `description` pertinente, une icône Lucide appropriée, et un `sort_order`
    - Le seeder est appelé dans `DatabaseSeeder`
    - `php artisan db:seed --class=CategorySeeder` s'exécute sans erreur

- [ ] **T-008 — Tests unitaires du modèle `Category`**
  - **Description** : Écrire des tests Pest pour le modèle Category : factory, relations, génération de slug.
  - **Critères d'acceptation** :
    - Test que la factory crée un Category valide
    - Test que le slug est auto-généré à partir du name
    - Test que les slugs sont uniques (pas de doublon)
    - Test de la relation `tools()` (hasMany)
    - Tous les tests passent

### 1.4 — Modèle Tag

- [ ] **T-009 — Créer la migration `tags`**
  - **Description** : Créer la migration pour la table `tags`.
  - **Critères d'acceptation** :
    - Colonnes : `id` (ulid), `name` (string), `slug` (string unique), `timestamps`
    - Index unique sur `slug`
    - La migration s'exécute sans erreur

- [ ] **T-010 — Créer le modèle `Tag` avec factory**
  - **Description** : Créer le modèle Eloquent `Tag` avec ses relations et la factory.
  - **Critères d'acceptation** :
    - Modèle dans `app/Models/Tag.php`
    - Traits `HasFactory`, `HasUlids`
    - Relation `belongsToMany(Tool::class)`
    - Slug auto-généré à partir du `name`
    - `TagFactory` crée des données réalistes
    - Route model binding via `slug` pour les routes publiques

- [ ] **T-011 — Tests unitaires du modèle `Tag`**
  - **Description** : Écrire des tests Pest pour le modèle Tag.
  - **Critères d'acceptation** :
    - Test factory, slug auto-gen, relation `tools()`
    - Tous les tests passent

### 1.5 — Modèle Tool

- [ ] **T-012 — Créer la migration `tools`**
  - **Description** : Créer la migration pour la table `tools` avec tous les champs du PLAN.md.
  - **Critères d'acceptation** :
    - Colonnes : `id` (ulid), `name` (string), `slug` (string unique), `url` (string), `logo_path` (string nullable), `description` (text nullable), `content` (longText nullable), `pricing` (json nullable), `pros` (json nullable), `cons` (json nullable), `features` (json nullable), `faq` (json nullable), `platforms` (json nullable), `category_id` (foreignUlid, constrained), `is_published` (boolean default false), `is_sponsored` (boolean default false), `sponsored_until` (datetime nullable), `generation_status` (string default 'pending'), `meta_title` (string nullable), `meta_description` (text nullable), `generated_at` (datetime nullable), `published_at` (datetime nullable), `timestamps`
    - Index unique sur `slug`
    - Foreign key sur `category_id` vers `categories`
    - La migration s'exécute sans erreur

- [ ] **T-013 — Créer la migration pivot `tool_tag`**
  - **Description** : Créer la table pivot pour la relation many-to-many entre Tool et Tag.
  - **Critères d'acceptation** :
    - Colonnes : `tool_id` (foreignUlid), `tag_id` (foreignUlid)
    - Clé primaire composite sur `(tool_id, tag_id)`
    - Foreign keys contraintes
    - Pas de timestamps

- [ ] **T-014 — Créer la migration pivot `tool_alternatives`**
  - **Description** : Créer la table pivot self-referencing pour les alternatives entre outils.
  - **Critères d'acceptation** :
    - Colonnes : `tool_id` (foreignUlid), `alternative_id` (foreignUlid)
    - Clé primaire composite sur `(tool_id, alternative_id)`
    - Foreign keys contraintes vers `tools`
    - Pas de timestamps

- [ ] **T-015 — Créer le modèle `Tool` avec factory**
  - **Description** : Créer le modèle Eloquent `Tool` avec toutes ses relations, casts, scopes, et la factory.
  - **Critères d'acceptation** :
    - Modèle dans `app/Models/Tool.php`
    - Traits : `HasFactory`, `HasUlids`
    - Relations : `belongsTo(Category::class)`, `belongsToMany(Tag::class)`, `belongsToMany(Tool::class, 'tool_alternatives', 'tool_id', 'alternative_id')->as('alternatives')`, `hasMany` vers Comparison (tool_a et tool_b)
    - Méthode `casts()` : `pricing` → array, `pros` → array, `cons` → array, `features` → array, `faq` → array, `platforms` → array, `is_published` → boolean, `is_sponsored` → boolean, `generation_status` → `GenerationStatus::class`, `generated_at` → datetime, `published_at` → datetime, `sponsored_until` → datetime
    - Slug auto-généré à partir du `name`
    - Scope `published()` : filtre `is_published = true`
    - Scope `sponsored()` : filtre `is_sponsored = true` et `sponsored_until > now`
    - `ToolFactory` avec des données réalistes, états `published()`, `sponsored()`
    - Accesseur pour l'URL du logo (avec fallback vers placeholder)
    - Route model binding via `slug` pour les routes publiques

- [ ] **T-016 — Tests unitaires du modèle `Tool`**
  - **Description** : Écrire des tests Pest pour le modèle Tool.
  - **Critères d'acceptation** :
    - Test factory (défaut + état published + état sponsored)
    - Test slug auto-gen et unicité
    - Test relation `category()`, `tags()`, `alternatives()`
    - Test scope `published()`, `sponsored()`
    - Test casts (pricing retourne un array, generation_status retourne l'enum, etc.)
    - Tous les tests passent

### 1.6 — Modèle Comparison

- [ ] **T-017 — Créer la migration `comparisons`**
  - **Description** : Créer la migration pour la table `comparisons`.
  - **Critères d'acceptation** :
    - Colonnes : `id` (ulid), `tool_a_id` (foreignUlid constrained), `tool_b_id` (foreignUlid constrained), `slug` (string unique), `content` (longText nullable), `verdict` (text nullable), `generation_status` (string default 'pending'), `is_published` (boolean default false), `meta_title` (string nullable), `meta_description` (text nullable), `generated_at` (datetime nullable), `published_at` (datetime nullable), `timestamps`
    - Index unique sur `slug`
    - Index unique composite sur `(tool_a_id, tool_b_id)` pour éviter les doublons
    - Foreign keys contraintes

- [ ] **T-018 — Créer le modèle `Comparison` avec factory**
  - **Description** : Créer le modèle Eloquent `Comparison` avec relations, casts, et factory.
  - **Critères d'acceptation** :
    - Modèle dans `app/Models/Comparison.php`
    - Traits : `HasFactory`, `HasUlids`
    - Relations : `belongsTo(Tool::class, 'tool_a_id')->as('toolA')`, `belongsTo(Tool::class, 'tool_b_id')->as('toolB')`
    - Casts : `generation_status` → `GenerationStatus::class`, `is_published` → boolean, `generated_at` → datetime, `published_at` → datetime
    - Slug auto-généré au format `{tool_a_slug}-vs-{tool_b_slug}`
    - Scope `published()`
    - `ComparisonFactory` avec données réalistes, état `published()`
    - Route model binding via `slug`

- [ ] **T-019 — Tests unitaires du modèle `Comparison`**
  - **Description** : Écrire des tests Pest pour le modèle Comparison.
  - **Critères d'acceptation** :
    - Test factory
    - Test slug auto-gen format `X-vs-Y`
    - Test unicité de la paire (tool_a, tool_b)
    - Test relations `toolA()`, `toolB()`
    - Test scope `published()`
    - Tous les tests passent

### 1.7 — Exécuter les migrations et seeders

- [ ] **T-020 — Lancer les migrations et le seeder**
  - **Description** : Exécuter toutes les migrations et le CategorySeeder pour initialiser la base.
  - **Critères d'acceptation** :
    - `php artisan migrate:fresh --seed` s'exécute sans erreur
    - Les tables `categories`, `tags`, `tools`, `comparisons`, `tool_tag`, `tool_alternatives` existent
    - Les 15 catégories prédéfinies sont présentes dans la table `categories`
    - Les tests existants passent toujours (`php artisan test`)

### 1.8 — Admin : Layout & Navigation

- [ ] **T-021 — Mettre à jour la navigation du sidebar admin**
  - **Description** : Ajouter les liens de navigation dans le sidebar existant pour les sections admin : Dashboard, Outils, Catégories, Tags, Comparatifs.
  - **Critères d'acceptation** :
    - Le composant `NavMain.vue` (ou équivalent) affiche les liens : Dashboard (`/dashboard`), Outils (`/dashboard/outils`), Catégories (`/dashboard/categories`), Tags (`/dashboard/tags`), Comparatifs (`/dashboard/comparatifs`)
    - Chaque lien a une icône Lucide appropriée
    - Le lien actif est visuellement distinct (highlight)
    - La navigation est fonctionnelle (les liens redirigent correctement)

### 1.9 — Admin CRUD : Catégories

- [ ] **T-022 — Créer le `CategoryController` admin**
  - **Description** : Créer le contrôleur admin pour les catégories avec les méthodes CRUD complètes.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/Admin/CategoryController.php`
    - Méthodes : `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`, `reorder()`
    - `index()` retourne les catégories ordonnées par `sort_order` avec le count de tools
    - `store()` crée une catégorie et redirige vers index
    - `update()` met à jour et redirige
    - `destroy()` supprime (avec vérification qu'aucun outil n'est lié)
    - `reorder()` accepte un array d'IDs ordonnés et met à jour les `sort_order`
    - Toutes les méthodes utilisent des Form Requests

- [ ] **T-023 — Créer les Form Requests pour Category**
  - **Description** : Créer `StoreCategoryRequest` et `UpdateCategoryRequest` avec les règles de validation.
  - **Critères d'acceptation** :
    - `app/Http/Requests/Admin/StoreCategoryRequest.php` : `name` required|string|max:255|unique:categories, `description` required|string, `icon` required|string|max:50
    - `app/Http/Requests/Admin/UpdateCategoryRequest.php` : mêmes règles avec `unique:categories,name,{id}`
    - Messages d'erreur personnalisés en français
    - Les règles utilisent le format string ou array selon les conventions du projet (vérifier les Form Requests existantes)

- [ ] **T-024 — Créer les routes admin pour les catégories**
  - **Description** : Enregistrer les routes CRUD + reorder pour les catégories dans un fichier de routes admin.
  - **Critères d'acceptation** :
    - Fichier `routes/admin.php` créé et inclus dans `routes/web.php`
    - Routes protégées par middleware `auth` et `verified`
    - Préfixe `/dashboard`
    - Routes : `GET /dashboard/categories` (index), `GET /dashboard/categories/create` (create), `POST /dashboard/categories` (store), `GET /dashboard/categories/{category}/edit` (edit), `PUT /dashboard/categories/{category}` (update), `DELETE /dashboard/categories/{category}` (destroy), `POST /dashboard/categories/reorder` (reorder)
    - Routes nommées `admin.categories.*`

- [ ] **T-025 — Créer la page Vue `Admin/Categories/Index.vue`**
  - **Description** : Créer la page listing des catégories avec tableau, actions et drag & drop.
  - **Critères d'acceptation** :
    - Page dans `resources/js/pages/Admin/Categories/Index.vue`
    - Affiche un tableau avec colonnes : icône, nom, slug, nombre d'outils, actions
    - Bouton "Ajouter une catégorie" en haut
    - Actions par ligne : Éditer, Supprimer (avec confirmation)
    - Drag & drop sur les lignes pour réordonner (met à jour `sort_order` via endpoint `reorder`)
    - Layout admin (AppSidebarLayout)
    - Message flash de succès après opérations

- [ ] **T-026 — Créer les pages Vue `Admin/Categories/Create.vue` et `Edit.vue`**
  - **Description** : Créer les formulaires de création et édition de catégorie.
  - **Critères d'acceptation** :
    - `resources/js/pages/Admin/Categories/Create.vue` : formulaire avec champs name, description, icon (sélecteur d'icônes Lucide)
    - `resources/js/pages/Admin/Categories/Edit.vue` : même formulaire pré-rempli
    - Validation côté client cohérente avec les Form Requests
    - Affichage des erreurs de validation sous chaque champ
    - Boutons Annuler (retour à index) et Sauvegarder
    - Soumission via Inertia form helper
    - Champs meta_title et meta_description optionnels (section "SEO" repliable)

- [ ] **T-027 — Tests feature du CRUD Catégories**
  - **Description** : Écrire des tests Pest pour toutes les opérations CRUD des catégories.
  - **Critères d'acceptation** :
    - Test `index` : retourne la liste des catégories (authentifié)
    - Test `index` : redirige vers login si non authentifié
    - Test `store` : crée une catégorie avec des données valides
    - Test `store` : échoue avec des données invalides (validation)
    - Test `update` : met à jour une catégorie existante
    - Test `destroy` : supprime une catégorie sans outils liés
    - Test `destroy` : échoue si la catégorie a des outils liés
    - Test `reorder` : met à jour l'ordre des catégories
    - Tous les tests passent

### 1.10 — Admin CRUD : Tags

- [ ] **T-028 — Créer le `TagController` admin**
  - **Description** : Créer le contrôleur admin pour les tags avec CRUD complet.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/Admin/TagController.php`
    - Méthodes : `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
    - `index()` retourne les tags paginés avec le count de tools, triés par nom
    - `destroy()` vérifie qu'aucun outil n'est lié avant suppression
    - Utilise des Form Requests

- [ ] **T-029 — Créer les Form Requests pour Tag**
  - **Description** : Créer `StoreTagRequest` et `UpdateTagRequest`.
  - **Critères d'acceptation** :
    - `app/Http/Requests/Admin/StoreTagRequest.php` : `name` required|string|max:100|unique:tags
    - `app/Http/Requests/Admin/UpdateTagRequest.php` : mêmes règles avec exception d'unicité
    - Messages en français

- [ ] **T-030 — Créer les routes admin pour les tags**
  - **Description** : Enregistrer les routes CRUD pour les tags dans `routes/admin.php`.
  - **Critères d'acceptation** :
    - Routes resource CRUD sous `/dashboard/tags`
    - Routes nommées `admin.tags.*`
    - Protégées par auth + verified

- [ ] **T-031 — Créer les pages Vue admin pour les tags**
  - **Description** : Créer les pages Index, Create, Edit pour les tags.
  - **Critères d'acceptation** :
    - `Admin/Tags/Index.vue` : tableau paginé avec nom, slug, nombre d'outils, actions (éditer, supprimer)
    - `Admin/Tags/Create.vue` : formulaire simple (name uniquement)
    - `Admin/Tags/Edit.vue` : formulaire pré-rempli
    - Layout admin, validation, messages flash

- [ ] **T-032 — Tests feature du CRUD Tags**
  - **Description** : Écrire des tests Pest pour le CRUD tags.
  - **Critères d'acceptation** :
    - Tests store, update, destroy (avec et sans outils liés), index, validation
    - Test accès non authentifié → redirect login
    - Tous les tests passent

### 1.11 — Admin CRUD : Outils

- [ ] **T-033 — Créer le `ToolController` admin**
  - **Description** : Créer le contrôleur admin pour les outils avec CRUD complet.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/Admin/ToolController.php`
    - Méthodes : `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`, `togglePublish()`
    - `index()` : retourne les outils paginés avec catégorie, filtrable par catégorie et statut de génération, avec recherche par nom
    - `create()` : passe les catégories et tags disponibles
    - `store()` : crée un outil avec les champs de base (name, url, category_id) + tags optionnels
    - `show()` : retourne l'outil avec toutes ses relations (catégorie, tags, alternatives, comparisons)
    - `edit()` : passe l'outil, les catégories, les tags
    - `update()` : met à jour l'outil avec sync des tags et alternatives
    - `destroy()` : supprime l'outil et ses relations pivot
    - `togglePublish()` : bascule `is_published` et met à jour `published_at`

- [ ] **T-034 — Créer les Form Requests pour Tool**
  - **Description** : Créer `StoreToolRequest` et `UpdateToolRequest`.
  - **Critères d'acceptation** :
    - `app/Http/Requests/Admin/StoreToolRequest.php` : `name` required|string|max:255|unique:tools, `url` required|url|max:500, `category_id` required|exists:categories,id, `tags` nullable|array, `tags.*` exists:tags,id
    - `app/Http/Requests/Admin/UpdateToolRequest.php` : mêmes règles avec exception d'unicité + champs optionnels pour le contenu (description, content, pricing, pros, cons, features, faq, platforms, meta_title, meta_description)
    - Règles pour les champs JSON : `pricing` nullable|array, `pros` nullable|array, `cons` nullable|array, etc.

- [ ] **T-035 — Créer les routes admin pour les outils**
  - **Description** : Enregistrer les routes CRUD + togglePublish pour les outils dans `routes/admin.php`.
  - **Critères d'acceptation** :
    - Routes resource CRUD sous `/dashboard/outils`
    - Route `POST /dashboard/outils/{tool}/toggle-publish` (togglePublish)
    - Routes nommées `admin.tools.*`

- [ ] **T-036 — Créer la page Vue `Admin/Tools/Index.vue`**
  - **Description** : Créer la page listing des outils avec tableau, filtres et actions.
  - **Critères d'acceptation** :
    - Tableau avec colonnes : logo (miniature), nom, catégorie, statut publication (badge), statut génération (badge coloré), date de création, actions
    - Filtres : sélecteur catégorie, sélecteur statut génération (pending/generating/completed/failed), recherche texte par nom
    - Bouton "Ajouter un outil" en haut
    - Actions par ligne : Voir, Éditer, Publier/Dépublier, Supprimer
    - Pagination
    - Layout admin

- [ ] **T-037 — Créer la page Vue `Admin/Tools/Create.vue`**
  - **Description** : Créer le formulaire de création d'outil (champs de base uniquement).
  - **Critères d'acceptation** :
    - Champs : name, url, category_id (select), tags (multi-select)
    - Sélecteur de catégorie avec les catégories disponibles
    - Multi-select de tags
    - Validation côté client
    - Soumission via Inertia form helper
    - Après création : redirect vers la page d'édition de l'outil

- [ ] **T-038 — Créer la page Vue `Admin/Tools/Edit.vue`**
  - **Description** : Créer le formulaire d'édition complet d'un outil avec preview et panels latéraux.
  - **Critères d'acceptation** :
    - **Section principale** (formulaire) :
      - Champs de base : name, url, category_id, tags (multi-select)
      - Description courte (textarea)
      - Contenu complet (textarea markdown avec preview)
      - Fonctionnalités (liste dynamique d'items ajoutables/supprimables)
      - Avantages (liste dynamique)
      - Inconvénients (liste dynamique)
      - Pricing (éditeur JSON structuré ou formulaire dynamique de plans)
      - Plateformes (checkboxes : Web, Desktop, Mobile, API, CLI)
      - FAQ (paires question/réponse dynamiques)
    - **Section SEO** (repliable) : meta_title, meta_description
    - **Section status** : badges generation_status, is_published, bouton publier/dépublier
    - **Bouton "Générer avec IA"** : visible, lance la génération (implémenté en Phase 2)
    - **Preview du logo** : affiche le logo actuel ou placeholder
    - Soumission via Inertia form helper

- [ ] **T-039 — Créer le composant Vue `Admin/Tools/AlternativesPanel.vue`**
  - **Description** : Panel latéral affichant les alternatives d'un outil avec leurs statuts.
  - **Critères d'acceptation** :
    - Affiche la liste des alternatives de l'outil
    - Pour chaque alternative : nom, logo miniature, statut (badge : existant publié / existant brouillon / en cours de génération / à créer)
    - Bouton "Créer l'outil" pour les alternatives non existantes → crée un Tool en brouillon et redirige vers son édition
    - Bouton "Générer avec IA" pour les alternatives existantes non générées (implémenté en Phase 2)
    - Si aucune alternative : message "Aucune alternative suggérée" + bouton "Suggérer des alternatives" (Phase 2)

- [ ] **T-040 — Créer le composant Vue `Admin/Tools/ComparisonsPanel.vue`**
  - **Description** : Panel latéral affichant les comparatifs possibles pour un outil.
  - **Critères d'acceptation** :
    - Affiche la liste des comparatifs existants et potentiels de l'outil
    - Pour chaque comparatif : "Outil vs Alternative", statut (badge : publié / brouillon / en génération / pending / à créer)
    - Bouton "Générer le comparatif" pour les comparatifs non générés (implémenté en Phase 2)
    - Si aucun comparatif : message "Aucun comparatif disponible"

- [ ] **T-041 — Tests feature du CRUD Outils**
  - **Description** : Écrire des tests Pest pour le CRUD outils.
  - **Critères d'acceptation** :
    - Test `index` : liste paginée, filtrage par catégorie, filtrage par statut
    - Test `store` : crée un outil avec données valides, sync tags
    - Test `store` : validation (name unique, url valide, category_id exists)
    - Test `update` : met à jour un outil, sync tags
    - Test `destroy` : supprime un outil et ses pivots
    - Test `togglePublish` : bascule la publication
    - Test accès non authentifié
    - Tous les tests passent

### 1.12 — Admin CRUD : Comparatifs

- [ ] **T-042 — Créer le `ComparisonController` admin**
  - **Description** : Créer le contrôleur admin pour les comparatifs avec CRUD complet.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/Admin/ComparisonController.php`
    - Méthodes : `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`, `togglePublish()`
    - `index()` : comparatifs paginés avec toolA, toolB eager loaded, filtrable par statut de génération
    - `create()` : passe la liste des outils pour les sélecteurs
    - `store()` : crée un comparatif (tool_a_id, tool_b_id), génère le slug automatiquement
    - `edit()` : passe le comparatif avec toolA, toolB
    - `update()` : met à jour le contenu, verdict, meta tags
    - `togglePublish()` : bascule la publication

- [ ] **T-043 — Créer les Form Requests pour Comparison**
  - **Description** : Créer `StoreComparisonRequest` et `UpdateComparisonRequest`.
  - **Critères d'acceptation** :
    - `StoreComparisonRequest` : `tool_a_id` required|exists:tools,id, `tool_b_id` required|exists:tools,id|different:tool_a_id, règle custom pour vérifier que la paire n'existe pas déjà
    - `UpdateComparisonRequest` : `content` nullable|string, `verdict` nullable|string, `meta_title` nullable|string|max:70, `meta_description` nullable|string|max:160

- [ ] **T-044 — Créer les routes admin pour les comparatifs**
  - **Description** : Enregistrer les routes CRUD + togglePublish dans `routes/admin.php`.
  - **Critères d'acceptation** :
    - Routes resource CRUD sous `/dashboard/comparatifs`
    - Route `POST /dashboard/comparatifs/{comparison}/toggle-publish`
    - Routes nommées `admin.comparisons.*`

- [ ] **T-045 — Créer les pages Vue admin pour les comparatifs**
  - **Description** : Créer les pages Index, Create, Edit pour les comparatifs.
  - **Critères d'acceptation** :
    - `Admin/Comparisons/Index.vue` : tableau avec "Outil A vs Outil B", statut génération (badge), statut publication, actions
    - `Admin/Comparisons/Create.vue` : deux sélecteurs d'outils (recherche/autocomplete), aperçu du slug généré
    - `Admin/Comparisons/Edit.vue` : header "Outil A vs Outil B" avec logos, textarea contenu (markdown), textarea verdict, champs SEO, bouton "Générer avec IA" (Phase 2), badge statut
    - Layout admin, messages flash

- [ ] **T-046 — Tests feature du CRUD Comparatifs**
  - **Description** : Écrire des tests Pest pour le CRUD comparatifs.
  - **Critères d'acceptation** :
    - Test store avec paire valide, store avec paire dupliquée (doit échouer)
    - Test store avec tool_a_id == tool_b_id (doit échouer)
    - Test update contenu et verdict
    - Test togglePublish
    - Test destroy
    - Tous les tests passent

### 1.13 — Dashboard Admin : Statistiques

- [ ] **T-047 — Mettre à jour le `DashboardController` avec les statistiques**
  - **Description** : Modifier la page Dashboard existante pour afficher les statistiques du site et les activités récentes.
  - **Critères d'acceptation** :
    - Le controller passe les données : nombre total d'outils (publiés et brouillons), nombre de comparatifs, nombre de catégories, nombre de tags, 5 derniers outils ajoutés, 5 derniers jobs de génération (statut)
    - Le Dashboard.vue est mis à jour pour afficher : cartes de statistiques (outils, comparatifs, catégories, tags), liste des outils récents avec statut, liste des dernières générations avec statut (icône de chargement si en cours)
    - Les chiffres sont cliquables et redirigent vers les pages admin correspondantes

- [ ] **T-048 — Tests feature du Dashboard admin**
  - **Description** : Écrire des tests Pest pour le dashboard admin.
  - **Critères d'acceptation** :
    - Test que le dashboard affiche les bonnes statistiques
    - Test accès non authentifié → redirect login
    - Tous les tests passent

### 1.14 — Wayfinder & Pint

- [ ] **T-049 — Générer les routes Wayfinder et formatter avec Pint**
  - **Description** : Régénérer les types TypeScript Wayfinder pour les nouvelles routes admin et formatter tout le code PHP avec Pint.
  - **Critères d'acceptation** :
    - `php artisan wayfinder:generate` exécuté sans erreur
    - Les actions TypeScript sont générées pour tous les controllers admin
    - `vendor/bin/pint --dirty` exécuté, aucune erreur de formatage
    - Tous les tests passent toujours

---

## Phase 2 — Génération IA

### 2.1 — Configuration Laravel AI

- [ ] **T-050 — Configurer Laravel AI avec le provider OpenAI**
  - **Description** : Configurer le package Laravel AI pour utiliser GPT-4o comme modèle par défaut.
  - **Critères d'acceptation** :
    - `config/ai.php` configuré avec le provider OpenAI et le modèle `gpt-4o`
    - La clé API est lue depuis `OPENAI_API_KEY` dans `.env`
    - Un test rapide via tinker confirme que l'API répond correctement
    - La documentation Laravel AI est consultée pour s'assurer de la bonne configuration

### 2.2 — Job : Fetch des logos

- [ ] **T-051 — Créer le job `FetchToolLogo`**
  - **Description** : Créer un job dispatché en queue qui récupère automatiquement le logo/favicon d'un outil depuis son site web.
  - **Critères d'acceptation** :
    - Job dans `app/Jobs/FetchToolLogo.php`
    - Implémente `ShouldQueue`
    - Logique : 1) Tente de récupérer le favicon depuis le site de l'outil (parse le HTML pour trouver les balises link rel="icon"), 2) Fallback vers Google Favicon API (`https://www.google.com/s2/favicons?domain=...&sz=128`), 3) Stocke le fichier dans `storage/app/public/logos/{slug}.png`, 4) Met à jour `tool.logo_path`
    - Gestion des erreurs : si le fetch échoue, log l'erreur mais ne fait pas échouer le job
    - Retry 3 fois avec backoff
    - Timeout de 30 secondes

- [ ] **T-052 — Tests du job `FetchToolLogo`**
  - **Description** : Écrire des tests Pest pour le job FetchToolLogo avec des réponses HTTP mockées.
  - **Critères d'acceptation** :
    - Test avec un favicon trouvé dans le HTML → logo stocké et `logo_path` mis à jour
    - Test fallback Google Favicon API
    - Test avec erreur HTTP → pas de crash, `logo_path` reste null
    - Tous les tests passent

### 2.3 — Job : Génération contenu outil

- [ ] **T-053 — Créer les templates de prompts IA**
  - **Description** : Créer les templates de prompts structurés pour la génération de contenu par l'IA. Stocker dans des fichiers dédiés.
  - **Critères d'acceptation** :
    - Fichier `app/AI/Prompts/GenerateToolPrompt.php` (ou config/prompts) : prompt système + prompt utilisateur pour générer une fiche outil complète
    - Le prompt demande : description (~150 chars), contenu (~500-1000 mots en markdown, SEO-optimisé, en français), liste d'avantages (5-8), liste d'inconvénients (3-5), fonctionnalités principales (5-10), FAQ (5-8 questions/réponses), pricing (structure plans), plateformes supportées, tags suggérés, alternatives suggérées (5-10 noms d'outils), meta_title (~60 chars), meta_description (~155 chars)
    - Le prompt spécifie le format de sortie JSON attendu
    - Le prompt précise le ton : professionnel, orienté développeurs, objectif
    - Fichier `app/AI/Prompts/GenerateComparisonPrompt.php` : prompt pour les comparatifs
    - Fichier `app/AI/Prompts/SuggestAlternativesPrompt.php` : prompt pour les suggestions

- [ ] **T-054 — Créer le job `GenerateToolContent`**
  - **Description** : Créer un job qui utilise Laravel AI pour générer le contenu complet d'une fiche outil.
  - **Critères d'acceptation** :
    - Job dans `app/Jobs/GenerateToolContent.php`
    - Implémente `ShouldQueue`
    - Reçoit un `Tool` en paramètre
    - Flow : 1) Met `generation_status` à `generating`, 2) Appelle l'API OpenAI via Laravel AI avec le prompt structuré, 3) Parse la réponse JSON, 4) Met à jour tous les champs du Tool (description, content, pros, cons, features, faq, pricing, platforms, meta_title, meta_description), 5) Crée les tags suggérés s'ils n'existent pas et les attache au tool, 6) Met `generation_status` à `completed` et `generated_at` à now(), 7) Dispatch `FetchToolLogo` et `SuggestAlternativesAndComparisons`
    - En cas d'erreur : `generation_status` → `failed`, log l'erreur
    - Retry 2 fois avec backoff exponentiel
    - Timeout de 120 secondes

- [ ] **T-055 — Tests du job `GenerateToolContent`**
  - **Description** : Écrire des tests Pest pour le job avec l'API IA mockée.
  - **Critères d'acceptation** :
    - Test avec réponse IA valide → tous les champs du tool sont mis à jour, status `completed`
    - Test avec erreur API → status `failed`, champs non modifiés
    - Test que `FetchToolLogo` et `SuggestAlternativesAndComparisons` sont dispatchés après succès
    - Test que les tags sont créés et attachés
    - Tous les tests passent

### 2.4 — Job : Génération contenu comparatif

- [ ] **T-056 — Créer le job `GenerateComparisonContent`**
  - **Description** : Créer un job qui génère le contenu d'un comparatif entre deux outils.
  - **Critères d'acceptation** :
    - Job dans `app/Jobs/GenerateComparisonContent.php`
    - Implémente `ShouldQueue`
    - Reçoit un `Comparison` en paramètre
    - Flow : 1) Met `generation_status` à `generating`, 2) Appelle l'API avec le prompt de comparaison (inclut les données des deux outils comme contexte), 3) Parse la réponse, 4) Met à jour `content`, `verdict`, `meta_title`, `meta_description`, 5) Status → `completed`, `generated_at` → now()
    - Gestion d'erreur identique à `GenerateToolContent`
    - Timeout de 120 secondes

- [ ] **T-057 — Tests du job `GenerateComparisonContent`**
  - **Description** : Écrire des tests Pest pour le job avec l'API IA mockée.
  - **Critères d'acceptation** :
    - Test succès : contenu et verdict mis à jour, status completed
    - Test échec : status failed
    - Tous les tests passent

### 2.5 — Job : Suggestion d'alternatives

- [ ] **T-058 — Créer le job `SuggestAlternativesAndComparisons`**
  - **Description** : Créer un job qui demande à l'IA de suggérer des alternatives pour un outil et crée les entrées correspondantes.
  - **Critères d'acceptation** :
    - Job dans `app/Jobs/SuggestAlternativesAndComparisons.php`
    - Implémente `ShouldQueue`
    - Reçoit un `Tool` en paramètre
    - Flow : 1) Appelle l'API IA avec le prompt de suggestion, 2) Pour chaque alternative suggérée : vérifie si le Tool existe déjà (par nom), sinon crée un Tool en brouillon (name + url si fourni + category_id déduite, generation_status: pending), 3) Attache comme alternative (table pivot `tool_alternatives`), 4) Pour chaque alternative : crée un Comparison si la paire n'existe pas (generation_status: pending)
    - Ne recrée pas les alternatives déjà existantes
    - Log le nombre d'alternatives et comparatifs créés

- [ ] **T-059 — Tests du job `SuggestAlternativesAndComparisons`**
  - **Description** : Écrire des tests Pest avec IA mockée.
  - **Critères d'acceptation** :
    - Test : crée des Tools brouillon pour les alternatives non existantes
    - Test : ne duplique pas les alternatives déjà existantes
    - Test : crée des Comparisons pour chaque paire
    - Test : ne duplique pas les Comparisons existantes
    - Tous les tests passent

### 2.6 — Intégration admin : Endpoints de génération

- [ ] **T-060 — Créer le `GenerationController` admin**
  - **Description** : Créer un contrôleur pour dispatcher les jobs de génération depuis l'admin.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/Admin/GenerationController.php`
    - Méthodes :
      - `generateTool(Tool $tool)` : dispatch `GenerateToolContent`, redirige avec message flash
      - `generateComparison(Comparison $comparison)` : dispatch `GenerateComparisonContent`, redirige avec message flash
      - `suggestAlternatives(Tool $tool)` : dispatch `SuggestAlternativesAndComparisons`, redirige
      - `fetchLogo(Tool $tool)` : dispatch `FetchToolLogo`, redirige
    - Vérifie que le statut n'est pas déjà `generating` avant de dispatcher

- [ ] **T-061 — Créer les routes de génération**
  - **Description** : Enregistrer les routes de génération dans `routes/admin.php`.
  - **Critères d'acceptation** :
    - `POST /dashboard/outils/{tool}/generate` → generateTool
    - `POST /dashboard/comparatifs/{comparison}/generate` → generateComparison
    - `POST /dashboard/outils/{tool}/suggest-alternatives` → suggestAlternatives
    - `POST /dashboard/outils/{tool}/fetch-logo` → fetchLogo
    - Routes nommées `admin.generate.*`

- [ ] **T-062 — Brancher les boutons de génération dans les pages admin**
  - **Description** : Connecter les boutons "Générer avec IA" dans les pages d'édition aux endpoints de génération.
  - **Critères d'acceptation** :
    - Page `Admin/Tools/Edit.vue` : bouton "Générer avec IA" fonctionnel, affiche un spinner pendant le dispatch, message flash de confirmation, désactivé si statut = `generating`
    - Page `Admin/Tools/Edit.vue` : bouton "Fetch logo" fonctionnel
    - Page `Admin/Comparisons/Edit.vue` : bouton "Générer avec IA" fonctionnel
    - Composant `AlternativesPanel.vue` : bouton "Suggérer des alternatives" fonctionnel, boutons "Créer l'outil" et "Générer" par alternative fonctionnels
    - Composant `ComparisonsPanel.vue` : bouton "Générer le comparatif" par comparatif fonctionnel

- [ ] **T-063 — Tests feature des endpoints de génération**
  - **Description** : Écrire des tests Pest pour les endpoints de génération.
  - **Critères d'acceptation** :
    - Test que le job est dispatché (Queue::fake)
    - Test que le statut `generating` empêche un nouveau dispatch
    - Test accès non authentifié
    - Tous les tests passent

### 2.7 — Wayfinder & Pint Phase 2

- [ ] **T-064 — Régénérer Wayfinder et formatter avec Pint**
  - **Description** : Régénérer les routes TypeScript et formatter le code après la Phase 2.
  - **Critères d'acceptation** :
    - `php artisan wayfinder:generate` sans erreur
    - `vendor/bin/pint --dirty` sans erreur
    - Tous les tests passent

---

## Phase 3 — Frontend Public

### 3.1 — Layout public

- [ ] **T-065 — Créer le layout public `PublicLayout.vue`**
  - **Description** : Créer un layout distinct pour les pages publiques (sans sidebar admin), avec header et footer.
  - **Critères d'acceptation** :
    - Fichier `resources/js/layouts/PublicLayout.vue`
    - Structure : `<header>` + `<main>` (slot) + `<footer>`
    - Responsive (mobile-first)
    - Support dark/light mode via le système d'apparence existant
    - Le layout est utilisable par les pages publiques via `layout: PublicLayout` dans le `defineOptions` de la page

- [ ] **T-066 — Créer le composant `PublicHeader.vue`**
  - **Description** : Créer le header de navigation public.
  - **Critères d'acceptation** :
    - Logo "Tool" cliquable (retour accueil)
    - Navigation : liens "Outils", "Catégories", "Comparatifs"
    - Bouton recherche avec icône + label "⌘K"
    - Toggle dark/light mode
    - Sur mobile : menu hamburger qui ouvre un menu overlay
    - Le lien actif est visuellement distinct
    - Design épuré, responsive

- [ ] **T-067 — Créer le composant `PublicFooter.vue`**
  - **Description** : Créer le footer du site public.
  - **Critères d'acceptation** :
    - Grille de liens : catégories principales (liens vers les pages catégories)
    - Section liens : Accueil, Outils, Catégories, Comparatifs
    - Copyright et mentions légales
    - Design responsive (colonnes sur desktop, empilé sur mobile)

- [ ] **T-068 — Créer le composant `MobileMenu.vue`**
  - **Description** : Créer le menu mobile overlay pour la navigation publique.
  - **Critères d'acceptation** :
    - S'ouvre via le bouton hamburger dans le header
    - Affiche tous les liens de navigation
    - Animation d'ouverture/fermeture fluide
    - Se ferme au clic sur un lien ou au clic en dehors
    - Occupe toute la hauteur de l'écran

### 3.2 — Composants réutilisables publics

- [ ] **T-069 — Créer le composant `ToolCard.vue`**
  - **Description** : Créer la carte d'outil réutilisable pour les grilles et listings.
  - **Critères d'acceptation** :
    - Props : tool (objet avec name, slug, description, logo_path, category, tags, platforms)
    - Affiche : logo (avec fallback placeholder), nom, catégorie (badge), description courte (tronquée si nécessaire), 3-4 tags (badges cliquables), icônes de plateformes
    - Lien cliquable vers `/outil/{slug}`
    - Effet hover subtil (scale + shadow)
    - Responsive : s'adapte à la grille parente
    - Badge "Sponsorisé" si `is_sponsored` (prévu pour plus tard)

- [ ] **T-070 — Créer le composant `ComparisonCard.vue`**
  - **Description** : Créer la carte de comparatif réutilisable.
  - **Critères d'acceptation** :
    - Props : comparison (objet avec slug, toolA, toolB)
    - Affiche : logo outil A + "VS" + logo outil B, noms des deux outils, description courte du comparatif
    - Lien cliquable vers `/comparatif/{slug}`
    - Effet hover

- [ ] **T-071 — Créer le composant `CategoryCard.vue`**
  - **Description** : Créer la carte de catégorie réutilisable.
  - **Critères d'acceptation** :
    - Props : category (objet avec name, slug, icon, tools_count)
    - Affiche : icône Lucide, nom, nombre d'outils
    - Lien cliquable vers `/categorie/{slug}`
    - Effet hover

- [ ] **T-072 — Créer les composants `TagBadge.vue` et `PlatformBadge.vue`**
  - **Description** : Créer les badges réutilisables pour tags et plateformes.
  - **Critères d'acceptation** :
    - `TagBadge.vue` : badge cliquable avec le nom du tag, lien vers `/tag/{slug}`, style discret
    - `PlatformBadge.vue` : badge avec icône + label (Web, Desktop, Mobile, API, CLI), non cliquable, style distinct par plateforme

- [ ] **T-073 — Créer le composant `PricingTable.vue`**
  - **Description** : Créer le composant d'affichage des tarifs d'un outil.
  - **Critères d'acceptation** :
    - Props : pricing (array de plans)
    - Affiche chaque plan : nom, prix, période (mensuel/annuel), features incluses
    - Mise en avant du plan populaire si indiqué
    - Badge "Gratuit" / "Freemium" / "Payant" selon le type
    - Responsive : cartes empilées sur mobile, côte à côte sur desktop

- [ ] **T-074 — Créer le composant `FaqAccordion.vue`**
  - **Description** : Créer l'accordéon FAQ réutilisable.
  - **Critères d'acceptation** :
    - Props : faq (array de {question, answer})
    - Chaque item est repliable/dépliable
    - Un seul item ouvert à la fois (ou tous fermés)
    - Animation d'ouverture/fermeture fluide
    - Utilise les composants Reka UI Collapsible si approprié

- [ ] **T-075 — Créer le composant `ProsCons.vue`**
  - **Description** : Créer le composant d'affichage avantages/inconvénients.
  - **Critères d'acceptation** :
    - Props : pros (array de strings), cons (array de strings)
    - Affiche deux colonnes : avantages (icône check vert) et inconvénients (icône x rouge)
    - Responsive : colonnes côte à côte sur desktop, empilées sur mobile

- [ ] **T-076 — Créer le composant `MarkdownContent.vue`**
  - **Description** : Créer un composant qui rend du contenu Markdown en HTML sécurisé. Installer un parser Markdown si nécessaire.
  - **Critères d'acceptation** :
    - Props : content (string markdown)
    - Rend le markdown en HTML avec les balises appropriées
    - Styles typographiques appliqués (headings, paragraphes, listes, code blocks, liens)
    - Sécurisé : pas de XSS (sanitize le HTML)
    - Package markdown installé (ex: `marked` ou `markdown-it`)

### 3.3 — Homepage

- [ ] **T-077 — Créer le `HomeController`**
  - **Description** : Créer le contrôleur pour la homepage publique qui fournit les données nécessaires.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/HomeController.php`
    - Méthode `__invoke()` retourne une page Inertia avec : outils populaires (6-12 outils publiés les plus récents), comparatifs tendance (4-6 comparatifs publiés), catégories avec count d'outils, tags populaires (les 20 tags les plus utilisés)
    - Eager loading des relations pour éviter N+1
    - Route `GET /` pointant vers ce controller (remplacer la route existante)

- [ ] **T-078 — Créer/Refondre la page `Welcome.vue` en Homepage**
  - **Description** : Transformer la page Welcome existante en homepage du site public.
  - **Critères d'acceptation** :
    - Utilise le `PublicLayout`
    - **Section Hero** : titre accrocheur ("Découvrez les meilleurs outils pour développeurs" ou similaire), sous-titre, bouton CTA vers `/outils`
    - **Section outils populaires** : titre + grille de `ToolCard` (responsive : 1 col mobile, 2 col tablette, 3 col desktop)
    - **Section comparatifs tendance** : titre + grille de `ComparisonCard`
    - **Section catégories** : titre + grille de `CategoryCard`
    - **Section tags populaires** : nuage de `TagBadge` cliquables
    - **Emplacement sponsor** : espace réservé visuellement (ex: section "Votre outil ici" ou banner, non fonctionnel pour l'instant)
    - Design soigné, dark/light compatible

- [ ] **T-079 — Tests feature de la Homepage**
  - **Description** : Écrire des tests Pest pour la homepage.
  - **Critères d'acceptation** :
    - Test que la page se charge (status 200)
    - Test que les outils publiés sont affichés (et pas les brouillons)
    - Test que les comparatifs publiés sont affichés
    - Test que les catégories sont affichées avec le count
    - Tous les tests passent

### 3.4 — Page fiche outil

- [ ] **T-080 — Créer le `PublicToolController`**
  - **Description** : Créer le contrôleur pour les pages publiques d'outils.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/PublicToolController.php`
    - Méthode `index()` : retourne la liste paginée des outils publiés avec filtres (catégorie, tag, plateforme, recherche texte, tri)
    - Méthode `show(Tool $tool)` : retourne la fiche complète d'un outil publié avec catégorie, tags, alternatives (publiées uniquement), comparaisons publiées. Retourne 404 si non publié
    - Eager loading de toutes les relations nécessaires

- [ ] **T-081 — Créer les routes publiques pour les outils**
  - **Description** : Enregistrer les routes publiques des outils.
  - **Critères d'acceptation** :
    - `GET /outils` → PublicToolController@index (nommée `tools.index`)
    - `GET /outil/{tool:slug}` → PublicToolController@show (nommée `tools.show`)
    - Routes accessibles sans authentification

- [ ] **T-082 — Créer la page Vue `Public/Tools/Show.vue`**
  - **Description** : Créer la page détaillée d'un outil.
  - **Critères d'acceptation** :
    - Utilise le `PublicLayout`
    - **Header** : logo de l'outil (grande taille), nom, catégorie (badge lien), lien site officiel (bouton externe avec icône)
    - **Section description** : contenu markdown rendu via `MarkdownContent`
    - **Section fonctionnalités** : liste avec icônes des fonctionnalités principales
    - **Section avantages/inconvénients** : composant `ProsCons`
    - **Section pricing** : composant `PricingTable`
    - **Section plateformes** : badges `PlatformBadge`
    - **Section tags** : badges `TagBadge` cliquables
    - **Section FAQ** : composant `FaqAccordion`
    - **Section alternatives** : grille de `ToolCard` (outils alternatifs publiés)
    - **Section comparatifs** : liens vers les pages VS disponibles ("Voir le comparatif Tool vs X")
    - Toutes les sections s'affichent conditionnellement (si la donnée existe)

- [ ] **T-083 — Créer la page Vue `Public/Tools/Index.vue`**
  - **Description** : Créer la page listing de tous les outils avec filtres et pagination.
  - **Critères d'acceptation** :
    - Utilise le `PublicLayout`
    - Titre "Tous les outils"
    - Barre de recherche texte
    - Filtres : sélecteur catégorie, sélecteur plateforme, tri (récent, alphabétique)
    - Grille de `ToolCard` responsive
    - Pagination (Inertia links)
    - Les filtres utilisent les query params URL (bookmarkable)
    - État vide : message "Aucun outil trouvé"

- [ ] **T-084 — Tests feature des pages outils publiques**
  - **Description** : Écrire des tests Pest pour les pages publiques d'outils.
  - **Critères d'acceptation** :
    - Test `show` : affiche un outil publié (200)
    - Test `show` : retourne 404 pour un outil non publié
    - Test `show` : retourne 404 pour un slug inexistant
    - Test `index` : liste paginée des outils publiés
    - Test `index` : filtrage par catégorie
    - Test `index` : recherche par nom
    - Tous les tests passent

### 3.5 — Page catégorie

- [ ] **T-085 — Créer le `PublicCategoryController`**
  - **Description** : Créer le contrôleur pour les pages publiques de catégories.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/PublicCategoryController.php`
    - Méthode `index()` : retourne toutes les catégories ordonnées par `sort_order` avec le count d'outils publiés
    - Méthode `show(Category $category)` : retourne la catégorie avec ses outils publiés paginés, filtrable par plateforme et tag, triable

- [ ] **T-086 — Créer les routes publiques pour les catégories**
  - **Description** : Enregistrer les routes publiques des catégories.
  - **Critères d'acceptation** :
    - `GET /categories` → PublicCategoryController@index (nommée `categories.index`)
    - `GET /categorie/{category:slug}` → PublicCategoryController@show (nommée `categories.show`)

- [ ] **T-087 — Créer les pages Vue publiques pour les catégories**
  - **Description** : Créer les pages Index et Show des catégories.
  - **Critères d'acceptation** :
    - `Public/Categories/Index.vue` : grille de `CategoryCard` responsive, layout public
    - `Public/Categories/Show.vue` : header (icône + nom + description), filtres (plateforme, tag, tri), grille de `ToolCard` paginée, layout public, état vide si aucun outil

- [ ] **T-088 — Tests feature des pages catégories publiques**
  - **Description** : Tests Pest pour les catégories publiques.
  - **Critères d'acceptation** :
    - Test index : affiche les catégories
    - Test show : affiche la catégorie avec ses outils publiés
    - Test show : n'affiche pas les outils non publiés
    - Tous les tests passent

### 3.6 — Page comparatif

- [ ] **T-089 — Créer le `PublicComparisonController`**
  - **Description** : Créer le contrôleur pour les pages publiques de comparatifs.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/PublicComparisonController.php`
    - Méthode `index()` : retourne les comparatifs publiés paginés avec toolA et toolB
    - Méthode `show(Comparison $comparison)` : retourne le comparatif publié avec toolA et toolB complets. 404 si non publié

- [ ] **T-090 — Créer les routes publiques pour les comparatifs**
  - **Description** : Enregistrer les routes publiques des comparatifs.
  - **Critères d'acceptation** :
    - `GET /comparatifs` → PublicComparisonController@index (nommée `comparisons.index`)
    - `GET /comparatif/{comparison:slug}` → PublicComparisonController@show (nommée `comparisons.show`)

- [ ] **T-091 — Créer les pages Vue publiques pour les comparatifs**
  - **Description** : Créer les pages Index et Show des comparatifs.
  - **Critères d'acceptation** :
    - `Public/Comparisons/Index.vue` : grille de `ComparisonCard` paginée, layout public
    - `Public/Comparisons/Show.vue` :
      - Header : logo A + "VS" + logo B, noms des deux outils
      - Tableau comparatif : fonctionnalités côte à côte (si les deux outils ont des features)
      - Contenu détaillé : markdown rendu via `MarkdownContent`
      - Verdict : section mise en avant avec la recommandation
      - Pricing comparé : les deux `PricingTable` côte à côte
      - Liens vers les fiches individuelles des deux outils
      - Layout public

- [ ] **T-092 — Tests feature des pages comparatifs publiques**
  - **Description** : Tests Pest pour les comparatifs publics.
  - **Critères d'acceptation** :
    - Test show : affiche un comparatif publié (200)
    - Test show : 404 si non publié
    - Test index : liste les comparatifs publiés
    - Tous les tests passent

### 3.7 — Page tag

- [ ] **T-093 — Créer le `PublicTagController`**
  - **Description** : Créer le contrôleur pour les pages publiques de tags.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/PublicTagController.php`
    - Méthode `show(Tag $tag)` : retourne le tag avec ses outils publiés paginés

- [ ] **T-094 — Créer la route publique pour les tags**
  - **Description** : Enregistrer la route publique du tag.
  - **Critères d'acceptation** :
    - `GET /tag/{tag:slug}` → PublicTagController@show (nommée `tags.show`)

- [ ] **T-095 — Créer la page Vue `Public/Tags/Show.vue`**
  - **Description** : Créer la page d'un tag avec la liste de ses outils.
  - **Critères d'acceptation** :
    - Header : nom du tag, nombre d'outils
    - Grille de `ToolCard` paginée
    - Layout public
    - État vide si aucun outil

- [ ] **T-096 — Tests feature de la page tag**
  - **Description** : Tests Pest pour la page tag.
  - **Critères d'acceptation** :
    - Test show : affiche le tag avec ses outils publiés
    - Test show : 404 pour un slug inexistant
    - Tous les tests passent

### 3.8 — Command Palette (⌘K)

- [ ] **T-097 — Créer le `SearchController` (endpoint API)**
  - **Description** : Créer un endpoint API qui retourne les résultats de recherche pour la command palette.
  - **Critères d'acceptation** :
    - Contrôleur dans `app/Http/Controllers/SearchController.php`
    - Méthode `__invoke(Request $request)` : reçoit un `query` (string), retourne un JSON avec résultats groupés
    - Recherche dans : outils publiés (par nom, description), catégories (par nom), tags (par nom)
    - Résultats limités (5 par type max)
    - Chaque résultat contient : type, name, slug, url, description/info supplémentaire
    - Route `GET /api/search` ou `GET /search`

- [ ] **T-098 — Créer le composant `CommandPalette.vue`**
  - **Description** : Créer la modale de recherche rapide avec raccourci clavier ⌘K.
  - **Critères d'acceptation** :
    - S'ouvre avec ⌘K (Mac) / Ctrl+K (Windows/Linux)
    - Se ferme avec Escape ou clic en dehors
    - Input de recherche avec focus automatique à l'ouverture
    - Appel API debounced (300ms) à chaque frappe
    - Résultats groupés par type (Outils, Catégories, Tags) avec icône par type
    - Navigation clavier : flèches haut/bas pour sélectionner, Enter pour naviguer
    - Clic sur un résultat navigue vers la page correspondante et ferme la modale
    - État vide : "Tapez pour rechercher"
    - État aucun résultat : "Aucun résultat pour {query}"
    - Loading spinner pendant la recherche
    - Overlay sombre derrière la modale
    - Animation d'ouverture/fermeture
    - Intégré dans le `PublicLayout` (toujours disponible)

- [ ] **T-099 — Tests feature de la recherche**
  - **Description** : Tests Pest pour l'endpoint de recherche.
  - **Critères d'acceptation** :
    - Test recherche par nom d'outil → résultat trouvé
    - Test recherche par nom de catégorie → résultat trouvé
    - Test recherche vide → résultats vides
    - Test que seuls les outils publiés apparaissent
    - Tous les tests passent

### 3.9 — Responsive & Finitions Frontend

- [ ] **T-100 — S'assurer que toutes les pages publiques sont responsive**
  - **Description** : Vérifier et ajuster le responsive de toutes les pages publiques sur mobile, tablette et desktop.
  - **Critères d'acceptation** :
    - Homepage : hero, grilles, tags s'adaptent sur mobile (1 colonne)
    - Fiche outil : toutes les sections lisibles sur mobile, pros/cons empilés, pricing empilé
    - Page catégorie : grille adapée, filtres empilés sur mobile
    - Page comparatif : tableau scrollable horizontalement ou empilé sur mobile
    - Command palette : pleine largeur sur mobile
    - Header : menu hamburger fonctionnel sur mobile
    - Footer : colonnes empilées sur mobile
    - Aucun overflow horizontal sur aucune page
    - Texte lisible (taille min 16px sur mobile)

### 3.10 — Wayfinder & Pint Phase 3

- [ ] **T-101 — Régénérer Wayfinder et formatter avec Pint**
  - **Description** : Régénérer les routes TypeScript et formatter le code après la Phase 3.
  - **Critères d'acceptation** :
    - `php artisan wayfinder:generate` sans erreur
    - `vendor/bin/pint --dirty` sans erreur
    - Tous les tests passent

---

## Phase 4 — SEO

### 4.1 — Inertia SSR

- [ ] **T-102 — Activer et configurer Inertia SSR**
  - **Description** : Activer le rendu côté serveur (SSR) d'Inertia pour que toutes les pages publiques soient indexables par les moteurs de recherche.
  - **Critères d'acceptation** :
    - SSR activé dans `config/inertia.php`
    - Le fichier `resources/js/ssr.ts` existe déjà et est configuré
    - `npm run build:ssr` génère le bundle SSR sans erreur
    - Le process Node.js SSR démarre avec `php artisan inertia:start-ssr`
    - Les pages publiques sont rendues côté serveur (visible dans le code source HTML)
    - Les pages admin fonctionnent toujours correctement

### 4.2 — Service SEO & Meta Tags

- [ ] **T-103 — Créer le service/helper `SeoMeta`**
  - **Description** : Créer un service centralisé pour gérer les meta tags dynamiques sur toutes les pages.
  - **Critères d'acceptation** :
    - Classe `app/Services/SeoMeta.php` ou utilisation du sharing Inertia
    - Méthode pour définir : title, description, canonical URL, og tags, twitter card tags
    - Fallbacks intelligents : si pas de meta_title custom → titre généré automatiquement, si pas de meta_description → tronqué depuis la description
    - Le middleware `HandleInertiaRequests` est mis à jour pour partager les meta tags avec toutes les pages
    - Format du title : "{Page Title} — Tool" (avec séparateur et nom du site)

- [ ] **T-104 — Implémenter les meta tags sur toutes les pages publiques**
  - **Description** : Ajouter les meta tags dynamiques sur chaque page publique via le service SEO.
  - **Critères d'acceptation** :
    - **Homepage** : title "Tool — Découvrez les meilleurs outils pour développeurs", description pertinente
    - **Fiche outil** : title = meta_title ou "{name} — Avis, Prix, Alternatives | Tool", description = meta_description ou description tronquée
    - **Page catégorie** : title = "{name} — Meilleurs outils | Tool", description = description de la catégorie
    - **Page comparatif** : title = meta_title ou "{Tool A} vs {Tool B} — Comparatif | Tool", description adaptée
    - **Page tag** : title = "Outils {tag} | Tool"
    - **Pages listing** : titles et descriptions appropriés
    - Chaque page a un `<title>` et `<meta name="description">` uniques
    - Les meta tags sont visibles dans le code source HTML (grâce au SSR)

- [ ] **T-105 — Implémenter les balises Open Graph et Twitter Cards**
  - **Description** : Ajouter les balises OG et Twitter Card sur toutes les pages publiques.
  - **Critères d'acceptation** :
    - Chaque page publique a les balises : `og:title`, `og:description`, `og:url`, `og:type` (website pour homepage, article pour fiches), `og:image` (logo de l'outil ou image par défaut du site)
    - Balises Twitter : `twitter:card` (summary_large_image), `twitter:title`, `twitter:description`, `twitter:image`
    - L'image OG par défaut du site existe dans `public/images/og-default.png`
    - Pour les fiches outils : `og:image` utilise le logo de l'outil si disponible

- [ ] **T-106 — Implémenter les URLs canoniques**
  - **Description** : Ajouter une balise `<link rel="canonical">` sur chaque page publique.
  - **Critères d'acceptation** :
    - Chaque page publique a une balise canonical avec l'URL absolue de la page
    - Les pages paginées ont le canonical vers la page 1 (ou la page courante selon la stratégie)
    - Pas de paramètres de query dans les canonicals (sauf pagination si nécessaire)

### 4.3 — Structured Data (JSON-LD)

- [ ] **T-107 — Créer le composant/helper `JsonLd.vue`**
  - **Description** : Créer un composant Vue qui injecte du JSON-LD dans le `<head>` de la page.
  - **Critères d'acceptation** :
    - Composant `resources/js/components/JsonLd.vue`
    - Props : `schema` (objet JavaScript représentant le schema JSON-LD)
    - Rend une balise `<script type="application/ld+json">` avec le JSON sérialisé
    - Compatible SSR (le JSON-LD est présent dans le code source HTML)

- [ ] **T-108 — Implémenter le JSON-LD sur la Homepage**
  - **Description** : Ajouter les schemas WebSite et SearchAction sur la homepage.
  - **Critères d'acceptation** :
    - Schema `WebSite` avec name, url, potentialAction (SearchAction vers la page de recherche)
    - Schema `Organization` avec name, url, logo
    - Le JSON-LD est valide (testable avec le Rich Results Test de Google)

- [ ] **T-109 — Implémenter le JSON-LD sur les fiches outils**
  - **Description** : Ajouter les schemas SoftwareApplication et FAQPage sur chaque fiche outil.
  - **Critères d'acceptation** :
    - Schema `SoftwareApplication` avec name, url, description, applicationCategory, operatingSystem, offers (pricing)
    - Schema `FAQPage` avec les questions/réponses de la FAQ (si FAQ présente)
    - Schema `BreadcrumbList` avec le fil d'ariane complet

- [ ] **T-110 — Implémenter le JSON-LD sur les pages comparatifs**
  - **Description** : Ajouter les schemas Article et FAQPage sur les comparatifs.
  - **Critères d'acceptation** :
    - Schema `Article` avec headline, description, datePublished, dateModified
    - Schema `FAQPage` si le comparatif contient des FAQ
    - Schema `BreadcrumbList`

- [ ] **T-111 — Implémenter le JSON-LD sur les pages catégories**
  - **Description** : Ajouter le schema CollectionPage sur les catégories.
  - **Critères d'acceptation** :
    - Schema `CollectionPage` avec name, description, numberOfItems
    - Schema `BreadcrumbList`

- [ ] **T-112 — Implémenter les breadcrumbs HTML et JSON-LD sur toutes les pages**
  - **Description** : Ajouter des breadcrumbs visuels (HTML) et structurés (JSON-LD BreadcrumbList) sur toutes les pages publiques.
  - **Critères d'acceptation** :
    - Composant `PublicBreadcrumbs.vue` qui affiche le fil d'ariane visuel
    - Hiérarchie : Accueil > [Catégorie/Section] > [Page actuelle]
    - Fiche outil : Accueil > Catégorie > Outil
    - Catégorie : Accueil > Catégories > Catégorie
    - Comparatif : Accueil > Comparatifs > Outil A vs Outil B
    - Tag : Accueil > Tags > Tag
    - Chaque breadcrumb a un `BreadcrumbList` JSON-LD correspondant
    - Liens cliquables sauf la page courante

### 4.4 — Sitemap XML & robots.txt

- [ ] **T-113 — Créer la génération automatique du sitemap XML**
  - **Description** : Créer une commande Artisan ou un contrôleur qui génère un sitemap XML dynamique incluant toutes les pages publiques.
  - **Critères d'acceptation** :
    - Route `GET /sitemap.xml` retourne un XML valide
    - Inclut : homepage, toutes les fiches outils publiées, toutes les catégories, tous les comparatifs publiés, tous les tags ayant au moins 1 outil
    - Chaque URL a : `<loc>`, `<lastmod>` (basé sur updated_at), `<changefreq>`, `<priority>`
    - Priorités : homepage 1.0, catégories 0.8, outils 0.7, comparatifs 0.7, tags 0.5
    - Le sitemap est caché et régénéré quand du contenu est publié/modifié (ou régénéré via commande Artisan)
    - Le XML est conforme au protocole Sitemap

- [ ] **T-114 — Configurer le robots.txt**
  - **Description** : Créer un robots.txt approprié pour le site.
  - **Critères d'acceptation** :
    - Fichier `public/robots.txt` configuré
    - Autorise tous les robots sur les pages publiques
    - Bloque l'accès aux routes admin (`/dashboard/*`)
    - Bloque l'accès aux routes API internes
    - Référence le sitemap : `Sitemap: {url}/sitemap.xml`

- [ ] **T-115 — Configurer la pagination SEO-friendly**
  - **Description** : S'assurer que les pages paginées ont les bonnes balises SEO.
  - **Critères d'acceptation** :
    - Les pages paginées ont des balises `rel="next"` et `rel="prev"` dans le `<head>`
    - Les URLs de pagination sont propres (`?page=2`)
    - Les pages paginées n'ont pas le même title (ajouter " — Page X" si page > 1)

### 4.5 — Tests SEO

- [ ] **T-116 — Tests des meta tags et structured data**
  - **Description** : Écrire des tests Pest vérifiant la présence des meta tags et JSON-LD.
  - **Critères d'acceptation** :
    - Test que chaque page publique retourne un title unique
    - Test que chaque page a une meta description
    - Test que les balises OG sont présentes
    - Test que le sitemap XML est valide et contient les bonnes URLs
    - Test que le robots.txt bloque les bonnes routes
    - Tous les tests passent

---

## Phase 5 — Polish & Optimisation

### 5.1 — Dark/Light Mode

- [ ] **T-117 — Finaliser le dark/light mode sur toutes les pages publiques**
  - **Description** : S'assurer que le dark/light mode fonctionne parfaitement sur toutes les pages et composants publics.
  - **Critères d'acceptation** :
    - Le toggle dans le header public fonctionne (utilise le système d'apparence existant)
    - Dark mode est le mode par défaut
    - Toutes les pages publiques sont lisibles et esthétiques dans les deux modes
    - Les cartes, badges, tableaux, accordéons ont des styles adaptés pour chaque mode
    - Les couleurs de fond, de texte, de bordure sont cohérentes
    - Le markdown rendu est stylé correctement dans les deux modes
    - Pas de flash de contenu au chargement (FOUC)

### 5.2 — Animations & Transitions

- [ ] **T-118 — Ajouter les transitions de page Inertia**
  - **Description** : Implémenter des transitions fluides entre les pages.
  - **Critères d'acceptation** :
    - Transition fade ou slide subtile lors de la navigation entre pages
    - Pas de transition sur les redirections (login, etc.)
    - Les transitions ne bloquent pas l'interaction utilisateur
    - Performance fluide (pas de jank)

- [ ] **T-119 — Ajouter les animations hover et scroll**
  - **Description** : Ajouter des micro-animations sur les éléments interactifs.
  - **Critères d'acceptation** :
    - Cartes : effet hover (léger scale + shadow) avec transition CSS
    - Boutons : effet hover subtil
    - Sections de la homepage : apparition progressive au scroll (intersection observer)
    - Les animations respectent `prefers-reduced-motion`

### 5.3 — Performance

- [ ] **T-120 — Implémenter le lazy loading des images**
  - **Description** : Ajouter le lazy loading natif sur toutes les images (logos d'outils).
  - **Critères d'acceptation** :
    - Toutes les balises `<img>` de logos ont l'attribut `loading="lazy"`
    - Les logos au-dessus de la ligne de flottaison (above the fold) n'ont PAS le lazy loading
    - Les attributs `width` et `height` sont définis pour éviter le CLS (Cumulative Layout Shift)
    - Les images ont un attribut `alt` descriptif

- [ ] **T-121 — Optimiser les requêtes Eloquent (N+1)**
  - **Description** : Vérifier et corriger tous les problèmes N+1 dans les contrôleurs publics.
  - **Critères d'acceptation** :
    - Activer le mode strict de Laravel pour détecter les lazy loads (`Model::preventLazyLoading()`)
    - Corriger tous les N+1 détectés dans les contrôleurs publics et admin
    - Aucun lazy loading non intentionnel dans les pages publiques
    - Les requêtes sont optimisées (vérifiable via debugbar ou logs)

- [ ] **T-122 — Ajouter du cache sur les pages publiques**
  - **Description** : Mettre en place un cache pour les données des pages publiques les plus consultées.
  - **Critères d'acceptation** :
    - Cache des données de la homepage (outils populaires, catégories, comparatifs tendance) avec TTL de 1 heure
    - Cache de la liste des catégories avec TTL de 1 heure
    - Invalidation du cache quand un outil/catégorie/comparatif est publié ou modifié
    - Cache du sitemap XML avec invalidation à la publication
    - Les données en cache sont servies rapidement

### 5.4 — Emplacement Sponsor

- [ ] **T-123 — Prévoir les emplacements sponsors dans le design**
  - **Description** : Ajouter les emplacements visuels réservés aux sponsors dans les pages clés.
  - **Critères d'acceptation** :
    - **Homepage** : emplacement en haut de la section outils populaires (banner ou rangée de logos sponsors)
    - **Page catégorie** : emplacement en haut de la grille d'outils (carte "sponsorisée" mise en avant)
    - Les emplacements sont visibles mais élégants (pas intrusifs)
    - Un composant `SponsorPlaceholder.vue` réutilisable (affiche "Votre outil ici" ou rien si aucun sponsor)
    - Les outils marqués `is_sponsored` apparaissent en premier dans les listes avec un badge "Sponsorisé"
    - Non fonctionnel pour l'instant (juste le design et le positionnement)

### 5.5 — Tests finaux

- [ ] **T-124 — Lancer la suite complète de tests**
  - **Description** : Exécuter tous les tests et corriger les éventuels échecs.
  - **Critères d'acceptation** :
    - `php artisan test` passe avec 0 failure et 0 error
    - Couverture de toutes les pages publiques et admin
    - Couverture de tous les jobs de génération
    - Couverture de toutes les validations de formulaires

- [ ] **T-125 — Formatter tout le code**
  - **Description** : Exécuter Pint et Prettier sur tout le codebase pour garantir la cohérence.
  - **Critères d'acceptation** :
    - `vendor/bin/pint` sans changement (tout est déjà formatté)
    - `npm run format` sans changement
    - `npm run lint` sans erreur
    - Le code est propre et cohérent

---

## Récapitulatif

| Phase | Tâches | Description |
|-------|--------|-------------|
| Phase 1 | T-001 → T-049 | Fondations : BDD, modèles, admin CRUD |
| Phase 2 | T-050 → T-064 | Génération IA : jobs, prompts, intégration admin |
| Phase 3 | T-065 → T-101 | Frontend public : layout, pages, composants, recherche |
| Phase 4 | T-102 → T-116 | SEO : SSR, meta tags, JSON-LD, sitemap |
| Phase 5 | T-117 → T-125 | Polish : dark mode, animations, performance, sponsors |

**Total : 125 tâches**
