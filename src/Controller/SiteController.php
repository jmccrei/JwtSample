<?php
/**
 * (c) 2020
 * Author: Josh McCreight<jmccreight@shaw.ca>
 */

declare( strict_types = 1 );

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 *
 * @package App\Controller
 */
class SiteController extends AbstractController
{
    /**
     * @Route("/", name="site_index")
     * @Template()
     */
    public function index()
    {
        return [];
    }
}
