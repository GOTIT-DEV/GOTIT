<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
	// get parameters
	$parameters = $containerConfigurator->parameters();
	$parameters->set(Option::PATHS, [__DIR__ . '/src']);
	//   $parameters->set(Option::IMPORT_SHORT_CLASSES, false);
	$parameters->set(Option::AUTO_IMPORT_NAMES, true);

	// Define what rule sets will be applied
	// $containerConfigurator->import(SetList::CODE_QUALITY);
	$containerConfigurator->import(SetList::PHP_74);
	$containerConfigurator->import(SetList::PHP_80);
	// $containerConfigurator->import(
	// 	DoctrineSetList::ENTITY_ANNOTATIONS_TO_ATTRIBUTES,
	// );
	// $containerConfigurator->import(DoctrineSetList::DOCTRINE_CODE_QUALITY);
	$containerConfigurator->import(DoctrineSetList::DOCTRINE_ORM_29);

	$services = $containerConfigurator->services();

	$services->set(
		AnnotationToAttributeRector::class,
	)
	->call('configure', [
		[
			AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline(
				[
					new AnnotationToAttribute(
						'ApiPlatform\\Core\\Annotation\\ApiResource',
						'ApiPlatform\\Core\\Annotation\\ApiResource',
					),
					new AnnotationToAttribute(
						'ApiPlatform\\Core\\Annotation\\ApiProperty',
						'ApiPlatform\\Core\\Annotation\\ApiProperty',
					),
					new AnnotationToAttribute(
						'ApiPlatform\\Core\\Annotation\\ApiFilter',
						'ApiPlatform\\Core\\Annotation\\ApiFilter',
					),
					new AnnotationToAttribute(
						"Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity",
						"Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity",
					),
					new AnnotationToAttribute(
						"Symfony\Component\Serializer\Annotation\Groups",
						"Symfony\Component\Serializer\Annotation\Groups",
					),
					new AnnotationToAttribute(
						"Symfony\Component\Validator\Constraints",
						"Symfony\Component\Validator\Constraints",
					),
					new AnnotationToAttribute(
						"Symfony\Component\Validator\Constraints\NotBlank",
						"Symfony\Component\Validator\Constraints\NotBlank",
					),
					// new AnnotationToAttribute('ORM\EntityListeners', 'ORM\EntityListeners'),
				],
			),
		],
	]);
	// get services (needed for register a single rule)
	// $services = $containerConfigurator->services();

	// register a single rule
	// $services->set(TypedPropertyRector::class);
};
