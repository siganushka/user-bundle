<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column]
    protected ?string $identifier = null;

    #[ORM\Column]
    protected ?string $password = null;

    /**
     * @var Collection<int, UserLogin>
     */
    #[ORM\OneToMany(targetEntity: UserLogin::class, mappedBy: 'user', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC', 'id' => 'DESC'])]
    protected Collection $logins;

    public function __construct()
    {
        $this->logins = new ArrayCollection();
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

    /**
     * @return Collection<int, UserLogin>
     */
    public function getLogins(): Collection
    {
        return $this->logins;
    }

    public function addLogin(UserLogin $login): static
    {
        if (!$this->logins->contains($login)) {
            $this->logins->add($login);
            $login->setUser($this);
        }

        return $this;
    }

    public function removeLogin(UserLogin $login): static
    {
        if ($this->logins->removeElement($login)) {
            if ($login->getUser() === $this) {
                $login->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        \assert(!empty($this->identifier));

        return $this->identifier;
    }
}
