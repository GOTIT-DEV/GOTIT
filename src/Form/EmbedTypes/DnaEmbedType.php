<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DnaEmbedType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'class' => 'App:Dna',
			'multiple' => false,
			'expanded' => false,
			'label' => false,
			'choice_label' => 'code',
			'query_builder' => function (EntityRepository $er) {
				return $er->createQueryBuilder('dna')->orderBy('dna.code', 'ASC');
			},
		]);
	}

	public function getParent() {
		return EntityType::class;
	}
}
