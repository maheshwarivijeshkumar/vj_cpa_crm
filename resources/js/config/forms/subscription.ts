/**
 * Form field definitions for the Subscription module.
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

export const subscriptionFields = {
  plan: {
    name: 'plan',
    label: 'Subscription Plan',
    placeholder: 'Select a plan',
    errorPlaceholder: 'Please select a subscription plan',
    type: 'select' as const,
  },
  billing_cycle: {
    name: 'billing_cycle',
    label: 'Billing Cycle',
    placeholder: 'Select billing cycle',
    errorPlaceholder: 'Please select monthly or annual billing',
    type: 'select' as const,
    helpText: 'Annual billing includes a discount.',
  },
  starts_at: {
    name: 'starts_at',
    label: 'Start Date',
    placeholder: 'Select start date',
    errorPlaceholder: 'Start date is required',
    type: 'date' as const,
  },
  ends_at: {
    name: 'ends_at',
    label: 'End Date',
    placeholder: 'Select end date',
    errorPlaceholder: 'End date must be after the start date',
    type: 'date' as const,
  },
  amount_paid: {
    name: 'amount_paid',
    label: 'Amount Paid',
    placeholder: 'e.g. 99.00',
    errorPlaceholder: 'Amount paid is required',
    type: 'number' as const,
  },
  discount_code: {
    name: 'discount_code',
    label: 'Discount Code',
    placeholder: 'Enter discount code (optional)',
    errorPlaceholder: 'Invalid or expired discount code',
    type: 'text' as const,
    helpText: 'Apply a discount code to reduce the subscription cost.',
  },
  payment_reference: {
    name: 'payment_reference',
    label: 'Payment Reference',
    placeholder: 'e.g. txn_xxxxx, PAY-12345',
    errorPlaceholder: '',
    type: 'text' as const,
    helpText: 'Payment gateway transaction ID or reference number.',
  },
  payment_method: {
    name: 'payment_method',
    label: 'Payment Method',
    placeholder: 'e.g. credit_card, bank_transfer',
    errorPlaceholder: '',
    type: 'text' as const,
  },
  cancellation_reason: {
    name: 'reason',
    label: 'Reason for Cancellation',
    placeholder: 'Please describe why you are cancelling…',
    errorPlaceholder: 'Please provide a reason for cancellation (at least 10 characters)',
    type: 'textarea' as const,
    helpText: 'This helps us improve our service.',
  },
  cancel_immediately: {
    name: 'immediately',
    label: 'Cancel immediately',
    placeholder: '',
    errorPlaceholder: '',
    type: 'checkbox' as const,
    helpText: 'If unchecked, access continues until the end of the current billing period.',
  },
} satisfies Record<string, FieldDef>;

/** Plan options used in dropdowns */
export const planOptions = [
  { value: 'trial',        label: 'Trial',        price: 0,   description: 'Full access, 14 days' },
  { value: 'starter',      label: 'Starter',      price: 49,  description: 'Up to 50 clients' },
  { value: 'professional', label: 'Professional', price: 99,  description: 'Up to 200 clients' },
  { value: 'enterprise',   label: 'Enterprise',   price: 199, description: 'Unlimited clients' },
] as const;

/** Billing cycle options */
export const billingCycleOptions = [
  { value: 'monthly', label: 'Monthly' },
  { value: 'annual',  label: 'Annual (save ~20%)' },
] as const;

/** Status options for filters */
export const subscriptionStatusOptions = [
  { value: 'trial',     label: 'Trial',     badgeClass: 'bg-cpa-light text-cpa-dark' },
  { value: 'active',    label: 'Active',    badgeClass: 'bg-cpa-success-bg text-cpa-success' },
  { value: 'cancelled', label: 'Cancelled', badgeClass: 'bg-cpa-warning-bg text-cpa-warning' },
  { value: 'lapsed',    label: 'Lapsed',    badgeClass: 'bg-cpa-danger-bg text-cpa-danger' },
  { value: 'expired',   label: 'Expired',   badgeClass: 'bg-gray-100 text-gray-500' },
] as const;
