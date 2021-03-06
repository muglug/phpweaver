<?php namespace PHPTracerWeaver\Signature;

use PHPTracerWeaver\Reflector\ClassCollatorInterface;

class FunctionSignature
{
    /** @var FunctionArgument[] */
    protected $arguments = [];
    /** @var FunctionArgument */
    protected $returnType;
    /** @var ClassCollatorInterface */
    protected $collator;

    /**
     * @param ClassCollatorInterface $collator
     */
    public function __construct(ClassCollatorInterface $collator)
    {
        $this->collator = $collator;
        $this->returnType = new FunctionArgument(0);
    }

    /**
     * @param string[] $arguments
     * @param string   $returnType
     *
     * @return void
     */
    public function blend(array $arguments, string $returnType): void
    {
        foreach ($arguments as $id => $type) {
            $arg = $this->getArgumentById($id);
            $arg->collateWith($type);
            if (!$arg->getName()) {
                $arg->setName($id);
            }
        }

        if ($returnType) {
            $this->returnType->collateWith($returnType);
        }
    }

    /**
     * @return string
     */
    public function getReturnType(): string
    {
        return $this->returnType->getType();
    }

    /**
     * @param int $id
     *
     * @return FunctionArgument
     */
    public function getArgumentById(int $id): FunctionArgument
    {
        if (!isset($this->arguments[$id])) {
            $this->arguments[$id] = new FunctionArgument($id);
        }

        return $this->arguments[$id];
    }

    /**
     * @param string $name
     *
     * @return ?FunctionArgument
     */
    public function getArgumentByName(string $name): ?FunctionArgument
    {
        foreach ($this->arguments as $argument) {
            if ($argument->getName() === $name) {
                return $argument;
            }
        }

        return null;
    }

    /**
     * @return FunctionArgument[]
     */
    public function getArguments(): array
    {
        $args = $this->arguments;
        ksort($args);

        return $args;
    }

    /**
     * @return string[]
     */
    public function export(): array
    {
        $out = [];
        foreach ($this->arguments as $argument) {
            $out[] = $argument->export();
        }

        return $out;
    }
}
