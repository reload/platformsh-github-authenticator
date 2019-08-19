<?php

declare(strict_types=1);

namespace App\GitHub;

class Status
{

    /* @var string */
    private $state;

    /* @var string|null */
    private $targetUrl;

    /* @var string|null */
    private $description;

    /* @var string|null */
    private $context;

    public function __construct(string $state)
    {
        $this->state = $state;
    }

    public function withState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function withTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;
        return $this;
    }

    public function withDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function withContext(string $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function toParams(): array
    {
        $params = [
            'state' => $this->state,
        ];

        if ($this->targetUrl) {
            $params['target_url'] = $this->targetUrl;
        }
        if ($this->description) {
            $params['description'] = $this->description;
        }
        if ($this->context) {
            $params['context'] = $this->context;
        }

        return $params;
    }
}
