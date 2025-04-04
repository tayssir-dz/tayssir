<?php

return [

    // ------------------------ CUSTOM --------------------------
    'nav.section.platform' => 'Platform',
    'nav.section.content' => 'Content',
    'nav.section.management' => 'Management',
    'nav.section.points' =>  'Points',

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
    "forms.material.create.section.image" => "Material media",

    'models.material.name' => 'Material name',
    'models.material.description' => 'Material description',
    'models.material.code' => 'Material code',
    'models.material.code.placeholder' => 'Code to identify the material',
    'models.material.color' => 'Material color',
    'models.material.secondary_color' => 'Secondary color',
    'models.material.division' => 'Division',
    'models.material.action.details' => 'Material details',
    'models.material.image' => 'Material image list',
    'models.material.image_grid' => 'Material grid image',

    // # Unit
    'models.unit.name' => 'Unit name',
    'models.unit.description' => 'Unit description',
    'models.unit.material' => 'Material',
    'models.unit.action.details' => 'Unit details',
    'models.unit.subscriptions' => 'Subscriptions',
    'models.unit.image' => 'Unit image',
    "forms.unit.create.section.infos" => "Unit information",
    "forms.unit.create.section.image" => "Unit image",

    // # User
    'models.user.name' => 'Name',
    'models.user.avatar' => "Profile picture",
    'models.user.email' => 'Email',
    'models.user.password' => 'Password',
    'models.user.phone' => 'Phone',
    'models.user.phone.empty' => 'No phone number',
    'models.user.roles' => 'Roles',
    'models.user.roles.empty' => 'No roles assigned',
    'models.user.verified' => 'Verified',
    'models.user.email_verified' => 'Email verified',
    'models.user.email_not_verified' => 'Email not verified',
    'models.user.points' => 'Points',
    'models.user.wilaya' => 'Wilaya',
    'models.user.wilayas' => 'Wilayas',
    'models.user.wilaya.empty' => 'No wilaya selected',
    'models.user.wilaya.field' => "name",
    'models.user.commune' => 'Commune',
    'models.user.communes' => 'Communes',
    'models.user.commune.empty' => 'No commune selected',
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
    "forms.chapter.create.section.infos" => "Chapter informations",
    "forms.chapter.create.section.image" => "Chapter image",
    'models.chapter.name' => 'Chapter name',
    'models.chapter.description' => 'Chapter description',
    'models.chapter.unit' => 'Unit',
    'models.chapter.photo' => 'Photo',
    'models.chapter.action.details' => 'Chapter details',
    "models.chapter.subscriptions" => "Subscriptions",

    'models.chapter_levels' => 'Chapter Levels',
    'models.chapter_level' => 'Chapter Level',
    'models.chapter_level.name' => 'Level Name',
    'models.chapter_level.exercice_points' => 'Exercise Points',
    'models.chapter_level.lesson_points' => 'Lesson Points',
    'models.chapter_level.bonus' =>  'Bonus',
    'forms.chapter_level.create.section.infos' => 'Level Information',
    'models.chapter.level' => 'Level',

    // # Question
    'models.question.tabs.infos' => 'Information',
    'models.question.tabs.assets' => 'Assets',
    'models.question.tabs.options' => 'Options',
    'models.question.question' => 'Question',
    'models.question.points' => 'Points',
    'models.question.hint' => 'Hint',
    'models.question.question_image' => 'Question Image',
    'models.question.explaination_asset' => 'Explanation Asset',
    'models.question.explanation_text' => 'Explanation Text',
    'models.question.hint_image' => 'Hint Image',
    'models.question.type' => 'Question Type',
    'models.question.types.multiple_choices' => 'Multiple Choices',
    'models.question.types.fill_in_the_blanks' => 'Fill in the Blanks',
    'models.question.types.pick_the_intruder' => 'Pick the Intruder',
    'models.question.types.true_or_false' => 'True or False',
    'models.question.types.match_with_arrows' => 'Match with Arrows',
    'models.question.options' => 'Options',
    'models.question.option' => 'Option',
    'models.question.option.iscorrect' => 'Is Correct',
    'models.question.words' => 'Words',
    'models.question.word' => 'Word',
    'models.question.word.is_intruder' => 'Is Intruder',
    'models.question.duos' => 'Pairs',
    'models.question.duo.first' => 'First Item',
    'models.question.duo.second' => 'Second Item',
    'models.question.fill_blank.answer' => 'Answer',
    'models.question.fill_blank.answers' => 'Answers',
    'models.question.fill_blank.paragraph' => 'Paragraph',
    'models.question.fill_blank.paragraph_help' => 'Write your paragraph with placeholders like [1], [2], etc. for the blank spaces',
    'models.question.fill_blank.word' => 'Word',
    'models.question.fill_blank.placeholder' => 'Placeholder (e.g., [1])',
    'models.question.fill_blank.words' => 'Fill in the Blanks Words',
    'models.question.fill_blank.add_answer' => 'Add Answer',
    'models.question.fill_blank.suggestions' => 'Suggestions',
    'models.question.fill_blank.suggestions_help' => 'Add possible words that will appear as suggestions',
    'models.question.fill_blank.suggestions_placeholder' => 'Enter a suggestion word',
    'models.question.fill_blank.blanks' => 'Blanks',
    'models.question.fill_blank.add_blank' => 'Add Blank',
    'models.question.fill_blank.correct_word' => 'Correct Word',
    'models.question.fill_blank.position' => 'Position',
    'models.question.true_false.correct_answer' => 'Correct Answer',
    'models.question.true_false.true' => 'True',
    'models.question.true_false.false' => 'False',
    'models.question.add_option' => 'Add Option',
    "models.question.add_word" => "Add Word",
    'models.question.add_duo' => 'Add Pair',

    'models.question.difficulty' => 'Difficulty',
    'models.question.difficulty.easy' => 'Easy',
    'models.question.difficulty.medium' => 'Medium',
    'models.question.difficulty.hard' => 'Hard',

    'models.question.scope' => 'Scope',
    'models.question.scope.exercice' => "Exercise",
    'models.question.scope.lesson' => "Lesson",
    'models.question.is_latex' => 'Is LaTeX',
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
    'models.subscriptionCard.user' => 'User',
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

    // # Active field
    'models.active' => 'Active',
    'models.active.true' => 'Active',
    'models.active.false' => 'Inactive',

    # Currency

    "currency.local.dzd" => env('MONEY_DEFAULT_LOCALE_en', 'EN_DZ'),

    # Stats
    "stats.users.new" => 'New users',
    "stats.users.last30Days" => 'Last 30 days',

    "direction.label" =>  'Text direction',
    "direction.rtl" =>  "From right to left (rtl)",
    "direction.ltr" =>   "From left to right (ltr)",
    "direction.inherit" =>  "Use the parent direction (inherit)",

    'table.image.empty' => "Image not uploaded",
];
