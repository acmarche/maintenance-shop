<?php

namespace AcMarche\MaintenanceShop\Entity;

use AcMarche\MaintenanceShop\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $username;
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;
    #[ORM\Column(type: 'string', length: 180, nullable: false)]
    private ?string $nom;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $prenom;
    #[ORM\Column(type: 'json')]
    private array $roles = [];
    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private string $password;
    /**
     * Plain password. Used for model validation. Must not be persisted.
     */
    protected ?string $plainPassword;

    public function __construct()
    {
    }

    public function __toString(): string
    {
        return (string)$this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function addRole(string $role): void
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (\in_array($role, $this->roles, true)) {
            $index = array_search($role, $this->roles);
            unset($this->roles[$index]);
        }
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->getRoles(), true);
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
