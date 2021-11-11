<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Book;

class MainController extends AbstractController{
	/**
	 * @route("/", name="homepage")
	 */
	public function index(){
		$books = $this->getDoctrine()->getRepository(Book::class)->findAll();
		return $this->render('/book/book_list.html.twig', [
			'books' => $books,
		]);
	}
	
	/**
	 * @route("/info", name="info")
	 */
	public function info(){
		return $this->render('info.html.twig');
	}
}