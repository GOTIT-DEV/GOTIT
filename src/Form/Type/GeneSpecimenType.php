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

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\GeneType;
use App\Form\Enums\Action;
use App\Form\ActionFormType;

class GeneSpecimenType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $form_data = $builder->getData();
    $gene      = $form_data['geneVocFk'];
    $specimen  = $form_data['individuFk'];

    $builder
      ->add('geneVocFk', GeneType::class, [
        'query_builder' =>
        function (EntityRepository $er) use ($gene) {
          $qb = $er->createQueryBuilder('voc')
            ->where("voc.parent = 'gene'")
            ->orderBy('voc.libelle', 'ASC');
          if ($gene) {
            $qb = $qb
              ->andWhere('voc.id = :geneVocFk')
              ->setParameter('geneVocFk', $gene->getId());
          }

          return $qb;
        },
      ])
      ->add('individuFk', SearchableSelectType::class, [
        'class'        => 'App:Individu',
        'choice_label' => 'codeIndBiomol',
        'placeholder'  => $this->translator->trans("Individu typeahead placeholder"),
        'attr'         => [
          'readonly' => $this->canEditAdminOnly($options) || $specimen != null,
        ],
      ]);

    if ($options['action_type'] != Action::show()) {
      $builder->add('button.Valid', SubmitType::class, array(
        'label' => 'button.Valid',
        'attr'  => ['class' => 'btn btn-round btn-success'],
      ));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'gene_specimen_form';
  }
}
