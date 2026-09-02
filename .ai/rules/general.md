---
paths:
  - '.smartfox*'
---

# General

## .smartfox replaces .env — custom loader, bootstrap credentials via Hash::make()
This project uses .smartfox instead of .env — same KEY=VALUE format, loaded by custom bootstrap. Never reference .env in new code. The example file is .smartfox.example. Bootstrap credentials: administrator@cpacrm.com / administrator90@#$ (Hash::make() — never plaintext). Production must use INITIAL_ADMIN_EMAIL / INITIAL_ADMIN_PASSWORD env vars and force must_change_password=true on first login.
