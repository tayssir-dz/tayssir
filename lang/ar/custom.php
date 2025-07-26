<?php

return [
    // ------------------------ CUSTOM --------------------------
    'nav.section.platform' => 'المنصة',
    'nav.section.content' => 'المحتوى',
    'nav.section.management' => 'الإدارة',
    'nav.section.points' => 'النقاط',


    'models.cards' => 'البطاقات (قديم)',
    'models.card' => 'بطاقة',

    'models.divisions' => 'الشعب',
    'models.division' => 'شعبة',

    'models.materials' => 'المواد',
    'models.material' => 'مادة',

    'models.units' => 'الوحدات',
    'models.unit' => 'وحدة',

    'models.chapters' => 'الفصول',
    'models.chapter' => 'فصل',

    'models.questions' => 'الأسئلة',
    'models.question' => 'سؤال',

    'models.users' => 'المستخدمين',
    'models.user' => 'مستخدم',
    'models.subscriptions' => 'الاشتراكات',
    'models.subscription' => 'اشتراك',

    'models.discounts' => "التخفيضات",
    'models.discount' => "تخفيض",

    'models.subscriptionCards' => "بطاقات الاشتراك",
    'models.subscriptionCard' => "بطاقة الاشتراك",

    'models.promoters' => 'المروجين',
    'models.promoter' => 'مروج',

    'models.promoCodes' => 'رموز الترويج',
    'models.promoCode' => 'رمز ترويج',

    // # Promoter
    'models.promoter.name' => 'الاسم',
    'models.promoter.avatar' => 'الصورة الشخصية',
    'models.promoter.email' => 'البريد الإلكتروني',
    'models.promoter.password' => 'كلمة المرور',
    'models.promoter.phone' => 'رقم الهاتف',
    'models.promoter.phone.empty' => 'لا يوجد رقم هاتف',
    'models.promoter.wilaya' => 'الولاية',
    'models.promoter.wilayas' => 'الولايات',
    'models.promoter.wilaya.empty' => 'لم يتم اختيار الولاية',
    'models.promoter.wilaya.field' => "arabic_name",
    'models.promoter.commune' => 'البلدية',
    'models.promoter.communes' => 'البلديات',
    'models.promoter.commune.empty' => 'لم يتم اختيار البلدية',
    'models.promoter.commune.field' => "arabic_name",
    'models.promoter.personal_info' => 'المعلومات الشخصية',

    // # Promo Code
    'models.promoCode.code' => 'الرمز',
    'models.promoCode.start_date' => 'تاريخ البداية',
    'models.promoCode.end_date' => 'تاريخ النهاية',
    'models.promoCode.student_discount' => 'خصم الطالب (%)',
    'models.promoCode.promoter_margin' => 'هامش المروج (%)',
    'models.promoCode.is_active' => 'نشط',
    'models.promoCode.validity_period' => 'فترة الصلاحية',
    'models.promoCode.discount_info' => 'معلومات الخصم',
    'forms.promoCode.create.section.infos' => 'معلومات رمز الترويج',
    'forms.promoCode.create.section.dates' => 'فترة الصلاحية',
    'forms.promoCode.create.section.discounts' => 'معلومات الخصم',
    'forms.promoCode.helper.code' => 'أدخل رمز ترويج فريد للتعريف',
    'forms.promoCode.helper.student_discount' => 'نسبة خصم للطلاب (0-100%)',
    'forms.promoCode.helper.promoter_margin' => 'نسبة هامش للمروج (0-100%)',

    'models.banners' => 'البانرات',
    'models.banner' => 'بانر',

    // # Banner
    'forms.banner.create.section.infos' => 'معلومات البانر',
    'forms.banner.create.section.image' => 'صورة البانر',
    'forms.banner.create.section.styling' => 'تصميم البانر',
    'models.banner.title' => 'العنوان',
    'models.banner.description' => 'الوصف',
    'models.banner.action_url' => 'رابط الإجراء',
    'models.banner.action_label' => 'تسمية الإجراء',
    'models.banner.gradient_start' => 'لون بداية التدرج',
    'models.banner.gradient_end' => 'لون نهاية التدرج',
    'models.banner.image' => 'صورة البانر',
    'models.banner.is_active' => 'نشط',

    'models.summaries' => 'الملخصات',
    'models.summary' => 'ملخص',

    // # Summary
    'forms.summary.create.section.infos' => 'معلومات الملخص',
    'forms.summary.create.section.file' => 'ملف الملخص PDF',
    'models.summary.title' => 'العنوان',
    'models.summary.description' => 'الوصف',
    'models.summary.material' => 'المادة',
    'models.summary.pdf' => 'ملف PDF',
    'models.summary.is_active' => 'نشط',

    'models.bacs' => 'بكالوريات',
    'models.bac' => 'بكالوريا',

    // # Bac
    'forms.bac.create.section.infos' => 'معلومات البكالوريا',
    'forms.bac.create.section.file' => 'ملف البكالوريا PDF',
    'models.bac.title' => 'العنوان',
    'models.bac.description' => 'الوصف',
    'models.bac.material' => 'المادة',
    'models.bac.pdf' => 'ملف PDF',
    'models.bac.is_active' => 'نشط',

    'models.flashcard_groups' => 'مجموعات البطاقات التعليمية',
    'models.flashcard_group' => 'مجموعة بطاقات تعليمية',

    // # Flashcard Group
    'forms.flashcard_group.create.section.infos' => 'معلومات مجموعة البطاقات التعليمية',
    'models.flashcard_group.title' => 'العنوان',
    'models.flashcard_group.description' => 'الوصف',
    'models.flashcard_group.action.details' => 'تفاصيل مجموعة البطاقات التعليمية',

    'models.flashcards' => 'البطاقات التعليمية',
    'models.flashcard' => 'بطاقة تعليمية',

    // # Flashcard
    'forms.flashcard.create.section.infos' => 'معلومات البطاقة التعليمية',
    'models.flashcard.title' => 'العنوان',
    'models.flashcard.description' => 'الوصف',


    // # Division
    "forms.division.create.section.infos" => "معلومات الشعبة",
    "forms.division.create.section.image" => "صورة الشعبة",

    'models.division.name' => 'اسم الشعبة',
    'models.division.description' => 'وصف الشعبة',

    // # Material
    "forms.material.create.section.infos" => "معلومات المادة",
    "forms.material.create.section.image" => "وسائط المادة",

    'models.material.name' => 'اسم المادة',
    'models.material.description' => 'وصف المادة',
    'models.material.code' => 'رمز المادة',
    'models.material.code.placeholder' => 'رمز لتحديد المادة',
    'models.material.color' => 'لون المادة',
    'models.material.secondary_color' => 'اللون الثانوي',
    'models.material.division' => 'القسم',
    'models.material.action.details' => 'تفاصيل المادة',
    'models.material.image' => 'قائمة صور المادة',
    'models.material.image_grid' => 'صورة شبكة المادة',
    // # Unit
    'models.unit.name' => 'اسم الوحدة',
    'models.unit.description' => 'وصف الوحدة',
    'models.unit.material' => 'المادة',
    'models.unit.action.details' => 'تفاصيل الوحدة',
    'models.unit.subscriptions' => 'الاشتراكات',
    'models.unit.image' => 'صورة الوحدة',
    "forms.unit.create.section.infos" => "معلومات الوحدة",
    "forms.unit.create.section.image" => "صورة الوحدة",

    // # User
    'models.user.name' => 'الاسم',
    'models.user.avatar' => 'الصورة الشخصية',
    'models.user.email' => 'البريد الإلكتروني',
    'models.user.password' => 'كلمة المرور',
    'models.user.phone' => 'رقم الهاتف',
    'models.user.phone.empty' => 'لا يوجد رقم هاتف',
    'models.user.roles' => 'الأدوار',
    'models.user.roles.empty' => 'لا توجد أدوار',
    'models.user.verified' => 'مفعل',
    'models.user.email_verified' => 'تم التحقق من البريد الإلكتروني',
    'models.user.email_not_verified' => 'لم يتم التحقق من البريد الإلكتروني',
    'models.user.points' => 'النقاط',
    'models.user.wilaya' => 'الولاية',
    'models.user.wilayas' => 'الولايات',
    'models.user.wilaya.empty' => 'لم يتم اختيار الولاية',
    'models.user.commune' => 'البلدية',
    'models.user.commune.empty' => 'لم يتم اختيار البلدية',
    'models.user.wilaya.field' => "arabic_name",
    'models.user.commune.field' => "arabic_name",

    'models.user.tabs.all' => 'الكل',
    'models.user.tabs.students' => 'الطلاب',
    'models.user.tabs.with_roles' => 'ذوي الأدوار',

    'models.user.perfonal_info' => 'المعلومات الشخصية',
    'models.user.subscribtion' => 'حالة الاشتراك',
    'models.user.subscribed' => 'مشترك',
    'models.user.not_subscribed' => 'غير مشترك',

    'models.user.roles_and_subscription' => 'الأدوار والاشتراك',


    // # Chapter
    "forms.chapter.create.section.infos" => "معلومات الفصل",
    "forms.chapter.create.section.image" => "صورة الفصل",
    'models.chapter.name' => 'اسم الفصل',
    'models.chapter.description' => 'الوصف',
    'models.chapter.unit' => 'الوحدة',
    'models.chapter.photo' => 'صورة الفصل',
    'models.chapter.action.details' => 'تفاصيل الفصل',
    "models.chapter.subscriptions" => 'الاشتراكات',

    // # Chapter
    'models.question.tabs.infos' => 'المعلومات',
    'models.question.tabs.assets' => 'الوسائط',
    'models.question.tabs.options' => 'الخيارات',
    'models.question.question' => 'السؤال',
    'models.question.points' => 'النقاط',
    'models.question.hint' => 'التلميح',
    'models.question.question_image' => 'صورة السؤال',
    'models.question.explaination_asset' => 'وسائط الشرح',
    'models.question.explanation_text' => 'نص التعليل',
    'models.question.hint_image' => 'صورة التلميح',
    'models.question.type' => 'نوع السؤال',
    'models.question.types.multiple_choices' => 'اختيارات متعددة',
    'models.question.types.fill_in_the_blanks' => 'ملء الفراغات',
    'models.question.types.pick_the_intruder' => 'اختر الدخيل',
    'models.question.types.true_or_false' => 'صح أم خطأ',
    'models.question.types.match_with_arrows' => 'التوصيل بالأسهم',
    'models.question.options' => 'الخيارات',
    'models.question.option' => 'خيار',
    'models.question.option.iscorrect' => 'إجابة صحيحة',
    'models.question.words' => 'الكلمات',
    'models.question.word' => 'كلمة',
    'models.question.word.is_intruder' => 'دخيل',
    'models.question.duos' => 'الأزواج',
    'models.question.duo.first' => 'العنصر الأول',
    'models.question.duo.second' => 'العنصر الثاني',
    'models.question.add_option' => 'إضافة خيار',
    "models.question.add_word" => "إضافة كلمة",
    'models.question.add_duo' => 'إضافة زوج',
    'models.question.difficulty' => 'الصعوبة',
    'models.question.difficulty.easy' => 'سهل',
    'models.question.difficulty.medium' => 'متوسط',
    'models.question.difficulty.hard' => 'صعب',
    // Question translations
    'models.question.fill_blank.answer' => 'الإجابة',
    'models.question.fill_blank.answers' => 'الإجابات',
    'models.question.true_false.correct_answer' => 'الإجابة الصحيحة',
    'models.question.true_false.true' => 'صحيح',
    'models.question.true_false.false' => 'خطأ',

    'models.question.fill_blank.paragraph' => 'الفقرة',
    'models.question.fill_blank.paragraph_help' => 'اكتب فقرتك مع علامات الفراغات مثل [1]، [2]، إلخ',
    'models.question.fill_blank.word' => 'الكلمة',
    'models.question.fill_blank.placeholder' => 'علامة الفراغ (مثال: [1])',
    'models.question.fill_blank.words' => 'كلمات ملء الفراغات',
    'models.question.fill_blank.add_answer' => 'إضافة إجابة',
    'models.question.fill_blank.suggestions' => 'الاقتراحات',
    'models.question.fill_blank.suggestions_help' => 'أضف كلمات ممكنة ستظهر كاقتراحات',
    'models.question.fill_blank.suggestions_placeholder' => 'أدخل كلمة اقتراح',
    'models.question.fill_blank.blanks' => 'الفراغات',
    'models.question.fill_blank.add_blank' => 'إضافة فراغ',
    'models.question.fill_blank.correct_word' => 'الكلمة الصحيحة',
    'models.question.fill_blank.position' => 'الموضع',

    'models.question.scope' => 'نطاق السؤال',
    'models.question.scope.exercice' => "تمرين",
    'models.question.scope.lesson' => "درس",
    'models.question.is_latex' => 'نص LaTeX',

    // Card

    "models.card.code" => "الكود",
    "models.card.code.warning" => "يستحسن اضافة البطاقات من الامر 'اضافة بطاقات' في الصفحة الاساسية للبطاقات",
    "models.card.tab.code" => "الكود",
    "models.card.tab.pricing" => "السعر",
    "models.card.tab.subscription" => "الاشتراك",
    "models.card." => "الكود",
    "models.card.price" => "السعر",
    "models.card.subscription_type" => "نوع الاشتراك",
    "models.card.user" => "المستخدم",
    "models.card.activated" => "مفعلة",
    "models.card.expired" => "منتهي",
    'models.card.done' => 'تم',
    "models.card.expires_at" => "ينتهي في",
    "models.card.activated_at" => "تم التفعيل في",
    'models.card.is_on_discount' => 'على خصم',
    'models.card.discount_price' => 'سعر الخصم',
    'models.card.discount_percentage' => 'نسبة الخصم %',
    'models.card.subscription.yearly' => 'الاشتراك السنوي',
    'models.card.display_price' => 'سعر العرض',
    'models.card.number_of_cards' => 'عدد البطاقات',
    'models.card.status' => 'الحالة',
    'models.card.status.idle' => 'جاهز',
    'models.card.status.expired' => 'منتهي',
    'models.card.status.active' => 'مفعل',
    'models.card.status.done' => 'تم',
    'models.card.status.problem' => 'معطل',
    'models.card.create_cards' => 'اضافة بطاقات',

    # Subscription
    'models.subscription.name' => 'الاسم',
    'models.subscription.description' => 'الوصف',
    'models.subscription.discounts' => "التخفيضات",
    "models.subscription.discounts.empty" => "لا يوجد تخفيضات",
    'models.subscription.price' => "السعر",
    'models.subscription.ending_date' => "تاريخ الانتهاء",

    # Discount
    'models.discount.name' => 'الاسم',
    'models.discount.description' => 'الوصف',
    'models.discount.amount' => 'مبلغ التخفيض',
    'models.discount.percentage' => 'نسبة التخفيض',
    'models.discount.from' => "من",
    'models.discount.to' => "إلى",
    'models.discount.subscriptions' => "الاشتراكات",
    'models.discount.subscriptions.empty' => "لا يوجد اشتراكات",
    'models.discount.tabs.informations' => "معلومات التخفيض",
    'models.discount.tabs.reduction' => "التخفيض",
    'models.discount.tabs.period' => "الفترة",


    # Subscription Card
    'models.subscriptionCard.code' => 'الكود',
    'models.subscriiptionCard.user' => 'المستخدم',
    'models.subscriptionCard.subscription' => 'الاشتراك',
    'models.subscriptionCard.redeemed_at' => "تم استخدامها في",
    'models.subscriptionCard.redeemed_at.empty' => "لم يتم استخدامها بعد",

    'models.subscriptionCard.copy_code' => 'نسخ الكود',
    'models.subscriptionCard.create_subscriptionCards' => 'اضافة بطاقات الاشتراك',
    'models.subscriptionCard.number_of_cards' => 'عدد البطاقات المراد إنشاؤها',
    'models.subscriptionCard.attach_user' => "ربط المستخدم",
    'models.subscriptionCard.user.email' => "البريد الإلكتروني",
    'models.subscriptionCard.user.empty' => "لا يوجد مستخدم",
    'models.subscriptionCard.user.added_successfully' => "تم ربط المستخدم بنجاح",
    'models.subscriptionCard.user.not_found' => "المستخدم غير موجود",
    'models.subscriptionCard.user.already_subscribed' => "المستخدم مشترك بالفعل",
    'models.subscriptionCard.activated_cards' => "البطاقات المفعلة",
    'models.subscriptionCard.unactivated_cards' => "البطاقات الغير مفعلة",
    'models.subscriptionCard.code_copied' => "تم نسخ الكود",

    # Currency
    "currency.local.dzd" => env('MONEY_DEFAULT_LOCALE_AR', 'ar_DZ'),

    # Stats
    "stats.users.new" => 'المستخدمين الجدد',
    "stats.users.last30Days" => 'خلال الـ 30 يوما',

    "direction.label" => "إتجاه النص",
    "direction.rtl" => '(rtl) من اليمين لليسار',
    "direction.ltr" =>   '(ltr) من اليسار لليمين',
    "direction.inherit" =>   '(inherit) من العنصر الأب',

    'models.chapter_levels' => 'مستويات الفصول',
    'models.chapter_level' => 'مستوى الفصل',
    'models.chapter_level.name' => 'اسم المستوى',
    'models.chapter_level.exercice_points' => 'نقاط التمارين',
    'models.chapter_level.lesson_points' => 'نقاط الدروس',
    'models.chapter_level.bonus' =>  'نقاط المكافأة',
    'forms.chapter_level.create.section.infos' => 'معلومات المستوى',
    'models.chapter.level' => 'المستوى',

    // # LeaderBoard
    'models.leaderboard' => 'لوحة المتصدرين',
    'models.leaderboard.user' => 'المستخدم',
    'models.leaderboard.points' => 'النقاط',
    'models.leaderboard.max' => 'النقاط القصوى',
    'models.leaderboard.progress' => 'التقدم',

    // # Active field
    'models.active' => 'نشط',
    'models.active.true' => 'نشط',
    'models.active.false' => 'غير نشط',

    'table.image.empty' => "لم يتم رفع صورة",

    // # Json Column
    'models.json_column.viewer_tab' => 'العارض',
    'models.json_column.editor_tab' => 'المحرر',

    // # JSON Upload
    'models.question.json_upload.button' => 'رفع ملف JSON',
    'models.question.json_upload.modal_heading' => 'رفع الأسئلة من ملف JSON',
    'models.question.json_upload.modal_description' => 'الصق مصفوفة JSON من الأسئلة. يجب أن يحتوي كل سؤال على الحقول المطلوبة.',
    'models.question.json_upload.submit_button' => 'رفع والتحقق',
    'models.question.json_upload.json_data_label' => 'بيانات JSON',
    'models.question.json_upload.helper_text' => 'الصق مصفوفة JSON الخاصة بك هنا. استخدم المحرر لتعديل بيانات JSON بسهولة.',
    'models.question.json_upload.invalid_json' => 'JSON غير صالح',
    'models.question.json_upload.invalid_json_message' => 'يرجى تقديم بيانات JSON صالحة: :error',
    'models.question.json_upload.invalid_format' => 'تنسيق غير صالح',
    'models.question.json_upload.invalid_format_message' => 'يجب أن تكون بيانات JSON كائنًا أو مصفوفة من كائنات الأسئلة.',
    'models.question.json_upload.success_title' => 'تم رفع الأسئلة',
    'models.question.json_upload.success_message' => 'تم رفع :count سؤال (أسئلة) بنجاح.',
    'models.question.json_upload.errors_title' => 'فشل بعض الأسئلة',
    'models.question.json_upload.upload_failed' => 'فشل الرفع',
    'models.question.json_upload.error_message' => 'حدث خطأ: :error',

    // # Flashcard JSON Upload
    'models.flashcard.json_upload.button' => 'رفع JSON',
    'models.flashcard.json_upload.modal_heading' => 'رفع البطاقات التعليمية من JSON',
    'models.flashcard.json_upload.modal_description' => 'الصق مصفوفة JSON للبطاقات التعليمية. يجب أن تحتوي كل بطاقة تعليمية على الحقول المطلوبة.',
    'models.flashcard.json_upload.submit_button' => 'رفع والتحقق',
    'models.flashcard.json_upload.json_data_label' => 'بيانات JSON',
    'models.flashcard.json_upload.helper_text' => 'الصق مصفوفة JSON للبطاقات التعليمية هنا. استخدم المحرر لتعديل بيانات JSON بسهولة.',
    'models.flashcard.json_upload.invalid_json' => 'JSON غير صالح',
    'models.flashcard.json_upload.invalid_json_message' => 'يرجى تقديم بيانات JSON صالحة: :error',
    'models.flashcard.json_upload.invalid_format' => 'تنسيق غير صالح',
    'models.flashcard.json_upload.invalid_format_message' => 'يجب أن تكون بيانات JSON عبارة عن كائن أو مصفوفة من كائنات البطاقات التعليمية.',
    'models.flashcard.json_upload.success_title' => 'تم رفع البطاقات التعليمية',
    'models.flashcard.json_upload.success_message' => 'تم رفع :count بطاقة تعليمية بنجاح.',
    'models.flashcard.json_upload.errors_title' => 'فشل بعض البطاقات التعليمية',
    'models.flashcard.json_upload.upload_failed' => 'فشل الرفع',
    'models.flashcard.json_upload.error_message' => 'حدث خطأ: :error',

    // # JSON Edit
    'models.question.json_edit.button' => 'تحرير JSON',
    'models.question.json_edit.modal_heading' => 'تحرير سؤال بصيغة JSON',
    'models.question.json_edit.modal_description' => 'قم بتعديل بيانات السؤال بتنسيق JSON.',
    'models.question.json_edit.submit_button' => 'تحديث السؤال',
    'models.question.json_edit.json_data_label' => 'بيانات السؤال بصيغة JSON',
    'models.question.json_edit.helper_text' => 'قم بتعديل بيانات JSON لهذا السؤال. تأكد من الحفاظ على البنية المطلوبة.',
    'models.question.json_edit.success_title' => 'تم تحديث السؤال',
    'models.question.json_edit.success_message' => 'تم تحديث السؤال بنجاح.',
    'models.question.json_edit.invalid_json' => 'JSON غير صالح',
    'models.question.json_edit.invalid_json_message' => 'يرجى تقديم بيانات JSON صالحة: :error',
    'models.question.json_edit.invalid_format' => 'تنسيق غير صالح',
    'models.question.json_edit.invalid_format_message' => 'يجب أن تكون بيانات JSON عبارة عن كائن سؤال صالح.',
    'models.question.json_edit.update_failed' => 'فشل التحديث',
    'models.question.json_edit.error_message' => 'حدث خطأ أثناء التحديث: :error',

    // # App Settings
    'settings.app.title' => 'إعدادات التطبيق',
    'settings.app.section.information' => 'معلومات التطبيق',
    'settings.app.version' => 'إصدار التطبيق',
    'settings.app.resumes' => 'السير الذاتية نشطة',
    'settings.app.bac_solutions' => 'حلول البكالوريا نشطة',
    'settings.app.cards_tools' => 'أدوات البطاقات نشطة',
];
