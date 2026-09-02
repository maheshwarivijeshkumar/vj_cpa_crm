/**
 * Form field definitions for the Discount module.
 *
 * All labels, placeholders, and error messages are defined here — never
 * inline in components. This enables future i18n without touching Vue files.
 *
 * Rule #8: Never hard-code form labels / placeholders / errors in components.
 */

export type FieldDef = {
  name: string;
  label: string;
  placeholder: string;
  errorPlaceholder: string;
  type?: 'text' | 'number' | 'date' | 'select' | 'textarea' | 'checkbox';
  helpText?: string;
};

export const discountFields = {
  code: {
    name: 'code',
    label: 'Discount Code',
    placeholder: 'e.g. SUMMER20, WELCOME10',
    errorPlaceholder: 'Discount code is required (uppercase letters, numbers, hyphens only)',
    type: 'text' as const,
    helpText: 'Will be auto-uppercased. Min 3 characters.',
  },
  name: {
    name: 'name',
    label: 'Discount Name',
    placeholder: 'e.g. Summer Sale 20%, Welcome Offer',
    errorPlaceholder: 'Please enter a name for this discount',
    type: 'text' as const,
    helpText: 'Internal reference name — not shown to tenants.',
  },
  description: {
    name: 'description',
    label: 'Description (optional)',
    placeholder: 'Internal notes about this discount…',
    errorPlaceholder: '',
    type: 'textarea' as const,
  },
  type: {
    name: 'type',
    label: 'Discount Type',
    placeholder: 'Select type',
    errorPlaceholder: 'Please select a discount type',
    type: 'select' as const,
    helpText: 'Fixed: exact amount off. Percentage: % off the total.',
  },
  value: {
    name: 'value',
    label: 'Discount Value',
    placeholder: 'e.g. 20 (for 20% or $20)',
    errorPlaceholder: 'Please enter a valid discount value greater than 0',
    type: 'number' as const,
  },
  max_discount_amount: {
    name: 'max_discount_amount',
    label: 'Maximum Discount Amount (optional)',
    placeholder: 'e.g. 100.00',
    errorPlaceholder: 'Must be a positive number',
    type: 'number' as const,
    helpText: 'Cap the discount at this dollar amount (for percentage types).',
  },
  applicability: {
    name: 'applicability',
    label: 'Applicability',
    placeholder: 'Select applicability',
    errorPlaceholder: 'Please select who this discount applies to',
    type: 'select' as const,
  },
  trigger: {
    name: 'trigger',
    label: 'Trigger',
    placeholder: 'Select trigger',
    errorPlaceholder: 'Please select a trigger',
    type: 'select' as const,
    helpText: 'How this discount is activated (manual, welcome, winback, etc.).',
  },
  valid_from: {
    name: 'valid_from',
    label: 'Valid From',
    placeholder: 'Select start date',
    errorPlaceholder: 'Please select a start date',
    type: 'date' as const,
  },
  valid_until: {
    name: 'valid_until',
    label: 'Expires On',
    placeholder: 'Select expiry date',
    errorPlaceholder: 'Expiry date must be on or after the start date',
    type: 'date' as const,
    helpText: 'Leave blank for no expiry.',
  },
  max_uses: {
    name: 'max_uses',
    label: 'Maximum Total Uses',
    placeholder: 'Leave blank for unlimited',
    errorPlaceholder: 'Must be at least 1',
    type: 'number' as const,
  },
  max_uses_per_tenant: {
    name: 'max_uses_per_tenant',
    label: 'Max Uses Per Tenant',
    placeholder: '1',
    errorPlaceholder: 'Must be at least 1',
    type: 'number' as const,
    helpText: 'How many times a single tenant can use this code.',
  },
  auto_email: {
    name: 'auto_email',
    label: 'Auto-send email when created',
    placeholder: '',
    errorPlaceholder: '',
    type: 'checkbox' as const,
    helpText: 'If enabled, the discount code is emailed to assigned tenants automatically.',
  },
  validate_code: {
    name: 'code',
    label: 'Have a discount code?',
    placeholder: 'Enter discount code',
    errorPlaceholder: 'Invalid or expired discount code',
    type: 'text' as const,
    helpText: 'Apply a discount code to reduce your subscription cost.',
  },
} satisfies Record<string, FieldDef>;

/** Discount type options */
export const discountTypeOptions = [
  { value: 'percentage', label: 'Percentage (%)' },
  { value: 'fixed',      label: 'Fixed Amount ($)' },
] as const;

/** Applicability options */
export const discountApplicabilityOptions = [
  { value: 'all',      label: 'All Tenants' },
  { value: 'specific', label: 'Specific Tenant(s)' },
  { value: 'plan',     label: 'Specific Plan(s)' },
] as const;

/** Trigger options */
export const discountTriggerOptions = [
  { value: 'manual',   label: 'Manual (platform admin)' },
  { value: 'welcome',  label: 'Welcome (new sign-up)' },
  { value: 'winback',  label: 'Win-back (lapsed tenant)' },
  { value: 'referral', label: 'Referral' },
  { value: 'promotion',label: 'Promotion' },
] as const;

/** Plan options for plan-restricted discounts */
export const subscriptionPlanOptions = [
  { value: 'trial',        label: 'Trial' },
  { value: 'starter',      label: 'Starter' },
  { value: 'professional', label: 'Professional' },
  { value: 'enterprise',   label: 'Enterprise' },
] as const;
