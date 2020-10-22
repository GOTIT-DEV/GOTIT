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

namespace App\Form\Type;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\GeneType;
use App\Form\EventListener\AddUserDateFields;
use App\Form\Enums\Action;
use App\Form\ActionFormType;
use App\Entity\Voc;
use App\Entity\Individu;

class GeneSpecimenType extends ActionFormType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $form_data = $builder->getData();
    $gene = $form_data['gene'];
    $specimen = $form_data['specimen'];

    $builder
      ->add('geneVocFk', GeneType::class, [
        'query_builder' =>
        function (EntityRepository $er) use ($gene) {
          $qb = $er->createQueryBuilder('voc')->where("voc.parent = 'gene'");
          if ($gene)
            $qb = $qb
              ->andWhere('voc.id = :geneVocFk')
              ->setParameter('geneVocFk', $gene->getId())
              ->orderBy('voc.libelle', 'ASC');
          return $qb;
        },
        'data' => $gene
      ])
      ->add('individuTypeahead', null, [
        'mapped' => false,
        'data' => $specimen ? $specimen->getCodeIndBioMol() : "",
        'attr' => [
          'class' => 'typeahead typeahead-individu',
          'data-target-id' => $this->getBlockPrefix() . "_individuFk",
          'name' => "where",
          'placeholder' => "Individu typeahead placeholder",
          "maxlength" => "255",
        ],
        'required' => true,
      ])
      ->add('individuFk', HiddenType::class, [
        'mapped' => false,
        'required' => true,
        'data' => $specimen ? $specimen->getId() : null,
      ]);
      
    if ($options['action_type'] != Action::show())
      $builder->add('button.Valid', SubmitType::class, array(
        'label' => 'button.Valid',
        'attr' => array('class' => 'btn btn-round btn-success')
      ));
  }


  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'gene_specimen_form';
  }
}
