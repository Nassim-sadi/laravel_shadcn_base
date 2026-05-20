<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        Service::create([
            'title' => ['fr' => 'Développement Web', 'en' => 'Web Development', 'ar' => 'تطوير الويب'],
            'description' => ['fr' => 'Création de sites web modernes et performants.', 'en' => 'Building modern, high-performance websites.', 'ar' => 'بناء مواقع ويب حديثة وعالية الأداء.'],
            'icon' => 'code',
            'order' => 1,
            'is_active' => true,
        ]);

        Service::create([
            'title' => ['fr' => 'Applications Mobiles', 'en' => 'Mobile Apps', 'ar' => 'تطبيقات الجوال'],
            'description' => ['fr' => 'Développement d\'applications iOS et Android.', 'en' => 'iOS and Android app development.', 'ar' => 'تطوير تطبيقات iOS و Android.'],
            'icon' => 'smartphone',
            'order' => 2,
            'is_active' => true,
        ]);

        Service::create([
            'title' => ['fr' => 'Consulting SEO', 'en' => 'SEO Consulting', 'ar' => 'استشارات تحسين محركات البحث'],
            'description' => ['fr' => 'Optimisation de votre présence en ligne.', 'en' => 'Optimize your online presence.', 'ar' => 'حسّن وجودك على الإنترنت.'],
            'icon' => 'search',
            'order' => 3,
            'is_active' => true,
        ]);

        Project::create([
            'title' => ['fr' => 'Site E-commerce', 'en' => 'E-commerce Site', 'ar' => 'موقع تجارة إلكترونية'],
            'description' => ['fr' => 'Plateforme de vente en ligne avec paiement intégré.', 'en' => 'Online sales platform with integrated payment.', 'ar' => 'منصة مبيعات عبر الإنترنت مع دفع مدمج.'],
            'client' => ['fr' => 'Client Exemple', 'en' => 'Example Client', 'ar' => 'عميل مثال'],
            'technologies' => ['Laravel', 'Vue.js', 'Stripe'],
            'order' => 1,
            'is_active' => true,
        ]);

        Project::create([
            'title' => ['fr' => 'Dashboard Analytique', 'en' => 'Analytics Dashboard', 'ar' => 'لوحة تحليلات'],
            'description' => ['fr' => 'Tableau de bord interactif pour visualiser les données.', 'en' => 'Interactive dashboard for data visualization.', 'ar' => 'لوحة تفاعلية لتصور البيانات.'],
            'client' => ['fr' => 'Startup Tech', 'en' => 'Tech Startup', 'ar' => 'شركة ناشئة'],
            'technologies' => ['React', 'D3.js', 'Node.js'],
            'order' => 2,
            'is_active' => true,
        ]);

        Testimonial::create([
            'name' => ['fr' => 'Jean Dupont', 'en' => 'John Smith', 'ar' => 'أحمد علي'],
            'position' => ['fr' => 'Directeur', 'en' => 'CEO', 'ar' => 'مدير'],
            'company' => ['fr' => 'Entreprise ABC', 'en' => 'ABC Corp', 'ar' => 'شركة ABC'],
            'content' => ['fr' => 'Excellent travail, livré dans les délais. Je recommande vivement.', 'en' => 'Great work, delivered on time. Highly recommended.', 'ar' => 'عمل ممتاز، تم التسليم في الوقت المحدد. أوصي بشدة.'],
            'rating' => 5,
            'is_active' => true,
            'order' => 1,
        ]);

        Testimonial::create([
            'name' => ['fr' => 'Marie Martin', 'en' => 'Jane Doe', 'ar' => 'سارة أحمد'],
            'position' => ['fr' => 'CMO', 'en' => 'CMO', 'ar' => 'مدير تسويق'],
            'company' => ['fr' => 'Agence XYZ', 'en' => 'XYZ Agency', 'ar' => 'وكالة XYZ'],
            'content' => ['fr' => 'Une équipe professionnelle et à l\'écoute. Résultat au-delà de nos attentes.', 'en' => 'A professional and attentive team. Result exceeded our expectations.', 'ar' => 'فريق محترف ومهتم. النتيجة تجاوزت توقعاتنا.'],
            'rating' => 5,
            'is_active' => true,
            'order' => 2,
        ]);

        Faq::create([
            'question' => ['fr' => 'Quels sont vos délais de livraison ?', 'en' => 'What are your delivery times?', 'ar' => 'ما هي أوقات التسليم الخاصة بك؟'],
            'answer' => ['fr' => 'Les délais varient selon le projet, généralement 2 à 8 semaines.', 'en' => 'Timelines vary by project, typically 2 to 8 weeks.', 'ar' => 'تختلف الجداول الزمنية حسب المشروع، عادة من 2 إلى 8 أسابيع.'],
            'is_active' => true,
            'order' => 1,
        ]);

        Faq::create([
            'question' => ['fr' => 'Proposez-vous un support après livraison ?', 'en' => 'Do you offer post-delivery support?', 'ar' => 'هل تقدم دعماً بعد التسليم؟'],
            'answer' => ['fr' => 'Oui, nous offrons une période de garantie et des contrats de maintenance.', 'en' => 'Yes, we offer a warranty period and maintenance contracts.', 'ar' => 'نعم، نقدم فترة ضمان وعقود صيانة.'],
            'is_active' => true,
            'order' => 2,
        ]);

        Faq::create([
            'question' => ['fr' => 'Quels types de projets acceptez-vous ?', 'en' => 'What types of projects do you accept?', 'ar' => 'ما أنواع المشاريع التي تقبلها؟'],
            'answer' => ['fr' => 'Nous travaillons sur des sites web, applications mobiles, dashboards, et plateformes e-learning.', 'en' => 'We work on websites, mobile apps, dashboards, and e-learning platforms.', 'ar' => 'نعمل على مواقع الويب وتطبيقات الجوال ولوحات المعلومات ومنصات التعلم الإلكتروني.'],
            'is_active' => true,
            'order' => 3,
        ]);

        EmailTemplate::create([
            'key' => 'contact_confirmation',
            'name' => ['fr' => 'Confirmation de contact', 'en' => 'Contact Confirmation', 'ar' => 'تأكيد الاتصال'],
            'subject' => ['fr' => 'Merci de nous avoir contactés, {name}', 'en' => 'Thank you for contacting us, {name}', 'ar' => 'شكراً لتواصلك معنا، {name}'],
            'body' => [
                'fr' => '<h1>Merci {name}</h1><p>Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.</p><p>Sujet: {subject}</p><p>Message: {message}</p>',
                'en' => '<h1>Thank you {name}</h1><p>We have received your message and will get back to you shortly.</p><p>Subject: {subject}</p><p>Message: {message}</p>',
                'ar' => '<h1>شكراً {name}</h1><p>لقد استلمنا رسالتك وسنرد عليك قريباً.</p><p>الموضوع: {subject}</p><p>الرسالة: {message}</p>',
            ],
            'variables' => ['name', 'email', 'subject', 'message'],
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'key' => 'contact_notification_admin',
            'name' => ['fr' => 'Notification de contact (admin)', 'en' => 'Contact Notification (admin)', 'ar' => 'إشعار اتصال (مشرف)'],
            'subject' => ['fr' => 'Nouveau message de {name}', 'en' => 'New message from {name}', 'ar' => 'رسالة جديدة من {name}'],
            'body' => [
                'fr' => '<h1>Nouveau message de contact</h1><p><strong>Nom:</strong> {name}</p><p><strong>Email:</strong> {email}</p><p><strong>Sujet:</strong> {subject}</p><p><strong>Message:</strong></p><p>{message}</p>',
                'en' => '<h1>New contact message</h1><p><strong>Name:</strong> {name}</p><p><strong>Email:</strong> {email}</p><p><strong>Subject:</strong> {subject}</p><p><strong>Message:</strong></p><p>{message}</p>',
                'ar' => '<h1>رسالة اتصال جديدة</h1><p><strong>الاسم:</strong> {name}</p><p><strong>البريد الإلكتروني:</strong> {email}</p><p><strong>الموضوع:</strong> {subject}</p><p><strong>الرسالة:</strong></p><p>{message}</p>',
            ],
            'variables' => ['name', 'email', 'subject', 'message'],
            'is_active' => true,
        ]);

        $general = BlogCategory::create(['name' => ['fr' => 'Général', 'en' => 'General', 'ar' => 'عام'], 'slug' => 'general', 'description' => ['fr' => 'Articles généraux', 'en' => 'General articles', 'ar' => 'مقالات عامة'], 'is_published' => true]);
        $tech = BlogCategory::create(['name' => ['fr' => 'Technologie', 'en' => 'Technology', 'ar' => 'تقنية'], 'slug' => 'technology', 'description' => ['fr' => 'Articles sur la technologie', 'en' => 'Technology articles', 'ar' => 'مقالات تقنية'], 'is_published' => true]);
        $business = BlogCategory::create(['name' => ['fr' => 'Business', 'en' => 'Business', 'ar' => 'أعمال'], 'slug' => 'business', 'description' => ['fr' => 'Articles business', 'en' => 'Business articles', 'ar' => 'مقالات أعمال'], 'is_published' => true]);

        $laravel = BlogTag::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $vue = BlogTag::create(['name' => 'Vue.js', 'slug' => 'vuejs']);
        $seo = BlogTag::create(['name' => 'SEO', 'slug' => 'seo']);
        $design = BlogTag::create(['name' => 'Design', 'slug' => 'design']);

        $user = User::first() ?? User::factory()->create();

        $post1 = BlogPost::create(['title' => ['fr' => 'Bienvenue sur NsBase', 'en' => 'Welcome to NsBase', 'ar' => 'مرحباً بك في NsBase'], 'slug' => 'welcome-to-nsbase', 'excerpt' => ['fr' => 'Notre plateforme de développement', 'en' => 'Our development platform', 'ar' => 'منصة التطوير لدينا'], 'is_published' => true, 'featured' => true, 'user_id' => $user->id, 'category_id' => $general->id]);
        $post1->body()->create(['body' => ['fr' => '<p>Bienvenue sur NsBase, notre plateforme de développement web.</p>', 'en' => '<p>Welcome to NsBase, our web development platform.</p>', 'ar' => '<p>مرحباً بك في NsBase، منصة تطوير الويب الخاصة بنا.</p>']]);
        $post1->tags()->sync([$laravel->id, $vue->id]);

        $post2 = BlogPost::create(['title' => ['fr' => 'Pourquoi choisir Laravel', 'en' => 'Why Choose Laravel', 'ar' => 'لماذا تختار Laravel'], 'slug' => 'why-choose-laravel', 'excerpt' => ['fr' => 'Les avantages de Laravel', 'en' => 'The benefits of Laravel', 'ar' => 'فوائد Laravel'], 'is_published' => true, 'featured' => false, 'user_id' => $user->id, 'category_id' => $tech->id]);
        $post2->body()->create(['body' => ['fr' => '<p>Laravel est le framework PHP le plus populaire.</p>', 'en' => '<p>Laravel is the most popular PHP framework.</p>', 'ar' => '<p>Laravel هو إطار عمل PHP الأكثر شهرة.</p>']]);
        $post2->tags()->sync([$laravel->id]);

        Setting::create([
            'key' => 'site_name',
            'group' => 'general',
            'value' => 'NsBase',
            'type' => 'string',
            'name' => 'Site Name',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'email',
            'group' => 'general',
            'value' => 'contact@example.com',
            'type' => 'string',
            'name' => 'Contact Email',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'phone',
            'group' => 'general',
            'value' => '+1 (555) 000-0000',
            'type' => 'string',
            'name' => 'Phone Number',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'address',
            'group' => 'general',
            'value' => '123 Main Street, City',
            'type' => 'string',
            'name' => 'Address',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'default_locale',
            'group' => 'localization',
            'value' => 'fr',
            'type' => 'string',
            'name' => 'Default Locale',
            'is_public' => false,
        ]);

        app(\App\Services\AiSettingsService::class)->seedDefaults();
    }
}
