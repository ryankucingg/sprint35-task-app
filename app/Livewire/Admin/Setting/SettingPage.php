<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use MrCatz\DataTable\MrCatzComponent;
use MrCatz\DataTable\MrCatzFormField;
use Illuminate\Support\Facades\Log;

class SettingPage extends MrCatzComponent
{
    public $app_name;
    public $app_short_name;
    public $app_subtitle;
    public $app_description;
    public $app_motto;
    public $app_address;
    public $app_phone;
    public $app_email;
    public $app_email_domain;
    public $app_social_facebook;
    public $app_social_instagram;
    public $app_social_youtube;
    public $app_social_tiktok;

    public function setForm(): array
    {
        return [
            MrCatzFormField::section('Informasi Aplikasi')->asCard(),
            MrCatzFormField::text('app_name', label: 'Nama Aplikasi',
                rules: 'required|max:255',
                messages: ['required' => 'Nama aplikasi wajib diisi'],
                icon: 'badge',
            )->span(6),
            MrCatzFormField::text('app_short_name', label: 'Nama Singkat',
                rules: 'required|max:50',
                messages: ['required' => 'Nama singkat wajib diisi'],
                icon: 'short_text',
            )->span(6),
            MrCatzFormField::text('app_subtitle', label: 'Subtitle',
                rules: 'nullable|max:255',
                icon: 'subtitles',
            )->span(6),
            MrCatzFormField::text('app_motto', label: 'Motto',
                rules: 'nullable|max:255',
                icon: 'format_quote',
            )->span(6),
            MrCatzFormField::textarea('app_description', label: 'Deskripsi Aplikasi',
                rules: 'nullable|max:1000',
                placeholder: 'Masukkan deskripsi lengkap aplikasi...',
            ),
            MrCatzFormField::section('Informasi Kontak')->asCard(),
            MrCatzFormField::email('app_email', label: 'Email',
                rules: 'nullable|email|max:255',
                icon: 'mail',
            )->span(6),
            MrCatzFormField::text('app_phone', label: 'Telepon',
                rules: 'nullable|max:50',
                icon: 'phone',
            )->span(6),
            MrCatzFormField::text('app_email_domain', label: 'Domain Email (untuk admin)',
                rules: 'required|max:255',
                messages: ['required' => 'Domain email wajib diisi'],
                icon: 'alternate_email',
            )->span(6),
            MrCatzFormField::textarea('app_address', label: 'Alamat',
                rules: 'nullable|max:500',
                placeholder: 'Masukkan alamat lengkap...',
            ),

            MrCatzFormField::section('Media Sosial')->asCard(),
            MrCatzFormField::text('app_social_facebook', label: 'Facebook URL',
                rules: 'nullable|max:500',
                icon: 'link',
            )->span(6),
            MrCatzFormField::text('app_social_instagram', label: 'Instagram URL',
                rules: 'nullable|max:500',
                icon: 'link',
            )->span(6),
            MrCatzFormField::text('app_social_youtube', label: 'YouTube URL',
                rules: 'nullable|max:500',
                icon: 'link',
            )->span(6),
            MrCatzFormField::text('app_social_tiktok', label: 'TikTok URL',
                rules: 'nullable|max:500',
                icon: 'link',
            )->span(6),
        ];
    }

    public function mount()
    {
        $this->setTitle('Pengaturan Situs');
        session()->put('active', 'admin-settings');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'Pengaturan Situs', 'url' => null],
        ];

        $settings = Setting::pluck('value', 'key');

        $this->app_name = $settings->get('app_name', config('site.name'));
        $this->app_short_name = $settings->get('app_short_name', '');
        $this->app_subtitle = $settings->get('app_subtitle', '');
        $this->app_description = $settings->get('app_description', '');
        $this->app_motto = $settings->get('app_motto', '');
        $this->app_address = $settings->get('app_address', '');
        $this->app_phone = $settings->get('app_phone', '');
        $this->app_email = $settings->get('app_email', '');
        $this->app_email_domain = $settings->get('app_email_domain', 'app.test');
        $this->app_social_facebook = $settings->get('app_social_facebook', '#');
        $this->app_social_instagram = $settings->get('app_social_instagram', '#');
        $this->app_social_youtube = $settings->get('app_social_youtube', '#');
        $this->app_social_tiktok = $settings->get('app_social_tiktok', '#');
    }

    public function save()
    {
        $this->validate(
            $this->getFormValidationRules(),
            $this->getFormValidationMessages()
        );

        $settings = [
            'app_name' => $this->app_name,
            'app_short_name' => $this->app_short_name,
            'app_subtitle' => $this->app_subtitle,
            'app_description' => $this->app_description,
            'app_motto' => $this->app_motto,
            'app_address' => $this->app_address,
            'app_phone' => $this->app_phone,
            'app_email' => $this->app_email,
            'app_email_domain' => $this->app_email_domain,
            'app_social_facebook' => $this->app_social_facebook,
            'app_social_instagram' => $this->app_social_instagram,
            'app_social_youtube' => $this->app_social_youtube,
            'app_social_tiktok' => $this->app_social_tiktok,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        Log::info('Settings updated', [
            'updated_by' => auth()->id(),
        ]);

        $this->notice('success', 'Pengaturan berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.admin.setting.setting-page')
            ->layout('components.layouts.admin_layout');
    }
}
