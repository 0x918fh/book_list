<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookEditType;

class BookController extends AbstractController{
	/**
	 * @route("/inline_form/{id}", name="inline_form")
	 */
	public function inline_form($id = -1){
		$book = $this->getDoctrine()->getRepository(Book::class)->find($id);
		if(!$book){
			return $this->json([
				'status' => false,
				'message' => 'Не найдена указанная книга, попробуйте обновить страницу',
			]);
		}
		return $this->json([
			'status' => true,
			'html' => $this->render('/book/book_inline.html.twig', ['book' => $book])->getContent(),
		]);
	}
	
	/**
	 * @route("/book_edit/{id}", name="book_edit")
	 */
	public function edit(Request $request, $id = -1){
		$book = $this->getDoctrine()->getRepository(Book::class)->find($id);
		$oldCover = null;
		$fileCover = '/books/nocover.png';
		if(!$book){
			$book = new Book();
			$book->setAuthor('');
		}
		else{
			$oldCover = $book->getCover();
			if($oldCover !== null){
				$fileCover = '/books/'.$book->getId().'/'.$oldCover;
			}
		}
		
		$form = $this->createForm(BookEditType::class, $book);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()){
			$book = $form->getData();
			$cover = $form->get('cover')->getData();
			if($cover){
				$coverName = 'cover.'.$cover->guessExtension();
				$book->setCover($coverName);
			}
			$changeAuthors = [];
			if($id > 0){
				foreach($book->getAuthors() as $item){
					$book->removeAuthor($item);
					$changeAuthors[] = $item->getId();
				}
			}
			$authors = json_decode($book->getAuthor(), true);
			$authorsFio = [];
			if($authors === null){
				$authors = [];
			}
			$authList = $this->getdoctrine()->getRepository(Author::class)->findBy(['id' => $authors]);
			foreach($authList as $item){
				$book->addAuthor($item);
				$changeAuthors[] = $item->getId();
				$authorsFio[] = trim($item->getFam().' '.$item->getNam().' '.$item->getOts());
			}
			$book->setAuthor(implode('; ', $authorsFio));
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($book);
			$entityManager->flush();
			
			$this->getDoctrine()->getRepository(Author::class)->countUpdate($changeAuthors);
			
			if($cover){
				if($oldCover !== null){
					unlink($this->getParameter('kernel.project_dir').'/public_html/books/'.$book->getId().'/'.$oldCover);
				}
				$cover->move($this->getParameter('kernel.project_dir').'/public_html/books/'.$book->getId(), $coverName);
			}
			
			return $this->redirectToRoute('homepage');
		}
		
//		$letterList = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 
//			'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 
//			'Ш', 'Щ', 'Э', 'Ю', 'Я'];
		
		return $this->render('book/book_edit.html.twig', [
			'form' => $form->createView(),
			'fileCover' => $fileCover,
//			'letterList' => $letterList,
			'authors' => $book->getAuthors(),
		]);
	}
	
	/**
	 * @route("/book_test", name="book_test")
	 */
	public function test(){
		$book = new Book();
		$authors = [1, 3, 5];
		$authList = $this->getdoctrine()->getRepository(Author::class)->findBy(['id' => $authors]);
		foreach($authList as $author){
			$book->addAuthor($author);
		}
		$book->setAuthor('[]');
		$book->setTitle('test book');
		$book->setYear(2021);
		$book->setDescription('Описание книги');
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($book);
		$entityManager->flush();
		
		return $this->redirectToRoute('homepage');
	}
	
	/**
	 * @route("/book_inline_save", name="book_inline_test")
	 */
	public function inlineSave(Request $request){
		$formBook = $request->request->get('book');
		$book = $this->getDoctrine()->getRepository(Book::class)->find($formBook['id']);
		
		if(!$book){
			return $this->json([
				'status' => false,
				'message' => 'Не найдена книга ['.$formBook['id'].']',
			]);
		}
		
		$changeAuthors = [];
		
		foreach($book->getAuthors() as $item){
			$book->removeAuthor($item);
			$changeAuthors[] = $item->getId();
		}
		$authors = json_decode($formBook['authors'], true);
		if($authors === null){
			$authors = [];
		}
		$authList = $this->getdoctrine()->getRepository(Author::class)->findBy(['id' => $authors]);
		foreach($authList as $item){
			$book->addAuthor($item);
			$changeAuthors[] = $item->getId();
		}
		$changeAuthors = array_unique($changeAuthors);
		
		$book->setTitle($formBook['title']);
		$book->setYear($formBook['year']);
		$book->setDescription($formBook['description']);
		$book->setAuthor($formBook['author']);
		
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($book);
		$entityManager->flush();
		
		$this->getDoctrine()->getRepository(Author::class)->countUpdate($changeAuthors);
		
		return $this->json([
			'status' => true,
			'bookCard' => $this->render('/book/book_card.html.twig', ['book' => $book])->getContent(),
			'authors' => $authors,
		]);
	}
	
	/**
	 * @route("/update-test", name="update_test")
	 */
	public function updateTest(){
		$count = $this->getDoctrine()->getRepository(Author::class)->countUpdate([1, 3, 5]);
		
		return $this->json([
			'count' => $count,
		]);
	}
}