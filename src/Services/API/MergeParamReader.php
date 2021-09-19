<?php

namespace App\Services\API;
use Doctrine\Common\Annotations\Reader;
use FOS\RestBundle\Controller\Annotations\ParamInterface;
use FOS\RestBundle\Request\ParamReaderInterface;

class MergeParamReader implements ParamReaderInterface {
  private $annotationReader;

  public function __construct(Reader $annotationReader) {
    $this->annotationReader = $annotationReader;
  }

  /**
   * {@inheritdoc}
   */
  public function read(\ReflectionClass $reflection, string $method): array
  {
    if (!$reflection->hasMethod($method)) {
      throw new \InvalidArgumentException(sprintf('Class "%s" has no method "%s".', $reflection->getName(), $method));
    }

    $methodParams = $this->getParamsFromMethod($reflection->getMethod($method));
    $classParams = $this->getParamsFromClass($reflection);

    return array_merge($methodParams, $classParams);
  }

  public function _getParamsFromMethod(\ReflectionMethod $method): array
  {
    $annotations = $this->annotationReader->getMethodAnnotations($method);

    return $this->getParamsFromAnnotationArray($annotations);
  }

  /**
   * {@inheritdoc}
   */
  public function getParamsFromMethod(\ReflectionMethod $method) {
    $parentParams = array();
    $params = $this->_getParamsFromMethod($method);

    // This loads the annotations of the parent method
    $declaringClass = $method->getDeclaringClass();
    $parentClass = $declaringClass->getParentClass();

    if ($parentClass && $parentClass->hasMethod($method->getShortName())) {
      $parentMethod = $parentClass->getMethod($method->getShortName());
      $parentParams = $this->_getParamsFromMethod($parentMethod);
    }

    return array_merge($params, $parentParams);
  }

  /**
   * {@inheritdoc}
   */
  public function getParamsFromClass(\ReflectionClass $class): array
  {
    $annotations = $this->annotationReader->getClassAnnotations($class);

    return $this->getParamsFromAnnotationArray($annotations);
  }

  /**
   * @return ParamInterface[]
   */
  private function getParamsFromAnnotationArray(array $annotations): array
  {
    $params = [];
    foreach ($annotations as $annotation) {
      if ($annotation instanceof ParamInterface) {
        $params[$annotation->getName()] = $annotation;
      }
    }

    return $params;
  }

}