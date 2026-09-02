---
paths:
  - 'resources/js/pages/**/*.vue'
---

# Pages

## Page pattern: DataTable with all features, 3 UI states, full CRUD vertical slice
Every listing page must have: DataTable with search/sort/filter/column-visibility/pagination (10/25/50/100)/bulk-actions/import/export/print buttons (permission-gated). Every page must handle 3 states: LoadingSkeleton, EmptyState (with CTA action), ErrorState (with retry). All CRUD pages come as a vertical slice: Index, Create, Edit, View. Delete uses ConfirmDialog. Restore available where soft-delete applies. Routes follow Inertia conventions — no client-side router.
