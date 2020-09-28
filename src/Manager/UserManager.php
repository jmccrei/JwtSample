<?php
/**
 * (c) 2020
 * Author: Josh McCreight<jmccreight@shaw.ca>
 */

declare( strict_types = 1 );

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class UserManager
 *
 * @package App\Manager
 */
class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct( EntityManagerInterface $entityManager,
                                 UserPasswordEncoderInterface $userPasswordEncoder )
    {
        $this->entityManager   = $entityManager;
        $this->passwordEncoder = $userPasswordEncoder;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return User|null
     */
    public function getUserWithCredentials( string $email, string $password ): ?User
    {
        $user = $this->getUser( $email );
        if ( empty( $user ) ) {
            return $this->createUser( $email, $password );
        }

        if ( !$this->passwordEncoder->isPasswordValid( $user, $password ) ) {
            throw new AuthenticationException( 'Invalid credentials' );
        }

        return $user;
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function getUser( string $email ): ?User
    {
        return $this->entityManager->getRepository( User::class )
            ->findOneBy( [ 'email' => $email ] );
    }

    /**
     * @param string $email
     * @param string $plainPassword
     *
     * @return User
     */
    public function createUser( string $email, string $plainPassword ): User
    {
        $user = ( new User() )->setEmail( $email )->setIdx( 0 );
        $user->setPassword( $this->passwordEncoder->encodePassword( $user, $plainPassword ) );

        $this->entityManager->persist( $user );
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return Token
     */
    public function generateTokenForUser( Request $request, User $user ): Token
    {
        // get the token
        return ( new Builder() )
            ->identifiedBy( $request->server->get( 'APP_SECRET' ), TRUE )
            ->issuedAt( $time = time() )
            ->canOnlyBeUsedAfter( $time + 60 )
            ->withClaim( 'email', $user->getEmail() )
            ->withClaim( 'uid', $user->getId() )
            ->withClaim( 'password', $user->getPassword() )
            ->getToken();
    }

    /**
     * @param string $token
     *
     * @return false
     */
    public function isUserAuthenticated( string $token )
    {
        $token = ( new Parser() )->parse( ( string ) $token );
        $token->getHeaders();
        $token->getClaims();

        $uid      = $token->getClaim( 'uid' );
        $email    = $token->getClaim( 'email' );
        $password = $token->getClaim( 'password' );

        $user = $this->entityManager->getRepository( User::class )->find( $uid );
        if ( empty( $user ) ) {
            return FALSE;
        }

        if ( $user->getPassword() !== $password || $user->getEmail() !== $email ) {
            return FALSE;
        }

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function incrementIdx( User $user ): User
    {
        $user->setIdx( (int) $user->getIdx() + 1 );

        $this->entityManager->persist( $user );
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param User $user
     * @param int  $current
     *
     * @return User
     */
    public function resetUserIdx( User $user, int $current = 0 ): User
    {
        $user->setIdx( $current );
        $this->entityManager->persist( $user );
        $this->entityManager->flush();

        return $user;
    }
}