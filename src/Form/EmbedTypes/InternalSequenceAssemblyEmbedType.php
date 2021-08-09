<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternalSequenceAssemblyEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('chromatogramFk', EntityType::class, [
      'class' => 'App:Chromatogram',
      'query_builder' => function (EntityRepository $er) use ($options) {
        $qb = $er->createQueryBuilder('chromatogram');
        return $qb->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogram.pcrFk = pcr.id')
          ->leftJoin('App:Dna', 'dna', 'WITH', 'pcr.dnaFk = dna.id')
          ->leftJoin('App:Specimen', 'specimen', 'WITH', 'dna.specimenFk = specimen.id')
          ->leftJoin('App:Voc', 'vocSpecificite', 'WITH', 'pcr.specificiteVocFk = vocSpecificite.id')
          ->where('pcr.geneVocFk = :geneVocFk')
          ->andwhere('specimen.id = :specimenFk')
          ->setParameters([
            'specimenFk' => $options['specimenFk'],
            'geneVocFk' => $options['geneVocFk'],
          ]);
      },
      'choice_label' => 'codeSpecificity',
      'multiple' => false,
      'expanded' => false,
      'required' => true,
      'label' => 'Code | Specificite',
      'placeholder' => 'Choose a chromatogram',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\InternalSequenceAssembly',
      'geneVocFk' => null,
      'specimenFk' => null,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'assembly';
  }
}
