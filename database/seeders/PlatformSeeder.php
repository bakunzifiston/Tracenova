<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            // Browser
            ['name' => 'JavaScript', 'slug' => 'javascript', 'category' => Platform::CATEGORY_BROWSER, 'description' => 'Vanilla JavaScript for web browsers', 'default_features' => ['errors', 'performance', 'user_actions', 'screen_views'], 'sort_order' => 1],
            ['name' => 'React', 'slug' => 'react', 'category' => Platform::CATEGORY_BROWSER, 'description' => 'React web applications', 'default_features' => ['errors', 'performance', 'navigation', 'screen_views'], 'sort_order' => 2],
            ['name' => 'Vue.js', 'slug' => 'vue', 'category' => Platform::CATEGORY_BROWSER, 'description' => 'Vue.js web applications', 'default_features' => ['errors', 'performance', 'navigation', 'screen_views'], 'sort_order' => 3],
            ['name' => 'Angular', 'slug' => 'angular', 'category' => Platform::CATEGORY_BROWSER, 'description' => 'Angular web applications', 'default_features' => ['errors', 'performance', 'navigation', 'screen_views'], 'sort_order' => 4],

            // Mobile
            ['name' => 'React Native', 'slug' => 'react-native', 'category' => Platform::CATEGORY_MOBILE, 'description' => 'React Native mobile apps (iOS & Android)', 'default_features' => ['errors', 'performance', 'navigation', 'sessions', 'network_monitoring', 'offline'], 'sort_order' => 10],
            ['name' => 'Flutter', 'slug' => 'flutter', 'category' => Platform::CATEGORY_MOBILE, 'description' => 'Flutter mobile apps (iOS & Android)', 'default_features' => ['errors', 'performance', 'navigation', 'sessions', 'network_monitoring'], 'sort_order' => 11],
            ['name' => 'iOS (Swift)', 'slug' => 'ios-swift', 'category' => Platform::CATEGORY_MOBILE, 'description' => 'Native iOS apps with Swift', 'default_features' => ['errors', 'performance', 'sessions', 'network_monitoring'], 'sort_order' => 12],
            ['name' => 'Android (Kotlin)', 'slug' => 'android-kotlin', 'category' => Platform::CATEGORY_MOBILE, 'description' => 'Native Android apps with Kotlin', 'default_features' => ['errors', 'performance', 'sessions', 'network_monitoring'], 'sort_order' => 13],

            // Server
            ['name' => 'PHP', 'slug' => 'php', 'category' => Platform::CATEGORY_SERVER, 'description' => 'PHP server-side applications', 'default_features' => ['errors', 'performance', 'third_party_api', 'security_events'], 'sort_order' => 20],
            ['name' => 'Node.js', 'slug' => 'nodejs', 'category' => Platform::CATEGORY_SERVER, 'description' => 'Node.js server applications', 'default_features' => ['errors', 'performance', 'third_party_api', 'security_events'], 'sort_order' => 21],
            ['name' => 'Python', 'slug' => 'python', 'category' => Platform::CATEGORY_SERVER, 'description' => 'Python server applications (Django, Flask, FastAPI)', 'default_features' => ['errors', 'performance', 'third_party_api', 'security_events'], 'sort_order' => 22],
            ['name' => 'Ruby', 'slug' => 'ruby', 'category' => Platform::CATEGORY_SERVER, 'description' => 'Ruby server applications (Rails, Sinatra)', 'default_features' => ['errors', 'performance', 'third_party_api'], 'sort_order' => 23],
            ['name' => 'Go', 'slug' => 'go', 'category' => Platform::CATEGORY_SERVER, 'description' => 'Go server applications', 'default_features' => ['errors', 'performance', 'third_party_api'], 'sort_order' => 24],
            ['name' => 'Java', 'slug' => 'java', 'category' => Platform::CATEGORY_SERVER, 'description' => 'Java server applications (Spring, etc.)', 'default_features' => ['errors', 'performance', 'third_party_api'], 'sort_order' => 25],
            ['name' => '.NET', 'slug' => 'dotnet', 'category' => Platform::CATEGORY_SERVER, 'description' => '.NET server applications (C#)', 'default_features' => ['errors', 'performance', 'third_party_api'], 'sort_order' => 26],
            ['name' => 'WordPress', 'slug' => 'wordpress', 'category' => Platform::CATEGORY_SERVER, 'description' => 'WordPress plugins and themes', 'default_features' => ['errors', 'performance', 'user_actions'], 'sort_order' => 27],

            // Desktop
            ['name' => 'Electron', 'slug' => 'electron', 'category' => Platform::CATEGORY_DESKTOP, 'description' => 'Electron desktop applications', 'default_features' => ['errors', 'performance', 'sessions', 'navigation'], 'sort_order' => 30],
            ['name' => 'Tauri', 'slug' => 'tauri', 'category' => Platform::CATEGORY_DESKTOP, 'description' => 'Tauri desktop applications', 'default_features' => ['errors', 'performance', 'sessions'], 'sort_order' => 31],

            // Serverless
            ['name' => 'AWS Lambda', 'slug' => 'aws-lambda', 'category' => Platform::CATEGORY_SERVERLESS, 'description' => 'AWS Lambda functions', 'default_features' => ['errors', 'performance'], 'sort_order' => 40],
            ['name' => 'Vercel Functions', 'slug' => 'vercel', 'category' => Platform::CATEGORY_SERVERLESS, 'description' => 'Vercel serverless functions', 'default_features' => ['errors', 'performance'], 'sort_order' => 41],
            ['name' => 'Cloudflare Workers', 'slug' => 'cloudflare-workers', 'category' => Platform::CATEGORY_SERVERLESS, 'description' => 'Cloudflare Workers', 'default_features' => ['errors', 'performance'], 'sort_order' => 42],

            // Gaming
            ['name' => 'Unity', 'slug' => 'unity', 'category' => Platform::CATEGORY_GAMING, 'description' => 'Unity games (C#)', 'default_features' => ['errors', 'performance', 'sessions', 'user_actions'], 'sort_order' => 50],
            ['name' => 'Unreal Engine', 'slug' => 'unreal', 'category' => Platform::CATEGORY_GAMING, 'description' => 'Unreal Engine games (C++)', 'default_features' => ['errors', 'performance', 'sessions'], 'sort_order' => 51],
        ];

        foreach ($platforms as $platform) {
            Platform::create($platform);
        }
    }
}
