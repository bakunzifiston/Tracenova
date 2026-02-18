# TraceNova

Application Monitoring, Tracking & Business Intelligence platform — similar to Sentry, built with Laravel and MySQL (or SQLite). Collects technical, behavioral, performance, and business events from mobile and web applications via API.

## Stack

- **Laravel 12** — backend
- **MySQL** (or SQLite for local dev) — database
- **Laravel Breeze (Blade)** — auth and dashboard UI (no Filament)

## Features (Phase 1)

- **App management** — Register multiple apps (Web, React Native, WordPress, PHP, iOS, Android, Flutter).
- **API keys** — Generate secure API keys per app; revoke when needed.
- **Tracking toggle** — Enable or disable tracking per app.
- **Tracking API** — Ingest single or batch events from external apps using `X-Api-Key` or `Authorization: Bearer <key>`.
- **Session tracking** — Session start/end, duration, active sessions, and foreground vs background time (e.g. mobile).
- **Navigation tracking** — From screen → to screen with type (push, replace, back).
- **Screen view tracking** — Screen name, previous screen, timestamp, screen load time (ms).
- **User action tracking** — Button clicks, dashboard access, payments, form submissions (action_type, action_name, target, payload).
- **Error tracking** — Error message, stack trace, file & line, user info, device info, severity (debug, info, warning, error, critical).
- **Performance monitoring** — Screen load time, API response time, session duration; optional slow-performance flags (is_slow, threshold). **Geo-performance:** optional country, region, city for breakdown by location.

**Phase 2 — Advanced**

- **User journey mapping** — Full navigation flow per session; custom journey steps; timeline visualization (screens, navigations, actions, journey events).
- **Funnel & drop-off analytics** — Define multi-step funnels (e.g. Inventory → Requests → Payment → Success); record step events via API; conversion and drop-off per step in dashboard.
- **Business event tracking** — Orders created, payments completed, inventory updates, product requests (event_type + reference_id + payload).
- **Financial impact monitoring** — Revenue impact of failed payments, system errors, and downtime (amount + currency per impact type).
- **Offline & network monitoring** — Offline sessions (start/end + duration), sync retries (count, success/fail), network strength (weak/moderate/strong).
- **Third-Party API monitoring** — Payment, SMS, and email provider calls: success/failure, response time, status codes. Dashboard shows totals, success/failure, and breakdown by provider type.
- **Feature usage analytics** — Track which features are used most and least. Send `feature_name` (and optional `feature_category`); dashboard shows most-used and least-used lists plus recent usage.
- **Module health scoring** — Report per-module health score (0–100) with optional breakdown: errors count, errors score, speed score, drop-off score. Dashboard shows latest score per module with health label (Healthy / Fair / Degraded / Critical).
- **Security event monitoring** — Track failed logins, suspicious activity, and token abuse. Optional IP, user identifier, and reason. Dashboard shows counts by type and recent events.
- **Data integrity monitoring** — Detect negative stock, duplicate orders, and missing transactions. Report via API with event_type, optional reference_id and description. Dashboard shows counts by type and recent issues.
- **Smart alert system** — Send alerts for revenue risk, error spikes, and performance drops. alert_type + optional title, message, severity (low/medium/high/critical). Dashboard shows total alerts, unacknowledged count, and recent alerts by type.

## Setup

### 1. Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 2. Database

**SQLite (default):**  
Already configured; run:

```bash
php artisan migrate
```

**MySQL:**  
In `.env` set:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracenova
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then create the database and run:

```bash
php artisan migrate
```

### 3. Frontend (Breeze)

```bash
npm install && npm run build
```

### 4. Run

```bash
php artisan serve
```

Visit `http://localhost:8000`. Register or log in, then use **Apps** to create an app and generate an API key.

## Tracking API

**Endpoint:** `POST /api/v1/track`

**Headers:**  
- `Content-Type: application/json`  
- `X-Api-Key: <your-api-key>` or `Authorization: Bearer <your-api-key>`

**Body (single event):**

```json
{
  "type": "page_view",
  "payload": { "path": "/dashboard", "title": "Dashboard" },
  "session_id": "optional",
  "user_id": "optional",
  "url": "optional",
  "occurred_at": "optional ISO 8601 date"
}
```

**Batch:** `POST /api/v1/track/batch` with body:

```json
{
  "events": [
    { "type": "page_view", "payload": { "path": "/" } },
    { "type": "error", "payload": { "message": "Something broke" } }
  ]
}
```

Event types (examples): `error`, `page_view`, `performance`, `business`, `custom`.

## Session tracking

- **Session start:** `POST /api/v1/sessions/start`  
  Body: `{ "session_id": "unique-id", "user_id": "optional", "metadata": {} }`
- **Session heartbeat:** `POST /api/v1/sessions/heartbeat`  
  Body: `{ "session_id": "...", "foreground_seconds": 120, "background_seconds": 30 }`  
  Send cumulative seconds in foreground and background (e.g. from mobile app lifecycle).
- **Session end:** `POST /api/v1/sessions/end`  
  Body: `{ "session_id": "...", "duration_seconds": 150, "foreground_seconds": 120, "background_seconds": 30 }`  
  `duration_seconds` is optional; server computes from start if omitted.

The dashboard per app shows **active sessions** (activity in last 30 minutes), **session duration** (total and average), and **foreground vs background** time.

## Navigation tracking

- **Record navigation:** `POST /api/v1/navigation`  
  Body: `{ "from_screen": "Home", "to_screen": "Profile", "navigation_type": "push" | "replace" | "back", "session_id": "optional", "user_id": "optional", "occurred_at": "optional ISO 8601", "metadata": {} }`

## Screen view tracking

- **Record screen view:** `POST /api/v1/screen-views`  
  Body: `{ "screen_name": "Profile", "previous_screen": "Home", "load_time_ms": 120, "session_id": "optional", "user_id": "optional", "occurred_at": "optional ISO 8601", "metadata": {} }`  
  `load_time_ms` is optional (screen load time in milliseconds).

The dashboard shows **navigation** counts by type (push/replace/back), recent navigations, **screen views** total, average load time, top screens, and recent views.

## User action tracking

- **Record action:** `POST /api/v1/user-actions`  
  Body: `{ "action_type": "button_click" | "dashboard_access" | "payment" | "form_submission" | "custom", "action_name": "optional", "target": "optional", "payload": {}, "session_id": "optional", "user_id": "optional", "occurred_at": "optional" }`

## Error tracking

- **Record error:** `POST /api/v1/errors`  
  Body: `{ "message": "required", "stack_trace": "optional", "file": "optional", "line": "optional", "severity": "debug|info|warning|error|critical", "user_info": {}, "device_info": {}, "context": {}, "session_id": "optional", "user_id": "optional", "occurred_at": "optional" }`

The dashboard shows **user actions** by type and recent list, **errors** by severity and recent list (message, file:line, time).

## Performance monitoring

- **Record metric:** `POST /api/v1/performance-metrics`  
  Body: `{ "metric_type": "screen_load" | "api_response" | "session_duration" | "custom", "name": "optional label", "value": 250, "value_unit": "ms" | "s", "is_slow": false, "threshold": 500, "metadata": {}, "session_id": "optional", "user_id": "optional", "occurred_at": "optional", "country_code": "optional e.g. US", "country": "optional", "region": "optional", "city": "optional" }`  
  Use **value_unit** `ms` for screen load and API response time, `s` for session duration. Set **is_slow** when the value exceeds your threshold. **Geo-performance:** include **country_code**, **country**, **region**, **city** (e.g. from IP geolocation) to see performance breakdown by country, region, and city in the dashboard.

The dashboard shows **total metrics**, **slow-flagged count**, breakdown by type (screen load, API response, session duration) with averages, and a recent table with slow rows highlighted.

## Funnel & drop-off analytics (Phase 2)

1. **Create a funnel** in the dashboard (Apps → [App] → Funnels): name and ordered steps (e.g. `inventory`, `requests`, `payment`, `success`).
2. **Record step events** from your app: `POST /api/v1/funnel-steps`  
   Body: `{ "funnel_id": 1 }` or `{ "funnel_slug": "checkout" }, "step_key": "inventory", "session_id": "sess_123", "user_id": "optional", "occurred_at": "optional", "metadata": {} }`  
   `step_key` must match one of the funnel’s steps.
3. The **funnel detail** page shows conversion (sessions per step) and drop-off between steps.

## User journey mapping (Phase 2)

- **Custom journey steps:** `POST /api/v1/journey`  
  Body: `{ "step_name": "Viewed product", "step_type": "screen|action|custom", "payload": {}, "session_id": "required", "user_id": "optional", "occurred_at": "optional" }`
- **Journey visualization:** In the app dashboard, “User journeys” lists recent sessions; “View journey” opens a timeline that merges **journey events**, **screen views**, **navigation events**, and **user actions** for that session in order.

## Business event tracking

- **Record event:** `POST /api/v1/business-events`  
  Body: `{ "event_type": "order_created" | "payment_completed" | "inventory_update" | "product_request" | "custom", "reference_id": "optional", "payload": {} (required), "session_id": "optional", "user_id": "optional", "occurred_at": "optional" }`  
  Use **payload** for event-specific data (amount, items, product_id, quantity, etc.). Dashboard shows counts by type and recent events.

## Financial impact monitoring

- **Record impact:** `POST /api/v1/financial-impacts`  
  Body: `{ "impact_type": "failed_payment" | "system_error" | "downtime" | "custom", "amount": 99.99, "currency": "USD", "reference_id": "optional", "description": "optional", "metadata": {}, "occurred_at": "optional", "session_id": "optional", "user_id": "optional" }`  
  Use **amount** for the estimated revenue impact (e.g. failed payment = order value; downtime = estimated lost revenue). Dashboard shows total impact and breakdown by type.

## Offline & network monitoring

- **Record event:** `POST /api/v1/network-monitoring`  
  Body: `{ "event_type": "offline_start" | "offline_end" | "sync_retry" | "network_strength", "occurred_at": "optional", "duration_seconds": "optional (for offline_end)", "retry_count": "optional (for sync_retry)", "success": "optional boolean (for sync_retry)", "network_strength": "optional e.g. weak|moderate|strong (for network_strength)", "payload": {}, "session_id": "optional", "user_id": "optional" }`  
  **Offline:** send `offline_start` when app goes offline, `offline_end` with `duration_seconds` when back online. **Sync retries:** send `sync_retry` with `retry_count` and `success`. **Network strength:** send `network_strength` with `network_strength` value. Dashboard shows offline session count, sync retry success/fail, and strength distribution.

## Third-Party API monitoring

- **Record API call:** `POST /api/v1/third-party-api`  
  Body: `{ "provider_type": "payment" | "sms" | "email", "provider_name": "optional e.g. Stripe, Twilio, SendGrid", "operation": "optional e.g. charge, send_sms", "success": true|false, "response_time_ms": "optional", "status_code": "optional", "error_message": "optional", "request_id": "optional", "payload": {}, "occurred_at": "optional", "session_id": "optional", "user_id": "optional" }`  
  Dashboard shows total calls, success/failure counts, average response time, and breakdown by provider type (payment, SMS, email).

## Feature usage analytics

- **Record feature usage:** `POST /api/v1/feature-usage`  
  Body: `{ "feature_name": "required", "feature_category": "optional", "payload": {}, "occurred_at": "optional", "session_id": "optional", "user_id": "optional" }`  
  Dashboard shows **most used** and **least used** features (by count), plus recent usage.

## Module health scoring

- **Record module health:** `POST /api/v1/module-health`  
  Body: `{ "module_id": "required", "module_name": "optional", "score": 0-100, "period_type": "optional daily|weekly", "period_start": "optional date", "period_end": "optional date", "errors_count": "optional", "errors_score": "optional 0-100", "speed_score": "optional 0-100", "drop_off_score": "optional 0-100", "metadata": {}, "recorded_at": "optional" }`  
  Score 0–100 (higher = better). Dashboard shows latest score per module with label (Healthy / Fair / Degraded / Critical) and optional breakdown (errors, speed, drop-off).

## Security event monitoring

- **Record security event:** `POST /api/v1/security-events`  
  Body: `{ "event_type": "failed_login" | "suspicious_activity" | "token_abuse", "ip_address": "optional", "user_identifier": "optional e.g. email/username", "reason": "optional", "payload": {}, "occurred_at": "optional", "session_id": "optional", "user_id": "optional" }`  
  Dashboard shows total events, counts by type (failed login, suspicious activity, token abuse), and recent events with user/IP and reason.

## Data integrity monitoring

- **Record data integrity issue:** `POST /api/v1/data-integrity`  
  Body: `{ "event_type": "negative_stock" | "duplicate_order" | "missing_transaction", "reference_id": "optional", "description": "optional", "payload": {}, "occurred_at": "optional", "session_id": "optional", "user_id": "optional" }`  
  Dashboard shows total issues, counts by type (negative stock, duplicate order, missing transaction), and recent events with reference and description.

## Smart alert system

- **Send alert:** `POST /api/v1/alerts`  
  Body: `{ "alert_type": "revenue_risk" | "error_spike" | "performance_drop", "title": "optional", "message": "optional", "severity": "optional low|medium|high|critical", "payload": {}, "channel": "optional e.g. in_app", "occurred_at": "optional" }`  
  Dashboard shows total alerts, unacknowledged count, counts by type, and recent alerts with title/message and severity.

## Project structure

```
app/
  Http/
    Controllers/
      Api/TrackController.php   # Ingest API
      Api/SessionController.php # Session start / end / heartbeat
      Api/NavigationController.php # Navigation + screen view tracking
      Api/UserActionController.php # User action tracking
      Api/ErrorController.php  # Error tracking
      Api/PerformanceMetricController.php # Performance metrics
      Api/FunnelController.php # Funnel step events
      Api/JourneyController.php # Journey steps
      Api/BusinessEventController.php # Business events
      Api/FinancialImpactController.php # Financial/revenue impact
      Api/NetworkMonitoringController.php # Offline & network events
      Api/ThirdPartyApiController.php # Payment / SMS / email API monitoring
      Api/FeatureUsageController.php # Feature usage analytics
      Api/ModuleHealthController.php # Module health scores
      Api/SecurityEventController.php # Security events (failed login, suspicious, token abuse)
      Api/DataIntegrityController.php # Data integrity (negative stock, duplicate order, missing transaction)
      Api/AlertController.php # Smart alerts (revenue risk, error spike, performance drop)
      FunnelController.php # Dashboard funnel CRUD
      AppController.php   # Dashboard app CRUD + journey view
    Middleware/
      ValidateTrackingApiKey.php
  Models/
    App.php
    ApiKey.php
    TrackingEvent.php
    TrackingSession.php
    NavigationEvent.php
    ScreenView.php
    UserAction.php
    ErrorEvent.php
    PerformanceMetric.php
    Funnel.php
    FunnelStepEvent.php
    JourneyEvent.php
    BusinessEvent.php
    FinancialImpact.php
    NetworkMonitoringEvent.php
    ThirdPartyApiEvent.php
    FeatureUsageEvent.php
    ModuleHealthScore.php
    SecurityEvent.php
    DataIntegrityEvent.php
    Alert.php
  Services/
    ApiKeyService.php
database/migrations/
  *_create_apps_table.php
  *_create_api_keys_table.php
  *_create_tracking_events_table.php
  *_create_tracking_sessions_table.php
  *_create_navigation_events_table.php
  *_create_screen_views_table.php
  *_create_user_actions_table.php
  *_create_error_events_table.php
  *_create_performance_metrics_table.php
  *_create_funnels_table.php
  *_create_funnel_step_events_table.php
  *_create_journey_events_table.php
  *_create_business_events_table.php
  *_create_financial_impacts_table.php
  *_create_network_monitoring_events_table.php
  *_create_third_party_api_events_table.php
  *_create_feature_usage_events_table.php
  *_create_module_health_scores_table.php
  *_create_security_events_table.php
  *_create_data_integrity_events_table.php
  *_create_alerts_table.php
routes/
  api.php   # ... + data-integrity, alerts
  web.php   # dashboard, apps.*, apps.funnels.*, apps.journey.show
resources/views/
  apps/     # index, create, edit, show
  funnels/  # index, create, show, edit
  journey/  # show
```

## License

MIT.
# Tracenova
