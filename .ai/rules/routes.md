---
paths:
  - 'routes/**/*.php'
---

# Routes

## Per-module route files under routes/api/v1/ — never a single api.php
Routes are split per module under routes/api/v1/{module}.php (e.g. crm.php, clients.php, accounting.php). Inertia page routes in routes/dashboard.php, routes/platform.php, routes/portal.php. All API routes protected by auth:sanctum. API versioned at /api/v1/. Every route file is registered in bootstrap/app.php. Never dump all routes into web.php or a single api.php.
