<?php

return [
    // How often each rate row must be re-verified against its official source.
    'rate_review_window_days' => env('BETTEROFF_RATE_REVIEW_WINDOW_DAYS', 90),
];
