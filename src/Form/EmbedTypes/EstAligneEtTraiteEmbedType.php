<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Chromatogramme;

class EstAligneEtTraiteEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('chromatogrammeFk', EntityType::class, [
      'class' => Chromatogramme::class,
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
      'choice_label' => 'codeChromatoSpecificite',
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
  public function getBlockPrefix(): string {
    return 'estaligneettraite';
  }
}
