<?php

namespace Bbees\E3sBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Bbees\E3sBundle\Entity\Voc;


/**
 * Service GenericFunctionE3s
 */
class GenericFunctionE3s
{
    private $entityManager;

    
    public function __construct(EntityManager $manager) {
        $this->entityManager = $manager ;
    }

    public function GetUserCreId($entity){
        $userCreId = ($entity->getUserCre() !== null) ? $entity->getUserCre() : 0;
        return $userCreId;
    }
    
    public function GetUserCreUsername($entity){
        $em = $this->entityManager;
        $userCreId = ($entity->getUserCre() !== null) ? $entity->getUserCre() : 0;
        $query = $em->createQuery('SELECT user.username FROM BbeesUserBundle:User user WHERE user.id = '.$userCreId.'')->getResult();
        $userCre = (count($query) > 0) ? $query[0]['username'] : 'NA';
        return $userCre;
    }
 
    public function GetUserMajUsername($entity){
        $em = $this->entityManager;
        $userMajId = ($entity->getUserMaj() !== null) ? $entity->getUserMaj() : 0;
        $query = $em->createQuery('SELECT user.username FROM BbeesUserBundle:User user WHERE user.id = '.$userMajId.'')->getResult();
        $userMaj = (count($query) > 0) ? $query[0]['username'] : 'NA';
        return $userMaj;
    }
    
    public function SetArrayCollection($nameArrayCollection, $entity){
        $method = 'get'.ucfirst($nameArrayCollection);
        // memorisation des ArrayCollection EstFinancePar
        $originalArrayCollection = new ArrayCollection();
        foreach ($entity->$method() as $entityCollection) {
        	$originalArrayCollection->add($entityCollection);
        }
        return $originalArrayCollection;
    }

    public function DelArrayCollection($nameArrayCollection, $entity, $originalArrayCollection ){
        $method = 'get'.ucfirst($nameArrayCollection);
        $em = $this->entityManager;  
            // suppression des ArrayCollections
            foreach ($entity->$method() as $entityCollection) {
                foreach ($originalArrayCollection as $key => $toDel) {
                    if ($toDel === $entityCollection) {
                        unset($originalArrayCollection[$key]);
                    }
                }
            }
            // remove the relationship 
            foreach ($originalArrayCollection as $entityCollection) {
                 $em->remove($entityCollection);
            }
        return true;
    }

    public function SetArrayCollectionEmbed($nameArrayCollection, $nameArrayCollectionEmbed, $entity){
        $method = 'get'.ucfirst($nameArrayCollection);
        $methodEmbed = 'get'.ucfirst($nameArrayCollectionEmbed);
        $listOriginalArrayCollection = [];
        // memorisation des ArrayCollection EstFinancePar
        $originalArrayCollection = new ArrayCollection();
        foreach ($entity->$method() as $entityCollection) {
        	$originalArrayCollection->add($entityCollection);
        }
        $listOriginalArrayCollection[$nameArrayCollection] = $originalArrayCollection;
        // 
        $originalArrayCollectionEmbed = new ArrayCollection();
        foreach ($entity->$method() as $entityCollection) {
	        foreach ($entityCollection->$methodEmbed() as $entityCollectionEmbed) {
	        	$originalArrayCollectionEmbed->add($entityCollectionEmbed);	        	
	        }
        }
        $listOriginalArrayCollection[$nameArrayCollectionEmbed] = $originalArrayCollectionEmbed;
        return $listOriginalArrayCollection;
    }

    public function DelArrayCollectionEmbed($nameArrayCollection, $nameArrayCollectionEmbed, $entity, $listOriginalArrayCollection ){
        // 
        $method = 'get'.ucfirst($nameArrayCollection);
        $methodEmbed = 'get'.ucfirst($nameArrayCollectionEmbed);
        $originalArrayCollection = $listOriginalArrayCollection[$nameArrayCollection];
        $originalArrayCollectionEmbed = $listOriginalArrayCollection[$nameArrayCollectionEmbed];
        $em = $this->entityManager;         
        // suppression des ArrayCollectionsEmbed
        foreach ($entity->$method() as $entityCollection) {
            foreach ($entityCollection->$methodEmbed() as $entityCollectionEmbed) {
                foreach ($originalArrayCollectionEmbed as $key => $toDel) {
                    if ($toDel === $entityCollectionEmbed) {
                        unset($originalArrayCollectionEmbed[$key]);
                    }
                }
            }    
        }
        foreach ($originalArrayCollectionEmbed as $entityCollectionEmbed) {
             $em->remove($entityCollectionEmbed);
        }
        // on supprime dans un deuxième temps  les ArrayCollections
        foreach ($entity->$method() as $entityCollection) {
            foreach ($originalArrayCollection as $key => $toDel) {
                if ($toDel === $entityCollection) {
                    unset($originalArrayCollection[$key]);
                }
            }
        }
        foreach ($originalArrayCollection as $entityCollection) {
             $em->remove($entityCollection);
        }        
            
        return true;
    }
    
}
