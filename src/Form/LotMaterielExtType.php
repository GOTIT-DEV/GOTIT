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

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\Enums\Action;
use App\Form\EmbedTypes\LotMaterielExtEstReferenceDansEmbedType;
use App\Form\EmbedTypes\LotMaterielExtEstRealiseParEmbedType;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;
use App\Form\ActionFormType;

class LotMaterielExtType extends ActionFormType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$sampling = $builder->getData()->getCollecteFk();

		$builder->add('collecteTypeahead', null, [
			'mapped' => false,
			'attr' => [
				'class' => 'typeahead typeahead-collecte',
				'data-target_id' => "bbees_e3sbundle_lotmaterielext_collecteId",
				'name' => "where",
				'placeholder' => "Collecte typeahead placeholder",
				"maxlength" => "255",
				'readonly' => ($options['action_type'] == Action::create() && $sampling != null)
			],
			'required' => true,
			'disabled' => $this->canEditAdminOnly($options)
		])
			->add('collecteId', HiddenType::class, array(
				'mapped' => false,
				'required' => true,
			))
			->add('codeLotMaterielExt', null, [
				'disabled' => $this->canEditAdminOnly($options),
				"attr" => [
					'readonly' => ($options['action_type'] == Action::create())
				]
			])
			->add('pigmentationVocFk', EntityType::class, array(
				'class' => 'App:Voc',
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('voc')
						->where('voc.parent LIKE :parent')
						->setParameter('parent', 'pigmentation')
						->orderBy('voc.libelle', 'ASC');
				},
				'choice_translation_domain' => true,
				'choice_label' => 'libelle',
				'multiple' => false,
				'expanded' => false,
				'placeholder' => 'Choose a Pigmentation'
			))
			->add('yeuxVocFk', EntityType::class, array(
				'class' => 'App:Voc',
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('voc')
						->where('voc.parent LIKE :parent')
						->setParameter('parent', 'yeux')
						->orderBy('voc.libelle', 'ASC');
				},
				'choice_translation_domain' => true,
				'choice_label' => 'libelle',
				'multiple' => false,
				'expanded' => false,
				'placeholder' => 'Choose a Eye'
			))
			->add('commentaireLotMaterielExt')
			->add('nbIndividusVocFk', EntityType::class, array(
				'class' => 'App:Voc',
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('voc')
						->where('voc.parent LIKE :parent')
						->setParameter('parent', 'nbIndividus')
						->orderBy('voc.libelle', 'ASC');
				},
				'choice_translation_domain' => true,
				'choice_label' => 'libelle',
				'multiple' => false,
				'expanded' => false,
				'placeholder' => 'Choose an option'
			))
			->add('commentaireNbIndividus')
			->add('datePrecisionVocFk', DatePrecisionType::class)
			->add('dateCreationLotMaterielExt', DateFormattedType::class)
			->add('lotMaterielExtEstRealisePars', CollectionType::class, array(
				'entry_type' => LotMaterielExtEstRealiseParEmbedType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'prototype' => true,
				'prototype_name' => '__name__',
				'by_reference' => false,
				'entry_options' => array('label' => false),
				'attr' => [
					"data-allow-new" => true,
					"data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction'
				]
			))
			->add('especeIdentifiees', CollectionType::class, array(
				'entry_type' => EspeceIdentifieeEmbedType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'prototype' => true,
				'prototype_name' => '__name__',
				'by_reference' => false,
				'entry_options' => array('label' => false)
			))
			->add('lotMaterielExtEstReferenceDanss', CollectionType::class, array(
				'entry_type' => LotMaterielExtEstReferenceDansEmbedType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'prototype' => true,
				'prototype_name' => '__name__',
				'by_reference' => false,
				'entry_options' => array('label' => false)
			))
			->addEventSubscriber($this->addUserDate);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		parent::configureOptions($resolver);
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\LotMaterielExt'
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'bbees_e3sbundle_lotmaterielext';
	}
}
