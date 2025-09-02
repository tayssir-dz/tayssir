<?php

return [
    "mode" =>  env("CHARGILY_EPAY_MODE", "test"), // "test" or "live"
    "public" => env("CHARGILY_EPAY_PUBLIC_KEY", "test_pk_*************************"),
    "secret" => env("CHARGILY_EPAY_SECRET_KEY", "test_sk_*************************"),
];
