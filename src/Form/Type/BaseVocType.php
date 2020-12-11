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
