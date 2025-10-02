<?php

namespace App\Filament\Admin;

class AdminNavigation
{

    public const PLATFORM_GROUP = 'custom.nav.section.platform';
    public const SUBSCRIPTION_AND_PAYMENT_GROUP = 'custom.nav.section.subscription_and_payment';
    public const CONTENT_GROUP = 'custom.nav.section.content';
    public const MANAGEMENT_GROUP = 'custom.nav.section.management';
    public const POINTS_GROUP = 'custom.nav.section.points';
    public const APP_GROUP = 'custom.nav.section.app';
    public const REPORTS_GROUP = 'custom.nav.section.reports';

    public const PLATFORM_SETTINGS = [
        'icon' => 'heroicon-o-globe-alt',
        'sort' => 1,
        'group' => self::PLATFORM_GROUP,
    ];

    public const APP_SETTINGS = [
        'icon' => 'heroicon-o-cog-6-tooth',
        'sort' => 2,
        'group' => self::APP_GROUP,
    ];

    public const BANNER_RESOURCE = [
        'icon' => 'heroicon-o-photo',
        'sort' => 3,
        'group' => self::APP_GROUP,
    ];

    public const USERS = [
        'icon' => 'heroicon-o-users',
        'sort' => 4,
        'group' => self::MANAGEMENT_GROUP,
    ];

    public const LEADER_BOARD_RESOURCE = [
        'icon' => 'heroicon-o-numbered-list',
        'sort' => 5,
        'group' => self::MANAGEMENT_GROUP,
    ];

    public const REFERRAL_SOURCE_RESOURCE = [
        'icon' => 'heroicon-o-share',
        'sort' => 6,
        'group' => self::MANAGEMENT_GROUP,
    ];

    public const SUBSCRIPTION_RESOURCE = [
        'icon' => 'heroicon-o-banknotes',
        'sort' => 7,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const DISCOUNT_RESOURCE = [
        'icon' => 'heroicon-o-percent-badge',
        'sort' => 8,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const PROMOTER_RESOURCE = [
        'icon' => 'heroicon-o-megaphone',
        'sort' => 9,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const PAYMENT_RESOURCE = [
        'icon' => 'heroicon-o-banknotes',
        'sort' => 10,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const CHAPTER_LEVEL_RESOURCE = [
        'icon' => 'heroicon-o-rectangle-stack',
        'sort' => 11,
        'group' => self::POINTS_GROUP,
    ];

    public const DIVISION_RESOURCE = [
        'icon' => "heroicon-o-academic-cap",
        'sort' => 12,
        'group' => self::CONTENT_GROUP,
    ];

    public const MATERIAL_RESOURCE = [
        'icon' => "heroicon-o-academic-cap",
        'sort' => 13,
        'group' => self::CONTENT_GROUP,
    ];

    public const UNIT_RESOURCE = [
        'icon' => "heroicon-o-document-duplicate",
        'sort' => 14,
        'group' => self::CONTENT_GROUP,
    ];

    public const CHAPTER_RESOURCE = [
        'icon' => "heroicon-o-rectangle-stack",
        'sort' => 15,
        'group' => self::CONTENT_GROUP,
    ];

    public const QUESTION_REPORT_RESOURCE = [
        'icon' => "heroicon-o-rectangle-stack",
        'sort' => 16,
        'group' => self::REPORTS_GROUP,
    ];

    public const CONTACT_FORM_RESOURCE = [
        'icon' => 'heroicon-o-envelope',
        'sort' => 17,
        'group' => self::REPORTS_GROUP,
    ];
}
