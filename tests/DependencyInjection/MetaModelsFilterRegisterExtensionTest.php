<?php

/**
 * This file is part of MetaModels/filter_register.
 *
 * (c) 2012-2024 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/filter_register
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterRegisterBundle\Test\DependencyInjection;

use MetaModels\FilterRegisterBundle\DependencyInjection\MetaModelsFilterRegisterExtension;
use MetaModels\FilterRegisterBundle\FilterSetting\RegisterFilterSettingTypeFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 *
 * @covers \MetaModels\FilterRegisterBundle\DependencyInjection\MetaModelsFilterRegisterExtension
 */
class MetaModelsFilterRegisterExtensionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $extension = new MetaModelsFilterRegisterExtension();

        $this->assertInstanceOf(MetaModelsFilterRegisterExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    public function testFactoryIsRegistered(): void
    {
        $container = new ContainerBuilder();

        $extension = new MetaModelsFilterRegisterExtension();
        $extension->load([], $container);

        self::assertTrue($container->hasDefinition('metamodels.filter_register.factory'));
        $definition = $container->getDefinition('metamodels.filter_register.factory');
        self::assertCount(1, $definition->getTag('metamodels.filter_factory'));
    }
}
