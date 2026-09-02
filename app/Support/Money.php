<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Lightweight money value object.
 *
 * Rules:
 *  - Stored as integer cents (or smallest currency unit) to avoid float errors.
 *  - DB columns must be decimal(20,6). Convert via fromDecimal() / toDecimal().
 *  - Never use float arithmetic on financial values.
 */
final class Money
{
    private function __construct(
        private readonly int $amount,      // in smallest unit (cents)
        private readonly string $currency, // ISO 4217
        private readonly int $precision,   // decimal places (2 for USD, 0 for JPY, etc.)
    ) {}

    // ─── Factories ────────────────────────────────────────────────────────────

    /**
     * Create from a decimal string or numeric value (e.g. "1234.56" → 123456 cents).
     * Uses BC Math to avoid float issues.
     */
    public static function fromDecimal(string|int|float $amount, string $currency = 'USD', int $precision = 2): self
    {
        $factor = (int) bcpow('10', (string) $precision);
        $cents  = (int) bcmul((string) $amount, (string) $factor, 0);

        return new self($cents, strtoupper($currency), $precision);
    }

    /**
     * Create from raw integer cents.
     */
    public static function fromCents(int $cents, string $currency = 'USD', int $precision = 2): self
    {
        return new self($cents, strtoupper($currency), $precision);
    }

    /**
     * Zero money.
     */
    public static function zero(string $currency = 'USD', int $precision = 2): self
    {
        return new self(0, strtoupper($currency), $precision);
    }

    // ─── Arithmetic ───────────────────────────────────────────────────────────

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency, $this->precision);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency, $this->precision);
    }

    public function multiply(int|float $multiplier): self
    {
        $result = (int) bcmul((string) $this->amount, (string) $multiplier, 0);

        return new self($result, $this->currency, $this->precision);
    }

    public function divide(int|float $divisor): self
    {
        if ($divisor == 0) {
            throw new InvalidArgumentException('Cannot divide by zero.');
        }
        $result = (int) bcdiv((string) $this->amount, (string) $divisor, 0);

        return new self($result, $this->currency, $this->precision);
    }

    // ─── Comparison ───────────────────────────────────────────────────────────

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function greaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount > $other->amount;
    }

    public function lessThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount < $other->amount;
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    // ─── Output ───────────────────────────────────────────────────────────────

    /**
     * Return the decimal string representation for DB storage (decimal(20,6) compatible).
     */
    public function toDecimal(): string
    {
        $factor = (int) bcpow('10', (string) $this->precision);

        return bcdiv((string) $this->amount, (string) $factor, $this->precision);
    }

    /**
     * Return raw integer cents.
     */
    public function toCents(): int
    {
        return $this->amount;
    }

    /**
     * Return formatted string e.g. "$1,234.56".
     */
    public function format(string $locale = 'en_US'): string
    {
        $decimal = $this->toDecimal();

        return number_format((float) $decimal, $this->precision, '.', ',');
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function __toString(): string
    {
        return $this->toDecimal();
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Currency mismatch: {$this->currency} vs {$other->currency}."
            );
        }
    }
}
