<?php

namespace App\Filament\Admin;

class AdminNavigation
{

    public const string PLATFORM_GROUP = 'custom.nav.section.platform';
    public const string SUBSCRIPTION_AND_PAYMENT_GROUP = 'custom.nav.section.subscription_and_payment';
    public const string CONTENT_GROUP = 'custom.nav.section.content';
    public const string MANAGEMENT_GROUP = 'custom.nav.section.management';
    public const string POINTS_GROUP = 'custom.nav.section.points';
    public const string APP_GROUP = 'custom.nav.section.app';

    public const array PLATFORM_SETTINGS = [
        'icon' => 'heroicon-o-globe-alt',
        'sort' => 1,
        'group' => self::PLATFORM_GROUP,
    ];

    public const array APP_SETTINGS = [
        'icon' => 'heroicon-o-cog-6-tooth',
        'sort' => 2,
        'group' => self::APP_GROUP,
    ];

    public const array BANNER_RESOURCE = [
        'icon' => 'heroicon-o-photo',
        'sort' => 3,
        'group' => self::APP_GROUP,
    ];

    public const array USERS = [
        'icon' => 'heroicon-o-users',
        'sort' => 4,
        'group' => self::MANAGEMENT_GROUP,
    ];

    public const array LEADER_BOARD_RESOURCE = [
        'icon' => 'heroicon-o-numbered-list',
        'sort' => 5,
        'group' => self::MANAGEMENT_GROUP,
    ];

    public const array REFERRAL_SOURCE_RESOURCE = [
        'icon' => 'heroicon-o-share',
        'sort' => 6,
        'group' => self::MANAGEMENT_GROUP,
    ];

    public const array SUBSCRIPTION_RESOURCE = [
        'icon' => 'heroicon-o-banknotes',
        'sort' => 7,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const array DISCOUNT_RESOURCE = [
        'icon' => 'heroicon-o-percent-badge',
        'sort' => 8,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const array PROMOTER_RESOURCE = [
        'icon' => 'heroicon-o-megaphone',
        'sort' => 9,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const array PAYMENT_RESOURCE = [
        'icon' => 'heroicon-o-banknotes',
        'sort' => 10,
        'group' => self::SUBSCRIPTION_AND_PAYMENT_GROUP,
    ];

    public const array CHAPTER_LEVEL_RESOURCE = [
        'icon' => 'heroicon-o-rectangle-stack',
        'sort' => 11,
        'group' => self::POINTS_GROUP,
    ];

    public const array DIVISION_RESOURCE = [
        'icon' => "heroicon-o-academic-cap",
        'sort' => 12,
        'group' => self::CONTENT_GROUP,
    ];

    public const array MATERIAL_RESOURCE = [
        'icon' => "heroicon-o-academic-cap",
        'sort' => 13,
        'group' => self::CONTENT_GROUP,
    ];

    public const array UNIT_RESOURCE = [
        'icon' => "heroicon-o-document-duplicate",
        'sort' => 14,
        'group' => self::CONTENT_GROUP,
    ];

    public const array CHAPTER_RESOURCE = [
        'icon' => "heroicon-o-rectangle-stack",
        'sort' => 15,
        'group' => self::CONTENT_GROUP,
    ];
}
