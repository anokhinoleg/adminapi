<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 19.03.18
 * Time: 16:15
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->redirectToRoute('sonata_user_admin_security_login');
    }
}