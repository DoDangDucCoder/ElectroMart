<?php

namespace App\Domain\Shared\ValueObjects;

class Money
{
    public function __construct(
        private readonly float $amount,
        private readonly string $currency = 'VND'
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Money amount cannot be negative');
        }
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): Money
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): Money
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $multiplier): Money
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function divide(float $divisor): Money
    {
        if ($divisor === 0.0) {
            throw new \InvalidArgumentException('Cannot divide by zero');
        }
        return new self($this->amount / $divisor, $this->currency);
    }

    public function isGreaterThan(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount > $other->amount;
    }

    public function isLessThan(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount < $other->amount;
    }

    public function equals(Money $other): bool
    {
        return $this->currency === $other->currency && $this->amount === $other->amount;
    }

    public function isZero(): bool
    {
        return $this->amount === 0.0;
    }

    public function format(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' ' . $this->currency;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            currency: $data['currency'] ?? 'VND'
        );
    }

    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot perform operation on different currencies');
        }
    }
}
