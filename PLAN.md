# Plan de Développement — Tool

Plateforme de découverte et comparaison d'outils pour développeurs.

---

## 1. Vue d'ensemble

| Aspect | Choix |
|--------|-------|
| Nom | Tool |
| Cible | Développeurs |
| Langue | Français uniquement |
| Stack | Laravel 12 + Inertia v2 SSR + Vue 3 + Tailwind CSS |
| IA | Laravel AI (package officiel) + OpenAI (GPT-4o) |
| Design | Dark/Light toggle (dark par défaut) |
| Auth | Pas de comptes publics — admin uniquement (Fortify déjà en place) |
| Monétisation | Sponsoring (prévu dans le design, implémenté plus tard) |
| SEO | Full SEO : SSR + Sitemap XML + JSON-LD + Open Graph + Twitter Cards + Canonical URLs + Breadcrumbs structurés |

---

## 2. Architecture des données

### 2.1 Modèles Eloquent

#### `Tool` (outil)
| Champ | Type | Description |
|-------|------|-------------|
| id | ulid | Identifiant unique |
| name | string | Nom de l'outil |
| slug | string (unique) | URL-friendly name |
| url | string | Site officiel |
| logo_path | string (nullable) | Chemin du logo fetchée automatiquement |
| description | text | Description courte (~150 caractères) pour les cartes |
| content | longText | Contenu complet de la fiche (~500-1000 mots, Markdown) |
| pricing | json | Structure tarifaire (plans, prix, gratuit/freemium/payant) |
| pros | json | Liste des avantages |
| cons | json | Liste des inconvénients |
| features | json | Fonctionnalités principales |
| faq | json | Questions/réponses |
| platforms | json | Plateformes supportées (web, desktop, mobile, api, cli) |
| category_id | foreignId | Catégorie principale |
| is_published | boolean | Publié ou brouillon |
| is_sponsored | boolean | Emplacement sponsorisé (pour plus tard) |
| sponsored_until | datetime (nullable) | Date de fin du sponsoring |
| generation_status | enum | pending / generating / completed / failed |
| meta_title | string (nullable) | Titre SEO custom |
| meta_description | text (nullable) | Description SEO custom |
| generated_at | datetime (nullable) | Date de dernière génération IA |
| published_at | datetime (nullable) | Date de publication |
| timestamps | | created_at / updated_at |

#### `Category` (catégorie)
| Champ | Type | Description |
|-------|------|-------------|
| id | ulid | Identifiant unique |
| name | string | Nom de la catégorie |
| slug | string (unique) | URL-friendly |
| description | text | Description pour SEO |
| icon | string | Nom de l'icône (Lucide) |
| sort_order | integer | Ordre d'affichage |
| meta_title | string (nullable) | Titre SEO custom |
| meta_description | text (nullable) | Description SEO custom |
| timestamps | | |

**Catégories prédéfinies (dev-oriented) :**
- IDE & Éditeurs de code
- CI/CD & DevOps
- Hébergement & Cloud
- Bases de données
- Monitoring & Observabilité
- Gestion de projet
- Communication & Collaboration
- Design & Prototypage
- Outils IA & Assistants de code
- Sécurité & Tests
- API & Backend
- Frontend & Frameworks
- Open Source
- No-Code / Low-Code
- Documentation & Knowledge

#### `Tag`
| Champ | Type | Description |
|-------|------|-------------|
| id | ulid | Identifiant unique |
| name | string | Nom du tag |
| slug | string (unique) | URL-friendly |
| timestamps | | |

#### `Comparison` (comparaison)
| Champ | Type | Description |
|-------|------|-------------|
| id | ulid | Identifiant unique |
| tool_a_id | foreignId | Premier outil |
| tool_b_id | foreignId | Second outil |
| slug | string (unique) | Format : `notion-vs-obsidian` |
| content | longText | Contenu du comparatif (~500-1000 mots, Markdown) |
| verdict | text (nullable) | Conclusion/recommandation |
| generation_status | enum | pending / generating / completed / failed |
| is_published | boolean | |
| meta_title | string (nullable) | |
| meta_description | text (nullable) | |
| generated_at | datetime (nullable) | |
| published_at | datetime (nullable) | |
| timestamps | | |

#### Tables pivot
- `tool_tag` — relation many-to-many Tool <-> Tag
- `tool_alternatives` — relation many-to-many Tool <-> Tool (alternatives)

### 2.2 Relations
```
Category hasMany Tool
Tool belongsTo Category
Tool belongsToMany Tag
Tool belongsToMany Tool (alternatives, self-referencing)
Comparison belongsTo Tool (tool_a)
Comparison belongsTo Tool (tool_b)
```

---

## 3. Structure des URLs (SEO optimisé)

| Page | URL | Exemple |
|------|-----|---------|
| Accueil | `/` | |
| Liste outils | `/outils` | |
| Fiche outil | `/outil/{slug}` | `/outil/notion` |
| Liste catégories | `/categories` | |
| Page catégorie | `/categorie/{slug}` | `/categorie/ide-editeurs-de-code` |
| Liste comparatifs | `/comparatifs` | |
| Page comparatif | `/comparatif/{slug}` | `/comparatif/notion-vs-obsidian` |
| Page tag | `/tag/{slug}` | `/tag/open-source` |
| Sitemap | `/sitemap.xml` | |

**Admin (protégé par auth) :**

| Page | URL |
|------|-----|
| Dashboard admin | `/dashboard` |
| Gestion outils | `/dashboard/outils` |
| Créer/éditer outil | `/dashboard/outils/{id}` |
| Gestion catégories | `/dashboard/categories` |
| Gestion comparatifs | `/dashboard/comparatifs` |
| Gestion tags | `/dashboard/tags` |
| Jobs IA | `/dashboard/generation` |

---

## 4. Pages publiques (Frontend)

### 4.1 Homepage (`/`)
- **Hero** : Titre accrocheur + sous-titre + barre de recherche
- **Filtres** : par profil/catégorie
- **Outils populaires** : grille de 6-12 cartes d'outils
- **Comparatifs tendance** : 4-6 comparatifs mis en avant
- **Catégories** : grille des catégories avec icônes et nombre d'outils
- **Emplacement sponsor** : prévu dans le design (header ou section dédiée)
- **Tags populaires** : nuage de tags cliquables

### 4.2 Fiche outil (`/outil/{slug}`)
- **Header** : Logo (fetch auto) + nom + catégorie + lien site officiel
- **Description** : contenu riche ~500-1000 mots
- **Fonctionnalités principales** : liste avec icônes
- **Avantages / Inconvénients** : colonnes côte à côte
- **Pricing** : tableau/cartes des plans tarifaires
- **Plateformes** : badges (Web, Desktop, Mobile, API, CLI)
- **Tags** : badges cliquables
- **FAQ** : accordéon avec questions/réponses
- **Alternatives** : grille de cartes d'outils similaires
- **Comparatifs disponibles** : liens vers les pages VS existantes
- **JSON-LD** : SoftwareApplication schema
- **Breadcrumbs** : Accueil > Catégorie > Outil

### 4.3 Page catégorie (`/categorie/{slug}`)
- **Header** : icône + nom + description
- **Filtres** : par plateforme, tags, tri (populaire, récent, alpha)
- **Grille** : cartes d'outils de la catégorie
- **JSON-LD** : CollectionPage schema
- **Breadcrumbs** : Accueil > Catégories > Catégorie

### 4.4 Page comparatif (`/comparatif/{slug}`)
- **Header** : Logo A vs Logo B
- **Tableau comparatif** : fonctionnalités côte à côte
- **Contenu détaillé** : analyse comparative (~500-1000 mots)
- **Verdict** : recommandation contextuelle
- **Pricing comparé** : plans côte à côte
- **FAQ** : questions fréquentes sur ce comparatif
- **JSON-LD** : FAQPage schema
- **Breadcrumbs** : Accueil > Comparatifs > Outil A vs Outil B

### 4.5 Page tag (`/tag/{slug}`)
- **Header** : nom du tag + nombre d'outils
- **Grille** : cartes d'outils associés au tag
- **Breadcrumbs** : Accueil > Tags > Tag

### 4.6 Pages listing (`/outils`, `/categories`, `/comparatifs`)
- Listes paginées avec filtres et recherche
- Meta tags dynamiques

### 4.7 Command Palette (⌘K)
- Modale de recherche rapide
- Recherche instantanée dans outils, catégories, tags
- Navigation rapide avec raccourci clavier
- Résultats groupés par type

---

## 5. Dashboard Admin (Inertia/Vue)

### 5.1 Dashboard principal (`/dashboard`)
- Statistiques : nombre d'outils, comparatifs, catégories
- Outils récemment ajoutés
- Jobs IA en cours / récents
- Statuts de génération

### 5.2 Gestion des outils (`/dashboard/outils`)
- **Liste** : table avec nom, catégorie, statut (publié/brouillon), statut génération
- **Création** : formulaire avec champs de base (nom, URL, catégorie)
- **Édition** : formulaire complet + preview du contenu généré
- **Panel latéral par outil** :
  - **Alternatives** : liste avec statuts (existant / en cours / à créer) + bouton "Créer l'outil"
  - **Pages VS** : liste des comparatifs possibles avec statuts + bouton "Générer le comparatif"
  - **Bouton "Générer/Régénérer avec IA"** : lance un job de génération

### 5.3 Gestion catégories (`/dashboard/categories`)
- CRUD simple avec drag & drop pour l'ordre

### 5.4 Gestion tags (`/dashboard/tags`)
- CRUD simple, merge de tags

### 5.5 Gestion comparatifs (`/dashboard/comparatifs`)
- Liste avec statuts de génération
- Création manuelle ou via génération IA

---

## 6. Génération IA (Laravel AI + OpenAI)

### 6.1 Package
- `laravel/ai` — package officiel Laravel pour l'intégration IA
- Provider : OpenAI (GPT-4o)

### 6.2 Jobs de génération

#### `GenerateToolContent`
- **Input** : nom de l'outil, URL, catégorie
- **Output** : description, content, pros, cons, features, faq, pricing, platforms, tags suggérés, alternatives suggérées, meta_title, meta_description
- **Prompt** : structuré pour produire du contenu SEO-friendly en français, ~500-1000 mots
- Le job fetch aussi automatiquement le logo (favicon) depuis le site de l'outil

#### `GenerateComparisonContent`
- **Input** : tool_a, tool_b
- **Output** : content comparatif, verdict, meta_title, meta_description
- **Prompt** : comparaison structurée, objective, SEO-optimisée

#### `SuggestAlternativesAndComparisons`
- **Input** : un outil
- **Output** : liste d'alternatives suggérées + comparatifs potentiels
- Crée automatiquement les entrées `Tool` (en brouillon) et `Comparison` (status: pending)
- Visible dans le panel admin avec les statuts

### 6.3 Workflow admin
1. L'admin crée un outil (nom + URL + catégorie minimum)
2. Click "Générer avec IA" → lance `GenerateToolContent`
3. Le contenu est généré et sauvegardé (statut: completed)
4. L'admin review et publie
5. Le panel latéral affiche les alternatives suggérées et comparatifs possibles
6. Pour chaque alternative/comparatif : statut + bouton d'action
7. L'admin peut lancer la génération en un clic

### 6.4 Fetch automatique des logos
- Récupération du favicon depuis le site de l'outil via une requête HTTP
- Fallback : Google Favicon API (`https://www.google.com/s2/favicons?domain=...&sz=128`)
- Stockage local dans `storage/app/public/logos/`
- Possibilité d'override depuis l'admin

---

## 7. SEO Technique

### 7.1 SSR (Server-Side Rendering)
- Inertia SSR activé via le process Node.js
- Toutes les pages publiques rendues côté serveur

### 7.2 Meta Tags dynamiques
- `<title>` et `<meta description>` dynamiques par page
- Custom overrides possibles depuis l'admin (meta_title, meta_description)
- Fallback intelligent si pas de custom

### 7.3 Open Graph & Twitter Cards
- og:title, og:description, og:image, og:url, og:type
- twitter:card, twitter:title, twitter:description, twitter:image
- Images OG générées dynamiquement (ou logo de l'outil)

### 7.4 JSON-LD Structured Data
- **Homepage** : WebSite + SearchAction
- **Fiche outil** : SoftwareApplication + FAQPage
- **Comparatif** : FAQPage + Article
- **Catégorie** : CollectionPage
- **Breadcrumbs** : BreadcrumbList sur chaque page

### 7.5 Sitemap XML
- Génération automatique via un package ou commande custom
- Inclut toutes les pages publiques : outils, catégories, comparatifs, tags
- Mise à jour automatique lors de la publication de contenu
- Soumission à Google Search Console

### 7.6 Autres optimisations SEO
- URLs canoniques sur chaque page
- Breadcrumbs structurés (HTML + JSON-LD)
- Attributs `alt` sur toutes les images
- Pagination SEO-friendly (rel="next"/"prev")
- robots.txt configuré
- Temps de chargement optimisé (lazy loading images, CSS/JS optimisés)

---

## 8. Design & UI

### 8.1 Thème
- Dark mode par défaut, light mode disponible
- Toggle dans le header
- Utilise le système d'apparence déjà en place

### 8.2 Layout public
- **Header** : logo + navigation (Outils, Catégories, Comparatifs) + toggle dark/light + bouton recherche ⌘K
- **Footer** : liens catégories, mentions légales, contact
- **Responsive** : mobile-first, menu hamburger sur mobile

### 8.3 Composants UI principaux
- **Carte outil** : logo + nom + catégorie + description courte + tags + plateformes
- **Carte comparatif** : Logo A vs Logo B + description courte
- **Carte catégorie** : icône + nom + nombre d'outils
- **Badge tag** : tag cliquable
- **Badge plateforme** : Web, Desktop, Mobile, API, CLI
- **Command palette** : modale recherche ⌘K
- **Accordéon FAQ** : pour les questions/réponses
- **Tableau pricing** : pour les plans tarifaires
- **Tableau comparatif** : features côte à côte

### 8.4 Animations
- Transitions de page fluides (Inertia)
- Hover sur les cartes
- Apparition progressive des éléments (scroll)

---

## 9. Phases de développement

### Phase 1 — Fondations (Base de données & Admin basique)
1. Installer et configurer `laravel/ai`
2. Créer les migrations, modèles, factories et seeders
3. Créer les catégories prédéfinies (seeder)
4. Créer le layout admin dans le dashboard existant
5. CRUD Admin : Catégories (avec drag & drop pour l'ordre)
6. CRUD Admin : Tags
7. CRUD Admin : Outils (formulaire de base)
8. CRUD Admin : Comparatifs
9. Tests pour chaque CRUD

### Phase 2 — Génération IA
1. Configurer Laravel AI avec OpenAI
2. Créer le job `GenerateToolContent`
3. Créer le job `GenerateComparisonContent`
4. Créer le job `SuggestAlternativesAndComparisons`
5. Créer le job de fetch automatique des logos
6. Intégrer les boutons de génération dans l'admin
7. Panel latéral : alternatives + comparatifs avec statuts
8. Tests pour les jobs

### Phase 3 — Frontend public
1. Créer le layout public (header, footer, navigation)
2. Homepage avec hero, grille d'outils, catégories, comparatifs
3. Page fiche outil complète
4. Page catégorie avec filtres
5. Page comparatif
6. Page tag
7. Pages listing (outils, catégories, comparatifs)
8. Command palette (⌘K)
9. Responsive mobile
10. Tests pour chaque page

### Phase 4 — SEO
1. Activer Inertia SSR
2. Meta tags dynamiques sur toutes les pages
3. Open Graph & Twitter Cards
4. JSON-LD sur chaque type de page
5. Sitemap XML automatique
6. Breadcrumbs structurés
7. robots.txt
8. Canonical URLs
9. Tests SEO

### Phase 5 — Polish & Optimisation
1. Dark/Light mode finalisé
2. Animations et transitions
3. Optimisation des performances (lazy loading, cache)
4. Emplacement sponsor prévu dans le design
5. Tests end-to-end
6. Déploiement

---

## 10. Stack technique récapitulatif

| Composant | Technologie |
|-----------|-------------|
| Backend | Laravel 12 |
| Frontend | Vue 3 + Inertia v2 |
| CSS | Tailwind CSS v4 |
| UI Components | Reka UI |
| Icons | Lucide Vue |
| SSR | Inertia SSR (Node.js) |
| IA | Laravel AI + OpenAI GPT-4o |
| Base de données | SQLite (dev) |
| Tests | Pest v4 |
| Code style | Pint + Prettier + ESLint |
| Routes TS | Wayfinder |
| Auth admin | Fortify |
