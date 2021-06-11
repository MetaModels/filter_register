<?php

/**
 * This file is part of MetaModels/filter_register.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/filter_register
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterRegisterBundle\Test\DependencyInjection;

use MetaModels\FilterRegisterBundle\DependencyInjection\MetaModelsFilterRegisterExtension;
use MetaModels\FilterRegisterBundle\FilterSetting\RegisterFilterSettingTypeFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 */
class MetaModelsFilterRegisterExtensionTest extends TestCase
{
    /**
     * Test that extension can be instantiated.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testInstantiation()
    {
        $extension = new MetaModelsFilterRegisterExtension();

        $this->assertInstanceOf(MetaModelsFilterRegisterExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testFactoryIsRegistered()
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();

        $container
            ->expects($this->atLeastOnce())
            ->method('setDefinition')
            ->withConsecutive(
                [
                    'metamodels.filter_register.factory',
                    $this->callback(
                        function ($value) {
                            /** @var Definition $value */
                            $this->assertInstanceOf(Definition::class, $value);
                            $this->assertEquals(RegisterFilterSettingTypeFactory::class, $value->getClass());
                            $this->assertCount(1, $value->getTag('metamodels.filter_factory'));

                            return true;
                        }
                    )
                ]
            );

        $extension = new MetaModelsFilterRegisterExtension();
        $extension->load([], $container);
    }
}
