<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Serializer\Mapping\Factory;

use Psr\Cache\CacheItemPoolInterface;
/**
 * Caches metadata using a PSR-6 implementation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CacheClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $decorated;
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;
    public function __construct(ClassMetadataFactoryInterface $decorated, CacheItemPoolInterface $cacheItemPool)
    {
        $this->decorated = $decorated;
        $this->cacheItemPool = $cacheItemPool;
    }
    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($value)
    {
        $class = $this->getClass($value);
        // Key cannot contain backslashes according to PSR-6
        $key = strtr($class, '\\', '_');
        $item = $this->cacheItemPool->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }
        $metadata = $this->decorated->getMetadataFor($value);
        $this->cacheItemPool->save($item->set($metadata));
        return $metadata;
    }
    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value)
    {
        return $this->decorated->hasMetadataFor($value);
    }
    /**
     * Gets a class name for a given class or instance.
     *
     * @param mixed $value
     *
     * @return string
     *
     * @throws InvalidArgumentException If the class does not exists
     */
    private function getClass($value)
    {
        if (is_string($value)) {
            if (!class_exists($value) && !interface_exists($value)) {
                throw new InvalidArgumentException(sprintf('The class or interface "%s" does not exist.', $value));
            }
            return ltrim($value, '\\');
        }
        if (!is_object($value)) {
            throw new InvalidArgumentException(sprintf('Cannot create metadata for non-objects. Got: "%s"', gettype($value)));
        }
        return get_class($value);
    }
}