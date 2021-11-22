<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Book;
use App\Entity\Author;
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
	
	/**
	 * @route("/generator", name="generator")
	 */
	public function generator(){
		$authorCount = $this->getDoctrine()->getRepository(Author::class)->getCount();
		$addAuthor = false;
		$countMap = [1, 1, 1, 2, 2, 3];
		
		$backColors = [
			[247, 159, 31],
			[238, 90, 36],
			[234, 32, 39],
			[196, 229, 56],
			[163, 203, 56],
			[0, 148, 50],
			[18, 203, 196],
			[18, 137, 167],
			[6, 82, 221],
			[27, 20, 100],
			[253, 167, 223],
			[217, 128, 250],
			[153, 128, 250],
			[87, 88, 187],
			[237, 76, 103],
			[181, 52, 113],
			[131, 52, 113],
			[111, 30, 81],
		];
		
		$nouns = file($this->getParameter('kernel.project_dir').'/public_html/gf/noun.txt');
		$ajectives = file($this->getParameter('kernel.project_dir').'/public_html/gf/ajective.txt');
		
		$entityManager = $this->getDoctrine()->getManager();
		
		if($authorCount < 100){
			$addAuthor = true;
			$authors = file($this->getParameter('kernel.project_dir').'/public_html/gf/names.txt');
			foreach($authors as $item){
				$author = new Author();
				$words = explode(' ', trim($item));
				$author->setFam($words[0]);
				$author->setNam($words[1]);
				$author->setOts($words[2]);
				$author->setBookCount(0);

				$entityManager->persist($author);
			}
			$entityManager->flush();
		}
		
		$allAuthors = $this->getDoctrine()->getRepository(Author::class)->findAll();

		for($i = 0; $i < 50; $i++){
			$bookAuthCount = $countMap[random_int(0, 5)];
			$book = new Book();
			$fioList = [];
			$authIds = [];
			for($a = 0; $a < $bookAuthCount; $a++){
				$authorIndex = random_int(0, count($allAuthors) - 1);
				$book->addAuthor($allAuthors[$authorIndex]);
				$fioList[] = $allAuthors[$authorIndex]->getFam().' '.$allAuthors[$authorIndex]->getNam().' '.$allAuthors[$authorIndex]->getOts();
				$authIds[] = $allAuthors[$authorIndex]->getId();
			}
			$book->setTitle($ajectives[random_int(0, count($ajectives) - 1)].' '.$nouns[random_int(0, count($nouns) - 1)]);
			$book->setAuthor(implode('; ', $fioList));
			$book->setDescription('Без описания');
			$book->setYear(random_int(1990, 2021));
			$book->setCover('cover.png');
			
			$entityManager->persist($book);
			$entityManager->flush();
			
			$this->getDoctrine()->getRepository(Author::class)->countUpdate($authIds);
			
			$this->_genCover($book->getId(), $backColors[random_int(0, count($backColors) - 1)]);
		}
		
		return $this->render('generator.html.twig', [
			'addAuthor' => $addAuthor,
		]);
	}
	
	protected function _genCover($id, $back){
		$width = 210;
		$height = 294;
		$img = imagecreatetruecolor($width, $height);
		imageantialias($img, true);
		$backColor = imagecolorallocate($img, $back[0], $back[1] ,$back[2]);
		imagefill($img, 0, 0, $backColor);
		
		$frontColor = imagecolorallocate($img, 255, 255, 255);
		$labelColor = imagecolorallocate($img, 255, 195, 18);
		$fontColor = imagecolorallocate($img, 44, 62, 80);
		
		imagesetthickness($img, 2);
		
		for($i = 0; $i <= random_int(7, 10); $i++){
			$orientation = random_int(0, 1);
			switch($orientation){
				case 0:
					imageline ($img, random_int(-20, 230), random_int(-20, 0), random_int(-20, 230), random_int(294, 314), $frontColor);
					break;
				case 1:
					imageline ($img, random_int(-20, 0), random_int(-20, 314), random_int(210, 230), random_int(-20, 314), $frontColor);
					break;
			}
		}
		
		imagefilledrectangle($img, 20, 40, 190, 100, $labelColor);
		
		imagettftext($img, 40, 0, 48, 90, $fontColor, $this->getParameter('kernel.project_dir').'/public_html/gf/font.ttf', 'BOOK');
		
		mkdir($this->getParameter('kernel.project_dir').'/public_html/books/'.$id);
		imagepng($img, $this->getParameter('kernel.project_dir').'/public_html/books/'.$id.'/cover.png');
	}
}