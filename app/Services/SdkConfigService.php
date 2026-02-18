<?php

namespace App\Services;

use App\Models\App;
use App\Models\Platform;

class SdkConfigService
{
    /**
     * Get the Platform model for an app (for use in controllers/views).
     * Uses platform_id only; the legacy "platform" string attribute is ignored.
     */
    public function getPlatform(App $app): ?Platform
    {
        return $this->resolvePlatformModel($app);
    }

    /**
     * Resolve the Platform model for an app. Uses platform_id/relationship only;
     * the legacy "platform" string attribute is ignored to avoid type conflicts.
     */
    protected function resolvePlatformModel(App $app): ?Platform
    {
        if (!$app->platform_id) {
            return null;
        }
        if ($app->relationLoaded('platform')) {
            $rel = $app->getRelation('platform');
            return $rel instanceof Platform ? $rel : null;
        }
        return Platform::find($app->platform_id);
    }

    /**
     * Check if platform supports a feature.
     */
    public function platformSupportsFeature(?Platform $platform, string $feature): bool
    {
        if (!$platform || !$platform->default_features) {
            return true; // Default to showing all features if no platform selected
        }
        return in_array($feature, $platform->default_features);
    }

    /**
     * Get SDK installation instructions for an app's platform.
     */
    public function getInstallInstructions(App $app): ?array
    {
        $platform = $this->resolvePlatformModel($app);
        if (!$platform) {
            return null;
        }

        return match ($platform->slug) {
            'javascript' => $this->getJavaScriptInstructions($app),
            'react' => $this->getReactInstructions($app),
            'react-native' => $this->getReactNativeInstructions($app),
            'flutter' => $this->getFlutterInstructions($app),
            'ios-swift' => $this->getIosSwiftInstructions($app),
            'android-kotlin' => $this->getAndroidKotlinInstructions($app),
            'php' => $this->getPhpInstructions($app),
            'nodejs' => $this->getNodejsInstructions($app),
            'python' => $this->getPythonInstructions($app),
            'wordpress' => $this->getWordPressInstructions($app),
            default => $this->getGenericInstructions($app),
        };
    }

    protected function getGenericInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'Use HTTP POST requests with JSON payloads.',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'example' => "curl -X POST \"{$baseUrl}/track\" \\\n  -H \"Content-Type: application/json\" \\\n  -H \"X-Api-Key: YOUR_API_KEY\" \\\n  -d '{\"type\":\"page_view\",\"payload\":{}}'",
        ];
    }

    protected function getJavaScriptInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'Add this script tag to your HTML or import in your JavaScript bundle.',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<JS
// Initialize TraceNova
const TRACENOVA_API_KEY = '{$apiKey?->key_prefix}••••••••';
const TRACENOVA_BASE = '{$baseUrl}';

async function traceNovaTrack(endpoint, data) {
  await fetch(\`\${TRACENOVA_BASE}\${endpoint}\`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Api-Key': TRACENOVA_API_KEY,
    },
    body: JSON.stringify({
      ...data,
      environment: window.location.hostname === 'localhost' ? 'development' : 'production',
    }),
  });
}

// Track errors
window.addEventListener('error', (e) => {
  traceNovaTrack('/errors', {
    message: e.message,
    file: e.filename,
    line: e.lineno,
    severity: 'error',
  });
});

// Track page views
traceNovaTrack('/screen-views', {
  screen_name: document.title,
  load_time_ms: performance.timing.loadEventEnd - performance.timing.navigationStart,
});
JS,
        ];
    }

    protected function getReactInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'npm install (or use fetch directly)',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<JS
// utils/tracenova.js
const API_KEY = '{$apiKey?->key_prefix}••••••••';
const BASE_URL = '{$baseUrl}';

export const track = async (endpoint, data) => {
  await fetch(\`\${BASE_URL}\${endpoint}\`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Api-Key': API_KEY,
    },
    body: JSON.stringify({
      ...data,
      environment: process.env.NODE_ENV === 'development' ? 'development' : 'production',
    }),
  });
};

// In your React app
import { track } from './utils/tracenova';

// Track screen views (useEffect in components)
useEffect(() => {
  track('/screen-views', {
    screen_name: 'Home',
    load_time_ms: performance.now(),
  });
}, []);

// Track errors (ErrorBoundary)
componentDidCatch(error, errorInfo) {
  track('/errors', {
    message: error.message,
    stack_trace: error.stack,
    severity: 'error',
  });
}
JS,
        ];
    }

    protected function getReactNativeInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'No package needed. Use fetch API.',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<JS
// utils/tracenova.js
const API_KEY = '{$apiKey?->key_prefix}••••••••';
const BASE_URL = '{$baseUrl}';

export const track = async (endpoint, data) => {
  await fetch(\`\${BASE_URL}\${endpoint}\`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Api-Key': API_KEY,
    },
    body: JSON.stringify({
      ...data,
      environment: __DEV__ ? 'development' : 'production',
    }),
  });
};

// Track session start (App.js)
import { track } from './utils/tracenova';
import AsyncStorage from '@react-native-async-storage/async-storage';

const sessionId = await AsyncStorage.getItem('session_id') || uuid();
await AsyncStorage.setItem('session_id', sessionId);
track('/sessions/start', { session_id: sessionId });

// Track screen views (React Navigation)
import { useFocusEffect } from '@react-navigation/native';
useFocusEffect(() => {
  track('/screen-views', {
    screen_name: 'Profile',
    session_id: sessionId,
  });
});
JS,
        ];
    }

    protected function getFlutterInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'Add http package: flutter pub add http',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<DART
// lib/services/tracenova.dart
import 'package:http/http.dart' as http;
import 'dart:convert';

const apiKey = '{$apiKey?->key_prefix}••••••••';
const baseUrl = '{$baseUrl}';

Future<void> track(String endpoint, Map<String, dynamic> body) async {
  body['environment'] = kReleaseMode ? 'production' : 'development';
  await http.post(
    Uri.parse('\$baseUrl\$endpoint'),
    headers: {
      'Content-Type': 'application/json',
      'X-Api-Key': apiKey,
    },
    body: jsonEncode(body),
  );
}

// Usage
await track('/sessions/start', {'session_id': sessionId});
await track('/screen-views', {'screen_name': 'Home', 'session_id': sessionId});
DART,
        ];
    }

    protected function getIosSwiftInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'No dependencies. Use URLSession.',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<SWIFT
// TraceNova.swift
let apiKey = "{$apiKey?->key_prefix}••••••••"
let baseUrl = "{$baseUrl}"

func track(endpoint: String, body: [String: Any]) {
    var payload = body
    #if DEBUG
    payload["environment"] = "development"
    #else
    payload["environment"] = "production"
    #endif
    
    let url = URL(string: "\\(baseUrl)\\(endpoint)")!
    var request = URLRequest(url: url)
    request.httpMethod = "POST"
    request.setValue("application/json", forHTTPHeaderField: "Content-Type")
    request.setValue(apiKey, forHTTPHeaderField: "X-Api-Key")
    request.httpBody = try? JSONSerialization.data(withJSONObject: payload)
    URLSession.shared.dataTask(with: request).resume()
}

// Usage
track(endpoint: "/sessions/start", body: ["session_id": sessionId])
track(endpoint: "/errors", body: ["message": error.localizedDescription, "severity": "error"])
SWIFT,
        ];
    }

    protected function getAndroidKotlinInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'Add OkHttp: implementation("com.squareup.okhttp3:okhttp:4.x.x")',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<KOTLIN
// TraceNova.kt
val apiKey = "{$apiKey?->key_prefix}••••••••"
val baseUrl = "{$baseUrl}"

fun track(endpoint: String, body: Map<String, Any>) {
    val json = JSONObject(body).apply {
        put("environment", if (BuildConfig.DEBUG) "development" else "production")
    }
    val request = Request.Builder()
        .url("\$baseUrl\$endpoint")
        .post(json.toString().toRequestBody("application/json".toMediaType()))
        .addHeader("X-Api-Key", apiKey)
        .build()
    OkHttpClient().newCall(request).enqueue(object : Callback {
        override fun onFailure(call: Call, e: IOException) {}
        override fun onResponse(call: Call, response: Response) {}
    })
}

// Usage
track("/sessions/start", mapOf("session_id" to sessionId))
track("/errors", mapOf("message" to e.message!!, "severity" to "error"))
KOTLIN,
        ];
    }

    protected function getPhpInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'No dependencies. Use cURL or Guzzle.',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<PHP
// TraceNova.php
class TraceNova {
    private \$apiKey = '{$apiKey?->key_prefix}••••••••';
    private \$baseUrl = '{$baseUrl}';
    
    public function track(\$endpoint, \$data) {
        \$data['environment'] = app()->environment();
        \$ch = curl_init(\$this->baseUrl . \$endpoint);
        curl_setopt_array(\$ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(\$data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Api-Key: ' . \$this->apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec(\$ch);
        curl_close(\$ch);
    }
}

// Usage
\$traceNova = new TraceNova();
\$traceNova->track('/errors', [
    'message' => \$e->getMessage(),
    'severity' => 'error',
]);
PHP,
        ];
    }

    protected function getNodejsInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'npm install axios (or use fetch/node-fetch)',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<JS
// tracenova.js
const axios = require('axios');
const apiKey = '{$apiKey?->key_prefix}••••••••';
const baseUrl = '{$baseUrl}';

async function track(endpoint, data) {
  await axios.post(\`\${baseUrl}\${endpoint}\`, {
    ...data,
    environment: process.env.NODE_ENV || 'production',
  }, {
    headers: { 'X-Api-Key': apiKey },
  });
}

// Usage
track('/errors', {
  message: error.message,
  severity: 'error',
});
JS,
        ];
    }

    protected function getPythonInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'pip install requests',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<PY
# tracenova.py
import requests
import os

API_KEY = '{$apiKey?->key_prefix}••••••••'
BASE_URL = '{$baseUrl}'

def track(endpoint, data):
    data['environment'] = os.getenv('ENVIRONMENT', 'production')
    requests.post(
        f'{BASE_URL}{endpoint}',
        json=data,
        headers={'X-Api-Key': API_KEY},
    )

# Usage
track('/errors', {
    'message': str(e),
    'severity': 'error',
})
PY,
        ];
    }

    protected function getWordPressInstructions(App $app): array
    {
        $apiKey = $app->apiKeys()->first();
        $baseUrl = url('/api/v1');
        return [
            'install' => 'Add to functions.php or create a plugin',
            'config' => [
                'api_key' => $apiKey?->key_prefix . '••••••••' ?? 'YOUR_API_KEY',
                'base_url' => $baseUrl,
            ],
            'code' => <<<PHP
// functions.php or plugin
function tracenova_track(\$endpoint, \$data) {
    \$api_key = '{$apiKey?->key_prefix}••••••••';
    \$base_url = '{$baseUrl}';
    \$data['environment'] = wp_get_environment_type();
    
    wp_remote_post(\$base_url . \$endpoint, [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-Api-Key' => \$api_key,
        ],
        'body' => json_encode(\$data),
    ]);
}

// Track errors
set_error_handler(function(\$errno, \$errstr) {
    tracenova_track('/errors', [
        'message' => \$errstr,
        'severity' => 'error',
    ]);
});
PHP,
        ];
    }
}
