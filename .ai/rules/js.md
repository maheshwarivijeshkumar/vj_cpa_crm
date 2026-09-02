---
paths:
  - 'resources/js/**/*.{vue,ts}'
---

# Js

## Vue 3 + Inertia frontend: form config, validation UX, permission helpers, palette tokens
Stack is Vue 3 (script setup, Composition API) + Inertia.js + TypeScript + Tailwind CSS 4. Use Lucide Vue for icons only. Form field labels/placeholders/error messages must come from config/forms/{module}.ts — never inline strings. Use VeeValidate + Zod for frontend validation. Error message replaces placeholder text (errorPlaceholder). Never use HTML required attribute. Permission gates: v-if="can('module.action')" controls visibility only — backend is final authority. Color palette: use cpa-* Tailwind tokens exclusively (defined in .kiro/steering/color-palette.md).
