---
paths:
  - 'app/Http/Controllers/**/*.php'
---

# Controllers

## Controller pattern: thin, Policy + FormRequest + Action + Resource
Controllers follow: authorize() via Policy → validated() from Form Request → Action::execute() → new XxxResource($result). Never put Mail::send, AuditLog::create, or domain logic here. Return ApiResponse::success() / ApiResponse::error() wrappers — never raw Eloquent models. Route files are per-module under routes/api/v1/{module}.php.
