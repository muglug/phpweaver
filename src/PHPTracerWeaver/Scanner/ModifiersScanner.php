<?php namespace PHPTracerWeaver\Scanner;

/** Tracks possible preludes for functions */
class ModifiersScanner implements ScannerInterface
{
    /** @var ?callable */
    protected $onModifiersBegin;
    /** @var ?callable */
    protected $onModifiersEnd;
    /** @var bool */
    protected $wasFunction = false;
    /** @var int */
    protected $state = 0;

    /**
     * @param ?callable $callback
     *
     * @return void
     */
    public function notifyOnModifiersBegin(?callable $callback): void
    {
        $this->onModifiersBegin = $callback;
    }

    /**
     * @param ?callable $callback
     *
     * @return void
     */
    public function notifyOnModifiersEnd(?callable $callback): void
    {
        $this->onModifiersEnd = $callback;
    }

    /**
     * @param Token $token
     *
     * @return void
     */
    public function accept(Token $token): void
    {
        if ($this->isModifyer($token)) {
            $this->state = 1;
            if (is_callable($this->onModifiersBegin)) {
                call_user_func($this->onModifiersBegin);
            }

            return;
        }

        if ($this->isModifyable($token)) {
            $this->wasFunction = $token->isA(T_FUNCTION);
            $this->state = 0;
            if (is_callable($this->onModifiersEnd)) {
                call_user_func($this->onModifiersEnd);
            }

            return;
        }
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    private function isModifyable(Token $token): bool
    {
        return $token->isA(T_INTERFACE)
            || $token->isA(T_CLASS)
            || $token->isA(T_FUNCTION)
            || $token->isA(T_VARIABLE);
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    private function isModifyer(Token $token): bool
    {
        return $token->isA(T_PRIVATE)
            || $token->isA(T_PROTECTED)
            || $token->isA(T_PUBLIC)
            || $token->isA(T_FINAL)
            || $token->isA(T_STATIC)
            || $token->isA(T_ABSTRACT);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return 1 === $this->state;
    }

    /**
     * @return bool
     */
    public function wasFunction(): bool
    {
        return $this->wasFunction;
    }
}
