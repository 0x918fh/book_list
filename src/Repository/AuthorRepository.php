<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }
	
	public function getCount(){
		$count = $this->createQueryBuilder('a')
				->select('count(a.id)')
				->getQuery()
				->getSingleScalarResult();
		
		return $count;
	}
	
	public function countUpdate($ids = []){
		$authors = $this->findBy(['id' => $ids]);
		$test = [];
		foreach($authors as $author){
			$count = count($author->getBooks());
			$test[] = $this->createQueryBuilder('a')
					->update()
					->set('a.bookCount', $count)
					->where('a.id = '.$author->getId())
					->getQuery()
					->execute();
//			$test[] = $this->createQueryBuilder('a')
//					->update()
//					->set('a.book_count' , ':count')
//					->setParameter('count', 1)
//					->where('a.id', ':id')
//					->setParameter('id', $author->getId())
//					->getQuery();
		}
		return $test;
	}
	
	public function findByLetter($letter, $array = false){
		$authors = $this->createQueryBuilder('a')
            ->andWhere('a.fam LIKE :val')
            ->setParameter('val', $letter.'%')
            //->orderBy(['fam' => 'ASC', 'nam' => 'ASC', 'ots' => 'ASC'])
			->addOrderBy('a.fam', 'ASC')
			->addOrderBy('a.nam', 'ASC')
			->addOrderBy('a.ots', 'ASC')
            ->getQuery()
            ->getResult();
		
		if($array){
			$res = [];
			foreach($authors as $item){
				$res[] = [
					'id' => $item->getId(),
					'fio' => $item->getFio(),
				];
			}
			return $res;
		}
		else{
			return $authors;
		}
	}
	
	public function findAll(){
		return $this->findBy(array(), array('fam' => 'ASC', 'nam' => 'ASC', 'ots' => 'ASC'));
	}

    // /**
    //  * @return Author[] Returns an array of Author objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
