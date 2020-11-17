<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ChoiceLoader\AutocompleteChoiceLoader;

class SearchableSelectType extends AbstractType {

  public function __construct(EntityManagerInterface $em) {
    $this->entityManager = $em;
  }

  public function configureOptions(OptionsResolver $resolver) {

    $resolver->setRequired(["choice_label", "class"]);

    $resolver->setDefaults([
      'multiple' => false,
      'expanded' => false,
      'choice_value' => 'id',
      /* Load choices using preset data
       * Also add choices generated from autocompletion on client side
       */
      'choice_loader' => function (Options $options) {
        $entityRepository = $this->entityManager->getRepository($options['class']);
        return new AutocompleteChoiceLoader($entityRepository, $options['choice_value']);
      },
      // Disable fetching entities in querybuilder
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('x')->where('x.id is NULL');
      },
    ]);

    $resolver->setNormalizer('attr', function (Options $options, $value) {
      return array_merge($value, [
        'class' => ($attrs['class'] ?? "") . " remote-source",
        'data-minimum-input-length' => 1,
      ]);
    });
  }

  public function getParent() {
    return EntityType::class;
  }
}
