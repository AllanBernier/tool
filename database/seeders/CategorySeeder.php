<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database with predefined categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'IDE & Éditeurs de code',
                'description' => 'Environnements de développement intégrés et éditeurs de code pour écrire, déboguer et gérer vos projets efficacement.',
                'icon' => 'code',
                'sort_order' => 1,
            ],
            [
                'name' => 'CI/CD & DevOps',
                'description' => 'Outils d\'intégration continue, de déploiement continu et de gestion des opérations pour automatiser vos pipelines.',
                'icon' => 'git-branch',
                'sort_order' => 2,
            ],
            [
                'name' => 'Hébergement & Cloud',
                'description' => 'Services d\'hébergement web, plateformes cloud et solutions d\'infrastructure pour déployer vos applications.',
                'icon' => 'cloud',
                'sort_order' => 3,
            ],
            [
                'name' => 'Bases de données',
                'description' => 'Systèmes de gestion de bases de données relationnelles, NoSQL et outils d\'administration pour stocker et interroger vos données.',
                'icon' => 'database',
                'sort_order' => 4,
            ],
            [
                'name' => 'Monitoring & Observabilité',
                'description' => 'Solutions de surveillance, de logging et d\'observabilité pour suivre les performances et la santé de vos applications.',
                'icon' => 'activity',
                'sort_order' => 5,
            ],
            [
                'name' => 'Gestion de projet',
                'description' => 'Outils de planification, de suivi de tâches et de gestion de projet pour organiser le travail de votre équipe.',
                'icon' => 'kanban',
                'sort_order' => 6,
            ],
            [
                'name' => 'Communication & Collaboration',
                'description' => 'Plateformes de messagerie, de visioconférence et de collaboration pour faciliter le travail d\'équipe.',
                'icon' => 'message-circle',
                'sort_order' => 7,
            ],
            [
                'name' => 'Design & Prototypage',
                'description' => 'Outils de conception d\'interfaces, de prototypage et de design system pour créer des expériences utilisateur abouties.',
                'icon' => 'pen-tool',
                'sort_order' => 8,
            ],
            [
                'name' => 'Outils IA & Assistants de code',
                'description' => 'Assistants de programmation alimentés par l\'IA, outils de génération de code et copilotes intelligents.',
                'icon' => 'bot',
                'sort_order' => 9,
            ],
            [
                'name' => 'Sécurité & Tests',
                'description' => 'Outils de tests automatisés, d\'analyse de sécurité et de qualité de code pour garantir la fiabilité de vos projets.',
                'icon' => 'shield',
                'sort_order' => 10,
            ],
            [
                'name' => 'API & Backend',
                'description' => 'Frameworks backend, outils de gestion d\'API et plateformes pour construire des services robustes et scalables.',
                'icon' => 'server',
                'sort_order' => 11,
            ],
            [
                'name' => 'Frontend & Frameworks',
                'description' => 'Frameworks et bibliothèques frontend pour construire des interfaces web modernes, réactives et performantes.',
                'icon' => 'layout',
                'sort_order' => 12,
            ],
            [
                'name' => 'Open Source',
                'description' => 'Outils et plateformes dédiés à l\'écosystème open source, la contribution et la gestion de projets communautaires.',
                'icon' => 'heart',
                'sort_order' => 13,
            ],
            [
                'name' => 'No-Code / Low-Code',
                'description' => 'Plateformes de développement sans code ou à faible code pour créer des applications rapidement sans expertise technique approfondie.',
                'icon' => 'blocks',
                'sort_order' => 14,
            ],
            [
                'name' => 'Documentation & Knowledge',
                'description' => 'Outils de documentation technique, de bases de connaissances et de gestion du savoir pour structurer l\'information.',
                'icon' => 'book-open',
                'sort_order' => 15,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
