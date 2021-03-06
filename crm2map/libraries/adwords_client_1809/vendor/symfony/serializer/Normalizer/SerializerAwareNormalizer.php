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
 * SerializerAware Normalizer implementation.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 *
 * @deprecated since version 3.1, to be removed in 4.0. Use the SerializerAwareTrait instead.
 */
abstract class SerializerAwareNormalizer implements SerializerAwareInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * Sets the serializer.
     *
     * @param SerializerInterface $serializer A SerializerInterface instance
     */
    public function setSerializer(/*SerializerInterface */$serializer)
    {
        $this->serializer = $serializer;
    }
}