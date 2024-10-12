<?php

return [

    // ------------------------ CUSTOM --------------------------
    'nav.section.platform' => 'Platform',
    'nav.section.content' => 'Content',
    'nav.section.management' => 'Management',

    'models.cards' => 'Cards (old)',
    'models.card' => 'Card',

    'models.divisions' => 'Divisions',
    'models.division' => 'Division',

    'models.materials' => 'Materials',
    'models.material' => 'Material',

    'models.units' => 'Units',
    'models.unit' => 'Unit',

    'models.chapters' => 'Chapters',
    'models.chapter' => 'Chapter',

    'models.questions' => 'Questions',
    'models.question' => 'Question',

    'models.users' => 'Users',
    'models.user' => 'User',

    'models.subscriptions' => 'Subscriptions',
    'models.subscription' => 'Subscription',

    'models.discounts' => "Discounts",
    'models.discount' => "Discount",

    'models.subscriptionCards' => "Subscription cards",
    'models.subscriptionCard' => "Subscription card",

    // # Division
    "forms.division.create.section.infos" => "Division informations",
    "forms.division.create.section.image" => "Division image",

    'models.division.name' => 'Division name',
    'models.division.description' => 'Division description',

    // # Material
    "forms.material.create.section.infos" => "Material informations",
    "forms.material.create.section.image" => "Material image",

    'models.material.name' => 'Material name',
    'models.material.description' => 'Material description',
    'models.material.code' => 'Material code',
    'models.material.code.placeholder' => 'Code to identify the material',
    'models.material.color' => 'Material color',
    'models.material.division' => 'Division',
    'models.material.action.details' => 'Material details',

    // # Unit
    'models.unit.name' => 'Unit name',
    'models.unit.description' => 'Unit description',
    'models.unit.material' => 'Material',
    'models.unit.action.details' => 'Unit details',

    // # User
    'models.user.name' => 'Name',
    'models.user.avatar' => 'Avatar',
    'models.user.email' => 'Email',
    'models.user.password' => 'Password',
    'models.user.phone' => 'Phone',
    'models.user.roles' => 'Roles',
    'models.user.email_verified' => 'Email verified',
    'models.user.email_not_verified' => 'Email not verified',
    'models.user.points' => 'Points',
    'models.user.wilaya' => 'Wilaya',
    'models.user.wilayas' => 'Wilayas',
    'models.user.commune' => 'Commune',
    'models.user.communes' => 'Communes',
    'models.user.wilaya.field' => "name",
    'models.user.commune.field' => "name",

    'models.user.tabs.all' => 'All',
    'models.user.tabs.students' => 'Students',
    'models.user.tabs.with_roles' => 'With roles',

    'models.user.perfonal_info' => 'Personal informations',
    'models.user.subscribtion' => 'Subscription',
    'models.user.subscribed' => 'Subscribed',
    'models.user.not_subscribed' => 'Not subscribed',

    'models.user.roles_and_subscription' => 'Roles and subscription',

    // # Chapter
    'models.chapter.name' => 'Chapter name',
    'models.chapter.description' => 'Chapter description',
    'models.chapter.unit' => 'Unit',
    'models.chapter.action.details' => 'Chapter details',

    // # Chapter
    'models.question.tabs.infos' => 'Informations',
    'models.question.tabs.assets' => 'Assets',
    'models.question.tabs.options' => 'Options',
    'models.question.question' => 'Question',
    'models.question.points' => 'Points',
    'models.question.hint' => 'Hint',
    'models.question.question_image' => "Image of the question",
    'models.question.explaination_asset' => 'Explaination asset',
    'models.question.hint_image' => 'Hint image',
    'models.question.type' => 'Type',
    'models.question.types.multiple_choices' => 'Single choice / Multiple choices',
    'models.question.types.fill_in_the_blanks' => 'Fill in the blanks',
    'models.question.types.pick_the_intruder' => 'Pick the intruder',
    'models.question.types.true_or_false' => 'True or False',
    'models.question.types.match_with_arrows' => 'Match with arrows',
    'models.question.options' => 'Options',
    'models.question.option' => 'Option',
    'models.question.option.iscorrect' => 'Is correct',

    'models.question.words' => 'Words',
    'models.question.word' => 'Word',
    'models.question.word.is_intruder' => 'Is intruder?',
    'models.question.duo' => 'Duos',
    'models.question.duos' => 'Duos',
    'models.question.duo.first' => 'First sentence',
    'models.question.duo.second' => 'Second sentence',

    // Card

    "models.card.code" => "Code",
    "models.card.code.warning" => "It is recommended to add cards from the 'Add Cards' command",
    "models.card.tab.code" => "Code",
    "models.card.tab.pricing" => "Pricing",
    "models.card.tab.subscription" => "Subscription",
    "models.card.price" => "Price",
    "models.card.subscription_type" => "Subscription type",
    "models.card.user" => "User",
    "models.card.activated" => "Activated",
    "models.card.expired" => "Expired",
    'models.card.done' => 'Done',
    "models.card.expires_at" => "Expires at",
    "models.card.activated_at" => "Activated at",
    'models.card.is_on_discount' => 'Is on discount',
    'models.card.discount_price' => 'Discount price',
    'models.card.discount_percentage' => 'Discount percentage %',
    'models.card.subscription.yearly' => 'Yearly subscription',
    'models.card.display_price' => 'Display price',
    'models.card.number_of_cards' => 'Number of cards',
    'models.card.status' => 'Status',
    'models.card.status.idle' => 'Ready',
    'models.card.status.expired' => 'Expired',
    'models.card.status.active' => 'Active',
    'models.card.status.done' => 'Done',
    'models.card.status.problem' => 'Problem',
    'models.card.create_cards' => 'Create cards',

    # Subscription
    'models.subscription.name' => 'Name',
    'models.subscription.description' => 'Description',
    'models.subscription.discounts' => 'Discounts',
    "models.subscription.discounts.empty" => "No discounts",
    'models.subscription.price' => 'Price',
    'models.subscription.ending_date' => 'Ending date',

    # Discount
    'models.discount.name' => 'Name',
    'models.discount.description' => 'Description',
    'models.discount.amount' => 'Discount amount',
    'models.discount.percentage' => 'Discount percentage',
    'models.discount.from' => "From",
    'models.discount.to' => "To",
    'models.discount.subscriptions' => 'Subscriptions',
    'models.discount.subscriptions.empty' => 'No subscriptions',
    'models.discount.tabs.informations' => "Discount informations",
    'models.discount.tabs.reduction' => "Reduction",
    'models.discount.tabs.period' => "Period",

    # Subscription Card
    'models.subscriptionCard.code' => 'Code',
    'models.subscriiptionCard.user' => 'User',
    'models.subscriptionCard.subscription' => 'Subscription',
    'models.subscriptionCard.redeemed_at' => "Redeemed at",
    'models.subscriptionCard.redeemed_at.empty' => "Not redeemed yet",

    'models.subscriptionCard.copy_code' => 'Copy code',
    'models.subscriptionCard.create_subscriptionCards' => 'Create subscription cards',
    'models.subscriptionCard.number_of_cards' => 'Number of cards',
    'models.subscriptionCard.attach_user' => "Attach user",
    'models.subscriptionCard.user.email' => "User email",
    'models.subscriptionCard.user.empty' => "No user attached",
    'models.subscriptionCard.user.added_successfully' => "User attached successfully",
    'models.subscriptionCard.user.not_found' => "User not found",
    'models.subscriptionCard.user.already_subscribed' => "User already subscribed",
    'models.subscriptionCard.activated_cards' => "Activated cards",
    'models.subscriptionCard.unactivated_cards' => "Unactivated cards",
    'models.subscriptionCard.code_copied' => "Code copied",

    # Currency

    "currency.local.dzd" => env('MONEY_DEFAULT_LOCALE_en', 'EN_DZ'),

    # Stats
    "stats.users.new" => 'New users',
    "stats.users.last30Days" => 'Last 30 days',
];
