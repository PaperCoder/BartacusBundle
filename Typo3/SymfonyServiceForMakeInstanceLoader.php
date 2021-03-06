<?php

declare(strict_types=1);

/*
 * This file is part of the Bartacus project, which integrates Symfony into TYPO3.
 *
 * Copyright (c) 2016-2017 Patrik Karisch
 *
 * The BartacusBundle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The BartacusBundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the BartacusBundle. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Bartacus\Bundle\BartacusBundle\Typo3;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Collects all classes which should be usable for {@see GeneralUtility::makeInstance()} calls.
 */
final class SymfonyServiceForMakeInstanceLoader
{
    /**
     * The tagged services for {@see GeneralUtility::makeInstance()} calls.
     *
     * [class name => instance]
     *
     * @var array
     */
    private $services = [];

    /**
     * @param string $className
     * @param object $instance
     */
    public function addService(string $className, $instance): void
    {
        $this->services[$className] = $instance;
    }

    /**
     * Loads all registered instances into the {@see GeneralUtility::makeInstance()} singleton cache.
     */
    public function load(): void
    {
        $refl = new \ReflectionClass(GeneralUtility::class);

        $this->loadClassNameCache($refl);
        $this->loadInstanceCache($refl);
    }

    private function loadClassNameCache(\ReflectionClass $refl): void
    {
        $reflProp = $refl->getProperty('finalClassNameCache');
        $reflProp->setAccessible(true);

        $classes = \array_keys($this->services);
        $classes = \array_combine($classes, $classes);

        $classNames = $reflProp->getValue();
        $classNames = \array_merge($classNames, $classes);

        $reflProp->setValue(null, $classNames);
    }

    private function loadInstanceCache(\ReflectionClass $refl): void
    {
        $reflProp = $refl->getProperty('singletonInstances');
        $reflProp->setAccessible(true);

        $instances = $reflProp->getValue();
        $instances = \array_merge($instances, $this->services);

        $reflProp->setValue(null, $instances);
    }
}
