<?php

namespace App\DataTransformer;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DTO\CsvRecordsRequest;
use App\Serializer\Normalizer\SemanticHeaderCsvDenormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class CsvRecordsRequestTransformer implements DataTransformerInterface {
  public function __construct(
    private EntityManagerInterface $em,
    private ValidatorInterface $validator,
  ) {
    $this->serializer = new Serializer(
      [new SemanticHeaderCsvDenormalizer()],
      [new CsvEncoder([CsvEncoder::DELIMITER_KEY => ';'])]
    );
    $this->objNormalizer = new ObjectNormalizer();
  }

  private function getRelatedBy(string $className, array $conditions) {
    return $this->em->getRepository($className)->findOneBy($conditions);
  }

  private function makeEntityNotFoundViolation(string $property, string $targetEntity, array $relation, int $line) {
    $message = sprintf('Entity %s not found with properties : %s', $targetEntity, json_encode($relation));

    return new ConstraintViolation(
      $message, null, [], $relation, "[{$line}].{$property}",
      $relation, null, 'related_not_found'
    );
  }

  private function validate($data, $constraintViolations) {
    // Merge custom and built-in validation errors
    try {
      $this->validator->validate($data);
    } catch (ValidationException $e) {
      $violations = $e->getConstraintViolationList();
      $constraintViolations->addAll($violations);
    }
    if ($constraintViolations->count()) {
      throw new ValidationException($constraintViolations);
    }
  }

  public function transform($object, string $to, array $context = []) {
    $records = $this->serializer->deserialize($object->getCsv(), 'array', 'csv');

    $data = new ArrayCollection();
    $notFoundViolations = new ConstraintViolationList();
    $meta = $this->em->getMetadataFactory()->getMetadataFor($to);

    // Create all entities
    foreach ($records as $line => $record) {
      $relations = [];
      // Retrieve all related entities
      foreach ($record['relations'] as $property => $relation) {
        $mapping = $meta->getAssociationMapping($property);
        $targetEntity = $mapping['targetEntity'];

        if (
          ClassMetadataInfo::MANY_TO_MANY === $mapping['type']
        || ClassMetadataInfo::ONE_TO_MANY === $mapping['type']
        ) {
          $relations[$property] = [];
          foreach ($relation as $rel) {
            $related = $this->getRelatedBy($targetEntity, $rel);
            if (null === $related) {
              $violation = $this->makeEntityNotFoundViolation($property, $targetEntity, $relation, $line);
              $notFoundViolations->add($violation);
            }
            $relations[$property][] = $related;
          }
        } else { //is many-to-one
          $related = $this->getRelatedBy($targetEntity, $relation);
          if (null === $related) {
            $violation = $this->makeEntityNotFoundViolation($property, $targetEntity, $relation, $line);
            $notFoundViolations->add($violation);
          }
          $relations[$property] = $related;
        }
      }
      // Denormalize to target entity
      $result = array_merge($relations, $record['ownProperties']);
      $entity = $this->objNormalizer->denormalize($result, $to, 'csv', $context);
      $data->add($entity);
    }

    $this->validate($data, $notFoundViolations);

    return $data;
  }

  public function supportsTransformation($data, string $to, array $context = []): bool {
    return is_array($data) && CsvRecordsRequest::class === ($context['input']['class'] ?? null);
  }
}
