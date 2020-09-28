<?php
/**
 * (c) 2020
 * Author: Josh McCreight<jmccreight@shaw.ca>
 */

declare( strict_types = 1 );

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idx;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * Get Idx
     *
     * @return int
     */
    public function getIdx(): int
    {
        return $this->idx ?? 0;
    }

    /**
     * Set Idx
     *
     * @param int $idx
     *
     * @return User
     * @throws InvalidArgumentException
     */
    public function setIdx( int $idx ): User
    {
        if ( $idx < 0 ) {
            throw new InvalidArgumentException( 'Idx cannot be negative' );
        }

        $this->idx = $idx;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'       => $this->getId(),
            'email'    => $this->getEmail(),
            'password' => $this->getPassword(),
            'token'    => $this->getToken()
        ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail( string $email ): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword( string $password ): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get Token
     *
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set Token
     *
     * @param string|null $token
     *
     * @return User
     */
    public function setToken( string $token = NULL ): User
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles   = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique( $roles );
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles( array $roles ): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @return array
     */
    public function toPublicArray(): array
    {
        return [
            'id'    => $this->getId(),
            'email' => $this->getEmail(),
            'token' => $this->getToken()
        ];
    }
}
