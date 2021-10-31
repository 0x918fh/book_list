<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController{
	/**
	 * @route("/", name="homepage")
	 */
	public function index(){
		return $this->render('main.html.twig');
	}
	
	/**
	 * @route("/info", name="info")
	 */
	public function info(){
		return $this->render('info.html.twig');
	}
}