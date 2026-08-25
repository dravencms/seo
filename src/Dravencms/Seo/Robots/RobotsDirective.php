<?php declare(strict_types = 1);

namespace Dravencms\Seo\Robots;

final class RobotsDirective
{
    /** @param array<string, mixed> $parameters */
    private function __construct(
        private string $action,
        private ?string $path,
        private ?string $destination,
        private array $parameters,
        private string $userAgent
    ) {
    }

    public static function forPath(string $action, string $path, string $userAgent = '*'): self
    {
        return new self($action, $path, null, [], $userAgent);
    }

    /** @param array<string, mixed> $parameters */
    public static function forDestination(
        string $action,
        string $destination,
        array $parameters = [],
        string $userAgent = '*'
    ): self {
        return new self($action, null, $destination, $parameters, $userAgent);
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    /** @return array<string, mixed> */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
