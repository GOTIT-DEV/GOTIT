<?php

namespace App\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Intl\Collator\Collator;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class BaseVocType extends AbstractType {
  protected $translator;

  public function __construct(TranslatorInterface $translator, EntityManagerInterface $em) {
    $this->translator = $translator;
    $this->entityManager = $em;
  }

  public function finishView(FormView $view, FormInterface $form, array $options) {
    // Order translated labels
    $collator = new \Collator($this->translator->getLocale());
    usort(
      $view->vars['choices'],
      function ($a, $b) use ($collator, $options) {
        return $collator->compare(
          $options['sort_by_id'] ? $a->value : $this->translator->trans($a->label),
          $options['sort_by_id'] ? $a->value : $this->translator->trans($b->label)
        );
      }
    );
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'class' => 'App:Voc',
      'choice_label' => 'libelle',
      'choice_attr' => function ($choice, $key, $value) {
        return ['data-code' => $choice->getCode()];
      },
      'multiple' => false,
      'expanded' => false,
      'choice_translation_domain' => true,
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('voc');
      },
      'sort_by_id' => false,
    ]);
    $resolver->setRequired('voc_parent');
    $resolver->setNormalizer('query_builder',
      function (Options $options, $qbFactory) {
        return $qbFactory($this->entityManager->getRepository($options['class']))
          ->where("voc.parent = :parent")
          ->setParameter('parent', $options['voc_parent']);
      });
  }

  public function getParent() {
    return EntityType::class;
  }
}
