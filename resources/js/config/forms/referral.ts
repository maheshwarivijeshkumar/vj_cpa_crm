/**
 * Form field definitions for the Referral module.
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

export const referralFields = {
  share_link: {
    name: 'share_link',
    label: 'Your Referral Link',
    placeholder: 'Generating your link…',
    errorPlaceholder: '',
    type: 'text' as const,
    helpText: 'Share this link with colleagues. You earn a reward when they sign up and subscribe.',
  },
  redeem_reward_type: {
    name: 'reward_type',
    label: 'Redeem As',
    placeholder: 'Select reward type',
    errorPlaceholder: 'Please select how you want to redeem your balance',
    type: 'select' as const,
    helpText: 'Points or credit can be applied to your next subscription renewal.',
  },
  redeem_amount: {
    name: 'amount',
    label: 'Amount to Redeem',
    placeholder: 'e.g. 50',
    errorPlaceholder: 'Please enter a valid amount greater than 0',
    type: 'number' as const,
    helpText: 'Cannot exceed your available balance.',
  },
} satisfies Record<string, FieldDef>;

/** Reward type options */
export const rewardTypeOptions = [
  { value: 'points', label: 'Points' },
  { value: 'credit', label: 'Account Credit ($)' },
] as const;

/** Referral status badge mapping (consistent with statusColors.ts) */
export const referralStatusOptions = [
  { value: 'pending',  label: 'Pending',   badgeClass: 'bg-cpa-warning-bg text-cpa-warning' },
  { value: 'signed',   label: 'Signed Up', badgeClass: 'bg-cpa-light text-cpa-dark' },
  { value: 'verified', label: 'Verified',  badgeClass: 'bg-cpa-very-light text-cpa-medium-dark' },
  { value: 'rewarded', label: 'Rewarded',  badgeClass: 'bg-cpa-success-bg text-cpa-success' },
  { value: 'expired',  label: 'Expired',   badgeClass: 'bg-gray-100 text-gray-500' },
  { value: 'revoked',  label: 'Revoked',   badgeClass: 'bg-cpa-danger-bg text-cpa-danger' },
] as const;

/** Reward summary display labels */
export const rewardLabels = {
  points: {
    singular: 'Point',
    plural:   'Points',
    icon:     'Star',
    color:    'text-cpa-warning',
    bg:       'bg-cpa-warning-bg',
  },
  credit: {
    singular: 'Credit',
    plural:   'Credits',
    icon:     'DollarSign',
    color:    'text-cpa-success',
    bg:       'bg-cpa-success-bg',
  },
} as const;
