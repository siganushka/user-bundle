<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\EnableInterface;
use Siganushka\Contracts\Doctrine\EnableTrait;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(columns: ['identifier'])]
class User implements ResourceInterface, EnableInterface, TimestampableInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use EnableTrait;
    use ResourceTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: UserRole::class, inversedBy: 'users')]
    private ?UserRole $role = null;

    #[ORM\Column]
    protected ?string $identifier = null;

    #[ORM\Column]
    protected ?string $password = null;

    protected ?string $plainPassword = null;

    public function getRole(): ?UserRole
    {
        return $this->role;
    }

    public function setRole(?UserRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        // [important] Force triggering of Events::preUpdate
        if ($plainPassword) {
            $this->password = null;
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->role?->getPermissions() ?? [];
        $roles[] = 'ROLE_USER';

        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier ?? throw new \InvalidArgumentException('The identifier for user cannot be empty.');
    }
}
