<?php
/**
 * (c) 2020
 * Author: Josh McCreight<jmccreight@shaw.ca>
 */

declare( strict_types = 1 );

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class RestController
 *
 * Though the controller lives in Symfony, which contains handlers for jwt
 * This controller will manually validate the tokens and not use the internal symfony user system
 *
 * @package App\Controller
 * @Route("/v1", options={"expose"=true})
 */
class RestController extends AbstractController
{
    /**
     * Register a new user account
     *
     * @Route("/register", name="api_register", methods={"POST"})
     * @param Request     $request
     * @param UserManager $manager
     *
     * @return JsonResponse
     */
    public function register( Request $request, UserManager $manager )
    {
        try {
            $data = $this->getJsonData();
            if ( empty( $data[ 'email' ] ) || empty( $data[ 'password' ] ) ) {
                return $this->json( [
                    'error' => 'Both `email` and `password` are required'
                ], 400 );
            }

            if ( empty( $user = $manager->getUser( $data[ 'email' ] ) ) ) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $user = $manager->createUser( $data[ 'email' ], $data[ 'password' ] );
            }

        } catch ( AuthenticationException $e ) {
            return $this->json( [
                'error' => $e->getMessage()
            ], 500 );
        }

        return $this->login( $request, $manager );
    }

    /**
     * @return array
     */
    private function getJsonData(): array
    {
        return json_decode( file_get_contents( 'php://input' ), TRUE ) ?? [];
    }

    /**
     * Attempt to login the user with the provided credentials
     *
     * @Route("/login", name="api_login", methods={"POST"})
     * @param Request     $request
     * @param UserManager $manager
     *
     * @return JsonResponse
     */
    public function login( Request $request, UserManager $manager )
    {
        $data = $this->getJsonData();
        try {
            $user = $manager->getUserWithCredentials( $data[ 'email' ], $data[ 'password' ] );
        } catch ( AuthenticationException $e ) {
            return $this->json( [ 'error' => $e->getMessage() ], 400 );
        }

        return $this->json( [
            'links' => $this->generateLinks( $request ),
            'data'  => [
                'email' => $user->getEmail(),
                'token' => (string) $manager->generateTokenForUser( $request, $user )
            ]
        ], 200 );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function generateLinks( Request $request )
    {
        return [
            'self'    => $this->generateUrl( $request->attributes->get( '_route' ), [], UrlGeneratorInterface::ABSOLUTE_URL ),
            'current' => $this->generateUrl( 'api_current', [], UrlGeneratorInterface::ABSOLUTE_URL ),
            'next'    => $this->generateUrl( 'api_next', [], UrlGeneratorInterface::ABSOLUTE_URL ),
            'login'   => $this->generateUrl( 'api_login', [], UrlGeneratorInterface::ABSOLUTE_URL )
        ];
    }

    /**
     * @Route("/current", name="api_current", methods={"GET", "PUT"})
     * @param Request     $request
     * @param UserManager $manager
     *
     * @return JsonResponse
     */
    public function current( Request $request, UserManager $manager )
    {
        if ( FALSE === ( $user = $this->validateAuthorization( $request, $manager ) ) ) {
            return $this->json( [ 'error' => 'Unauthorized' ], 403 );
        }

        if ( $request->isMethod( 'PUT' ) ) {
            $data = $this->getJsonData();
            /** @var User $user */
            $manager->resetUserIdx( $user, $data[ 'current' ] ?? 0 );
        }

        return $this->json( [
            'links' => $this->generateLinks( $request ),
            'data'  => $user->getIdx()
        ], 200 );
    }

    /**
     * @param Request     $request
     *
     * @param UserManager $manager
     *
     * @return bool
     */
    private function validateAuthorization( Request $request, UserManager $manager )
    {
        try {
            $authorization = $request->headers->get( 'authorization', NULL );
            $userToken     = substr( (string) $authorization, 7 );

            if ( !empty( $userToken ) ) {
                return $manager->isUserAuthenticated( $userToken );
            }
        } catch ( Exception $e ) {
            // any exceptions, errors in token ..etc are considered false!
            return FALSE;
        }

        return FALSE;
    }

    /**
     * @Route("/next", name="api_next", methods={"GET"})
     * @param Request     $request
     * @param UserManager $manager
     *
     * @return JsonResponse
     */
    public function next( Request $request, UserManager $manager )
    {
        if ( FALSE === ( $user = $this->validateAuthorization( $request, $manager ) ) ) {
            return $this->json( [ 'error' => 'Unauthorized' ], 403 );
        }

        /** @var User $user */
        $user = $manager->incrementIdx( $user );

        return $this->json( [
            'links' => $this->generateLinks( $request ),
            'data'  => $user->getIdx()
        ], 200 );
    }
}
