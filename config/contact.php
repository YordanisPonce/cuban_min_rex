<?php

use App\Models\SeoText;

$seo = rescue(fn () => SeoText::firstOrCreate([]), null, false);

return [
    'email' => $seo?->contact_email ?? env('CONTACT_EMAIL'),
    'phone' => $seo?->contact_phone ?? env('CONTACT_PHONE'),
    'instagram' => $seo?->contact_instagram ?? env('CONTACT_INSTAGRAM'),
    'youtube' => $seo?->contact_youtube ?? env('CONTACT_YOUTUBE'),
    'facebook' => $seo?->contact_facebook ?? env('CONTACT_FACEBOOK'),
];