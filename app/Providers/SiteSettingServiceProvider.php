<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SiteSettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = Setting::pluck('value', 'key');

        if ($settings->isEmpty()) {
            return;
        }

        $map = [
            'app_short_name' => 'site.short_name',
            'app_subtitle' => 'site.subtitle',
            'app_description' => 'site.description',
            'app_motto' => 'site.motto',
            'app_address' => 'site.contact.address',
            'app_phone' => 'site.contact.phone',
            'app_email' => 'site.contact.email',
            'app_email_domain' => 'site.admin.default_email_domain',
            'app_social_facebook' => 'site.social.facebook',
            'app_social_instagram' => 'site.social.instagram',
            'app_social_youtube' => 'site.social.youtube',
            'app_social_tiktok' => 'site.social.tiktok',
        ];

        foreach ($map as $settingKey => $configKey) {
            if ($settings->has($settingKey)) {
                config([$configKey => $settings->get($settingKey)]);
            }
        }
    }
}
