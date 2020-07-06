<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace App\Services\Core;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\Voc;


/**
 * Service GenericFunctionE3s
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class GenericFunctionE3s
{
    private $entityManager;

    
    public function __construct(EntityManagerInterface $manager) {
        $this->entityManager = $manager ;
    }

    public function GetUserCreId($entity){
        $userCreId = ($entity->getUserCre() !== null) ? $entity->getUserCre() : 0;
        return $userCreId;
    }
    
    public function GetUserCreUsername($entity){
        $em = $this->entityManager;
        $userCreId = ($entity->getUserCre() !== null) ? $entity->getUserCre() : 0;
        $query = $em->createQuery('SELECT user.username FROM App:User user WHERE user.id = '.$userCreId.'')->getResult();
        $userCre = (count($query) > 0) ? $query[0]['username'] : 'NA';
        return $userCre;
    }
 
    public function GetUserMajUsername($entity){
        $em = $this->entityManager;
        $userMajId = ($entity->getUserMaj() !== null) ? $entity->getUserMaj() : 0;
        $query = $em->createQuery('SELECT user.username FROM App:User user WHERE user.id = '.$userMajId.'')->getResult();
        $userMaj = (count($query) > 0) ? $query[0]['username'] : 'NA';
        return $userMaj;
    }
    
    public function SetArrayCollection($nameArrayCollection, $entity){
        $method = 'get'.ucfirst($nameArrayCollection);
        // memorize ArrayCollection EstFinancePar
        $originalArrayCollection = new ArrayCollection();
        foreach ($entity->$method() as $entityCollection) {
        	$originalArrayCollection->add($entityCollection);
        }
        return $originalArrayCollection;
    }

    public function DelArrayCollection($nameArrayCollection, $entity, $originalArrayCollection ){
        $method = 'get'.ucfirst($nameArrayCollection);
        $em = $this->entityManager;  
            // delete ArrayCollections
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
        // memorize ArrayCollection EstFinancePar
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
        // delete ArrayCollectionsEmbed
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
        // delete ArrayCollections
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
