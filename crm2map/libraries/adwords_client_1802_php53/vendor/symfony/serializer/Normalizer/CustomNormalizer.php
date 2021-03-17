<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Serializer\Normalizer;

use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class CustomNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private $cache = array();
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $object->normalize($this->serializer, $format, $context);
    }
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $object = $this->extractObjectToPopulate($class, $context) ?: new $class();
        $object->denormalize($this->serializer, $data, $format, $context);
        return $object;
    }
    /**
     * Checks if the given class implements the NormalizableInterface.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof NormalizableInterface;
    }
    /**
     * Checks if the given class implements the DenormalizableInterface.
     *
     * @param mixed  $data   Data to denormalize from
     * @param string $type   The class to which the data should be denormalized
     * @param string $format The format being deserialized from
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (isset($this->cache[$type])) {
            return $this->cache[$type];
        }
        if (!class_exists($type)) {
            return $this->cache[$type] = false;
        }
        return $this->cache[$type] = is_subclass_of($type, 'Symfony\\Component\\Serializer\\Normalizer\\DenormalizableInterface');
    }
    /**
     * Extract the `object_to_populate` field from the context if it exists
     * and is an instance of the provided $class.
     *
     * @param string $class The class the object should be
     * @param $context The denormalization context
     * @param string $key They in which to look for the object to populate.
     *                    Keeps backwards compatibility with `AbstractNormalizer`.
     *
     * @return object|null an object if things check out, null otherwise
     */
    protected function extractObjectToPopulate($class, array $context, $key = null)
    {
        $key = $key ?: 'object_to_populate';
        if (isset($context[$key]) && is_object($context[$key]) && $context[$key] instanceof $class) {
            return $context[$key];
        }
        return null;
    }
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * Sets the serializer.
     *
     * @param SerializerInterface $serializer A SerializerInterface instance
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}