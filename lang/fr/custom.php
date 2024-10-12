<?php

return [

    // ------------------------ CUSTOM --------------------------
    'nav.section.platform' => 'Platforme',
    'nav.section.content' => 'Contenu',
    'nav.section.management' => 'Gestion',

    'models.cards' => 'Cartes (ancien)',
    'models.card' => 'Carte',

    'models.divisions' => 'Les Filières',
    'models.division' => 'Filière',

    'models.materials' => 'Les Matières',
    'models.material' => 'Matière',

    'models.units' => 'Les Unités',
    'models.unit' => 'Unité',

    'models.chapters' => 'Les Chapitres',
    'models.chapter' => 'Chapitre',

    'models.questions' => 'Les Questions',
    'models.question' => 'Question',

    'models.users' => 'Utilisateurs',
    'models.user' => 'Utilisateur',

    'models.subscriptions' => 'Les Abonnements',
    'models.subscription' => 'Abonnement',


    'models.discounts' => "Promotions",
    'models.discount' => "Promotion",

    'models.subscriptionCards' => "Cartes d'abonnement",
    'models.subscriptionCard' => "Carte d'abonnement",

    // # Division
    "forms.division.create.section.infos" => "Informations de la filière",
    "forms.division.create.section.image" => "Image de la filière",

    'models.division.name' => 'Nom de la filière',
    'models.division.description' => 'Description de la filière',

    // # Material
    "forms.material.create.section.infos" => "Informations de la matière",
    "forms.material.create.section.image" => "Image de la matière",

    'models.material.name' => 'Nom de la matière',
    'models.material.description' => 'Description de la matière',
    'models.material.code' => 'Code de la matière',
    'models.material.code.placeholder' => 'Code pour identifier la matière',
    'models.material.color' => 'Couleur de la matière',
    'models.material.division' => 'Filière',
    'models.material.action.details' => 'Détails de la matière',

    // # Unit
    'models.unit.name' => 'Nom de l\'unité',
    'models.unit.description' => 'Description de l\'unité',
    'models.unit.material' => 'Matière',
    'models.unit.action.details' => 'Détails de l\'unité',

    // # User
    'models.user.name' => 'Nom',
    'models.user.avatar' => 'Avatar',
    'models.user.email' => 'Email',
    'models.user.password' => 'Mot de passe',
    'models.user.phone' => 'Téléphone',
    'models.user.phone.empty' => 'Pas de numéro de téléphone',
    'models.user.roles' => 'Rôles',
    'models.user.email_verified' => 'Email vérifié',
    'models.user.email_not_verified' => 'Email non vérifié',
    'models.user.points' => 'Points',

    'models.user.wilaya' => 'Wilaya',
    'models.user.wilayas' => 'Wilayas',
    'models.user.commune' => 'Commune',
    'models.user.communes' => 'Communes',
    'models.user.wilaya.field' => "name",
    'models.user.commune.field' => "name",

    'models.user.tabs.all' => 'Tous',
    'models.user.tabs.students' => 'Étudiants',
    'models.user.tabs.with_roles' => 'Avec rôles',

    'models.user.perfonal_info' => 'Informations personnelles',
    'models.user.subscribtion' => 'Abonnement',
    'models.user.subscribed' => 'Abonné',
    'models.user.not_subscribed' => 'Non abonné',

    'models.user.roles_and_subscription' => 'Rôles et abonnement',


    // # Chapter
    'models.chapter.name' => 'Nom du chapitre',
    'models.chapter.description' => 'Description du chapitre',
    'models.chapter.unit' => 'Unité',
    'models.chapter.action.details' => 'Détails du chapitre',

    // # Chapter
    'models.question.tabs.infos' => 'Informations',
    'models.question.tabs.assets' => 'Ressources',
    'models.question.tabs.options' => 'Options',
    'models.question.question' => 'Question',
    'models.question.points' => 'Points',
    'models.question.hint' => 'Indice',
    'models.question.question_image' => 'Image de la question',
    'models.question.explaination_asset' => 'Ressource d\'explication',
    'models.question.hint_image' => 'Image d\'indice',
    'models.question.type' => 'Type de question',
    'models.question.types.multiple_choices' => "Choix unique / Choix multiples",
    'models.question.types.fill_in_the_blanks' => 'Remplir les blancs',
    'models.question.types.pick_the_intruder' => 'Choisir l\'intrus',
    'models.question.types.true_or_false' => 'Vrai ou Faux',
    'models.question.types.match_with_arrows' => 'Associer avec des flèches',
    'models.question.options' => 'Options',
    'models.question.option' => 'Option',
    'models.question.option.iscorrect' => 'Est correcte?',

    'models.question.words' => 'Mots',
    'models.question.word' => 'Mot',
    'models.question.word.is_intruder' => 'Est un intrus?',
    'models.question.duo' => 'Duo',
    'models.question.duos' => 'Duos',
    'models.question.duo.first' => 'Première phrase',
    'models.question.duo.second' => 'Deuxième phrase',

    // Card

    "models.card.code" => "Code",
    "models.card.code.warning" => "Il est recommandé d'ajouter des cartes à partir de la commande 'Ajouter des cartes' sur la page principale des cartes",
    "models.card.tab.code" => "Code",
    "models.card.tab.pricing" => "Tarification",
    "models.card.tab.subscription" => "Abonnement",
    "models.card.price" => "Prix",
    "models.card.subscription_type" => "Type d'abonnement",
    "models.card.user" => "Utilisateur",
    "models.card.activated" => "Activé",
    "models.card.expired" => "Expiré",
    'models.card.done' => 'Terminé',
    "models.card.expires_at" => "Expire à",
    "models.card.activated_at" => "Activé à",
    'models.card.is_on_discount' => 'Est en promotion',
    'models.card.discount_price' => 'Prix de réduction',
    'models.card.discount_percentage' => 'Pourcentage de réduction %',
    'models.card.subscription.yearly' => 'Abonnement annuel',
    'models.card.display_price' => 'Prix d\'affichage',
    'models.card.number_of_cards' => 'Nombre de cartes',
    'models.card.status' => 'Statut',
    'models.card.status.idle' => 'Prêt',
    'models.card.status.expired' => 'Expiré',
    'models.card.status.active' => 'Actif',
    'models.card.status.done' => 'Terminé',
    'models.card.status.problem' => 'Problème',
    'models.card.create_cards' => 'Créer des cartes',

    # Subscription
    'models.subscription.name' => 'Nom',
    'models.subscription.description' => 'Description',
    'models.subscription.discounts' => 'Promotions',
    "models.subscription.discounts.empty" => "Aucune promotion",
    'models.subscription.price' => 'Prix',
    'models.subscription.ending_date' => 'Date de fin',

    # Discount
    'models.discount.name' => 'Nom',
    'models.discount.description' => 'Description',
    'models.discount.amount' => 'Montant de la réduction',
    'models.discount.percentage' => 'Pourcentage de réduction',
    'models.discount.from' => "De",
    'models.discount.to' => "À",
    'models.discount.subscriptions' => 'Abonnements',
    'models.discount.subscriptions.empty' => 'Aucun abonnement',
    'models.discount.tabs.informations' => "Informations sur la réduction",
    'models.discount.tabs.reduction' => "Réduction",
    'models.discount.tabs.period' => "Période",

    # Subscription Card
    'models.subscriptionCard.code' => 'Code',
    'models.subscriiptionCard.user' => 'Utilisateur',
    'models.subscriptionCard.subscription' => 'Abonnement',
    'models.subscriptionCard.redeemed_at' => "Utilisé à",
    'models.subscriptionCard.redeemed_at.empty' => "Non utilisé",

    'models.subscriptionCard.copy_code' => 'Copier le code',
    'models.subscriptionCard.create_subscriptionCards' => 'Créer des cartes d\'abonnement',
    'models.subscriptionCard.number_of_cards' => 'Nombre de cartes',
    'models.subscriptionCard.attach_user' => "Attacher l'utilisateur",
    'models.subscriptionCard.user.email' => "Email de l'utilisateur",
    'models.subscriptionCard.user.empty' => "Aucun utilisateur attaché",
    'models.subscriptionCard.user.added_successfully' => "Utilisateur attaché avec succès",
    'models.subscriptionCard.user.not_found' => "Utilisateur non trouvé",
    'models.subscriptionCard.user.already_subscribed' => "Utilisateur déjà abonné",
    'models.subscriptionCard.activated_cards' => "Cartes activées",
    'models.subscriptionCard.unactivated_cards' => "Cartes non activées",
    'models.subscriptionCard.code_copied' => "Code copié",

    # Currency

    "currency.local.dzd" => env('MONEY_DEFAULT_LOCALE_fr', 'fr_DZ'),

    # Stats
    "stats.users.new" => 'Nouveaux utilisateurs',
    "stats.users.last30Days" => 'Les 30 derniers jours',
];
