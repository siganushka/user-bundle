<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\CreatableTrait;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;

#[ORM\Entity]
class UserLogin implements ResourceInterface, CreatableInterface
{
    use ResourceTrait;
    use CreatableTrait;

    #[ORM\ManyToOne(inversedBy: 'logins')]
    protected ?User $user = null;

    #[ORM\Column(nullable: true)]
    protected ?string $clientIp = null;

    #[ORM\Column(nullable: true)]
    protected ?string $userAgent = null;

    #[ORM\Column]
    protected ?string $authenticator = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    public function setClientIp(?string $clientIp): self
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getAuthenticator(): ?string
    {
        return $this->authenticator;
    }

    public function setAuthenticator(?string $authenticator): self
    {
        $this->authenticator = $authenticator;

        return $this;
    }
}
