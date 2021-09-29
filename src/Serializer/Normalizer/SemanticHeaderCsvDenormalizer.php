<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SemanticHeaderCsvDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface {
  private const MATCH_COLLECTION = '/^(?P<prop>\\w+)#(?P<related_prop>\\w+)$/';

  private const MATCH_VOC_PARENT = '/^(?P<prop>\w+):(?P<parent>\w*)$/';

  private function stringAsCollection(string $value, string $propertyName, string $sep = '$') {
    return $value
            ? array_map(fn ($v) => [$propertyName => trim($v)], explode('$', $value))
            : [];
  }

  private function processRecord($record) {
    $processedData = [
      'ownProperties' => [],
      'relations' => [],
    ];

    foreach ($record as $key => $value) {
      if (preg_match(self::MATCH_COLLECTION, $key, $matches)) {
        $collection = $this->stringAsCollection($value, $matches['related_prop']);
        $processedData['relations'][$matches['prop']] = $collection;
      } elseif (preg_match(self::MATCH_VOC_PARENT, $key, $matches)) {
        $value = array_merge($value, [
          'parent' => $matches['parent'] ? $matches['parent'] : $matches['prop'],
        ]);
        $processedData['relations'][$matches['prop']] = $value;
      } else {
        $value = ('' === $value) ? null : $value;
        $category = is_array($value) ? 'relations' : 'ownProperties';
        $processedData[$category][$key] = $value;
      }
    }

    return $processedData;
  }

  public function denormalize($data, string $type, ?string $format = null, array $context = []) {
    return array_map([$this, 'processRecord'], $data);
  }

  public function supportsDenormalization($data, string $type, ?string $format = null) {
    return 'array' === $type && 'csv' == $format;
  }

  public function hasCacheableSupportsMethod(): bool {
    return true;
  }
}
