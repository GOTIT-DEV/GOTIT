<?php

namespace App\Controller\API;

use App\DTO\CsvRecordsRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @param CsvRecordsRequest $data
 */
class ImportCSVAction extends AbstractController {
	public function __invoke(ArrayCollection $data): ArrayCollection {
		$em = $this->getDoctrine()->getManager();
		foreach ($data as $entity) {
			$em->persist($entity);
		}
		$em->flush();

		return $data;
	}
}
