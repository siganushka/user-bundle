<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\CreatableTrait;
use Siganushka\UserBundle\Repository\UserLoginRepository;

#[ORM\MappedSuperclass(repositoryClass: UserLoginRepository::class)]
abstract class AbstractUserLogin implements CreatableInterface
{
    use CreatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'logins')]
    protected ?AbstractUser $user = null;

    #[ORM\Column(nullable: true)]
    protected ?string $clientIp = null;

    #[ORM\Column(nullable: true)]
    protected ?string $userAgent = null;

    #[ORM\Column]
    protected ?string $authenticator = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?AbstractUser
    {
        return $this->user;
    }

    public function setUser(?AbstractUser $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    public function setClientIp(?string $clientIp): static
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getAuthenticator(): ?string
    {
        return $this->authenticator;
    }

    public function setAuthenticator(?string $authenticator): static
    {
        $this->authenticator = $authenticator;

        return $this;
    }
}
