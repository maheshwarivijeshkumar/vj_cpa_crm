/**
 * Shared type definitions matching what HandleInertiaRequests shares.
 * Keep in sync with app/Http/Middleware/HandleInertiaRequests.php
 */

export interface AuthUser {
    id: number
    uuid: string
    name: string
    first_name: string
    last_name: string
    email: string
    username: string | null
    avatar_path: string | null
    user_type: 'platform_admin' | 'firm_owner' | 'firm_user' | 'client'
    status: 'active' | 'inactive' | 'suspended' | 'invited' | 'archived'
    must_change_password: boolean
    two_factor_enabled: boolean
    timezone_id: number | null
    language_id: number | null
    currency_id: number | null
    date_format: string | null
    preferences: Record<string, unknown>
}

export interface AuthTenant {
    id: number
    uuid: string
    name: string
    slug: string
    plan: string
    status: string
    logo_path: string | null
    brand_colors: Record<string, string> | null
    currency_id: number | null
    timezone_id: number | null
    language_id: number | null
    fiscal_year_start_month: number
    fiscal_year_start_day: number
}

export interface FlashMessages {
    success: string | null
    error: string | null
    warning: string | null
    info: string | null
}

export interface AppMeta {
    name: string
    env: string
    locale: string
    version: string
}

export interface SharedProps {
    auth: {
        user: AuthUser | null
        permissions: string[]
        roles: string[]
    }
    tenant: AuthTenant | null
    flash: FlashMessages
    app: AppMeta
}

// ─── Pagination ───────────────────────────────────────────────────────────────
export interface PaginationMeta {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
}

export interface Paginated<T> {
    data: T[]
    meta: PaginationMeta
}

// ─── API response wrapper ────────────────────────────────────────────────────
export interface ApiSuccess<T = unknown> {
    success: true
    message: string
    data: T
    meta?: PaginationMeta
}

export interface ApiError {
    success: false
    message: string
    code: string
    errors: Record<string, string[]>
}

export type ApiResponse<T = unknown> = ApiSuccess<T> | ApiError

// ─── Breadcrumb ───────────────────────────────────────────────────────────────
export interface BreadcrumbItem {
    label: string
    href?: string
}

// ─── Select option ────────────────────────────────────────────────────────────
export interface SelectOption {
    value: string | number
    label: string
    disabled?: boolean
}
