<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController{
	/**
	 * @route("/", name="homepage")
	 */
	public function index(Request $request){
		$filter = $request->request->get('filter');
		$books = $this->getDoctrine()->getRepository(Book::class)->findAll($filter);
		return $this->render('/book/book_list.html.twig', [
			'books' => $books,
			'filter' => $filter,
		]);
	}
	
	/**
	 * @route("/info", name="info")
	 */
	public function info(){
		return $this->render('info.html.twig');
	}
}