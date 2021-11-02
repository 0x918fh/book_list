<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Author;
use App\Form\AuthorEditType;

class AuthorController extends AbstractController{
	/**
	 * @route("/author_edit/{id}", name="author_edit")
	 */
	public function edit(Request $request, $id = -1){
		$author = $this->getDoctrine()->getRepository(Author::class)->find($id);
		if(!$author){
			$author = new Author();
			$author->setBookCount(0);
		}
		
		$form = $this->createForm(AuthorEditType::class, $author);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()){
			$author = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($author);
			$entityManager->flush();
			
			return $this->redirectToRoute('authors');
		}
		
		return $this->render('author/author_edit.html.twig', [
			'form' => $form->createView(),
		]);
	}
	
	/**
	 * @Route("/authors", name="authors")
	 */
	public function authors(){
		$authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
		
		return $this->render('author/author_list.html.twig', [
			'authors' => $authors,
		]);
	}
}