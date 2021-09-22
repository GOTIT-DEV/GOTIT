<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternalSequenceAssemblyEmbedType extends AbstractType {

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'gene' => null,
      'specimen' => null,
      'class' => 'App:Chromatogram',
      'choice_label' => 'codeSpecificity',
      'multiple' => false,
      'expanded' => false,
      'required' => true,
      'label' => false,
      'placeholder' => 'Choose a chromatogram',
      "query_builder" => function (Options $options) {
        return function (EntityRepository $er) use ($options) {
          return $er->createQueryBuilder('chromatogram')
            ->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogram.pcr = pcr.id')
            ->leftJoin('App:Dna', 'dna', 'WITH', 'pcr.dna = dna.id')
            ->leftJoin('App:Specimen', 'specimen', 'WITH', 'dna.specimen = specimen.id')
            ->leftJoin('App:Voc', 'vocSpecificite', 'WITH', 'pcr.specificity = vocSpecificite.id')
            ->where('pcr.gene = :gene')
            ->andwhere('specimen = :specimen')
            ->setParameters([
              'specimen' => $options['specimen'],
              'gene' => $options['gene'],
            ]);
        };
      },
    ));
  }

  public function getParent() {
    return EntityType::class;
  }
}
