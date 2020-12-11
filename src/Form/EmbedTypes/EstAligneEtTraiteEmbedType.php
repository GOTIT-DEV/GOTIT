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

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstAligneEtTraiteEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('chromatogrammeFk', EntityType::class, [
      'class' => 'App:Chromatogramme',
      'query_builder' => function (EntityRepository $er) use ($options) {
        $qb = $er->createQueryBuilder('chromatogramme');
        return $qb->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogramme.pcrFk = pcr.id')
          ->leftJoin('App:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
          ->leftJoin('App:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
          ->leftJoin('App:Voc', 'vocSpecificite', 'WITH', 'pcr.specificiteVocFk = vocSpecificite.id')
          ->where('pcr.geneVocFk = :geneVocFk')
          ->andwhere('individu.id = :individuFk')
          ->setParameters([
            'individuFk' => $options['individuFk'],
            'geneVocFk' => $options['geneVocFk'],
          ]);
      },
      'choice_label' => 'codeChromato',
      'multiple' => false,
      'expanded' => false,
      'required' => true,
      'label' => 'Code Chromato | Specificite',
      'placeholder' => 'Choose a chromatogramme',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\EstAligneEtTraite',
      'geneVocFk' => null,
      'individuFk' => null,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'estaligneettraite';
  }
}
