<?php

return [
    /* API Title */
    "title" => env("SWAGGER_TITLE", "Api Documentation"),

    /* API Description */
    "description" => env("SWAGGER_DESCRIPTION", "Laravel autogenerate swagger"),

    /* API Email */
    "email" => env("SWAGGER_EMAIL", "m_keziz@estin.dz"),

    /* API Version */
    "version" => env("SWAGGER_VERSION", "1.0.0"),

    /* Enable Response Schema */
    "enable_response_schema" => true,
    "suggestions_select_input" => false,
    "load_from_json" => false,

    /* Authentication Middlewares */
    "auth_middlewares" => [
        "auth",
        "auth:api",
        "auth:sanctum",
    ],
    /* API URL */
    "url" => env("SWAGGER_URL", "swagger/documentation"),

    /* Issues URL */
    "issues_url" => env("SWAGGER_ISSUE_URL", "swagger/issues"),

    /* Enable Swagger */
    "enable" => env('SWAGGER_ENABLED', true),

    /* Show Prefix */
    "show_prefix" => [
        // '/api/'
    ],

    /* API Versions */
    "versions" => [
        "all",
        // "v1"
    ],
    "default" => "all",

    /* Servers */
    "servers" => [
        [
            "url" => env("APP_URL"),
            "description" => "localhost"
        ]
    ],

    /* Security Schemes */
    "security_schemes" => [
        // "authorization" => [
        //     "type" => "apiKey",
        //     "name" => "authorization",
        //     "in" => "header"
        // ],
        "bearer" => [
            "type" => "http",
            "scheme" => "bearer",
            "bearerFormat" => "JWT",
            "securityScheme" => "bearerAuth",
            "description" => "Enter the token with without the Bearer prefix"
        ]
    ],

    /* Status */
    "status" => [
        "GET" => [
            "200" => [
                "description" => "Successful Operation",
            ],
            "404" => [
                "description" => "Not Found"
            ],
            "401" => [
                "description" => "Unauthorized"
            ],
        ],
        "POST" => [
            "200" => [
                "description" => "Successful Operation",
            ],
            "422" => [
                "description" => "Validation Issues"
            ],
            "401" => [
                "description" => "Unauthorized"
            ],
        ],
        "PUT" => [
            "200" => [
                "description" => "Successful Operation",
            ],
            "404" => [
                "description" => "Not Found"
            ],
            "422" => [
                "description" => "Validation exception"
            ],
            "401" => [
                "description" => "Unauthorized"
            ],
        ],
        "PATCH" => [
            "200" => [
                "description" => "Successful Operation",
            ],
            "404" => [
                "description" => "Not Found"
            ],
            "422" => [
                "description" => "Validation exception"
            ]
        ],
        "DELETE" => [
            "200" => [
                "description" => "successful Operation",
            ],
            "404" => [
                "description" => "page Not Found"
            ],
            "401" => [
                "description" => "Unauthorized"
            ],
            "422" => [
                "description" => "Validation exception"
            ]
        ],
    ],

];
