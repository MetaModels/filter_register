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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterRegisterBundle\Test\FilterSetting;

use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\FilterRegisterBundle\FilterSetting\Register;
use MetaModels\FilterRegisterBundle\FilterSetting\RegisterFilterSettingTypeFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This tests the factory.
 *
 * @covers \MetaModels\Filter\Setting\RegisterFilterSettingTypeFactory
 */
class RegisterFilterSettingTypeFactoryTest extends TestCase
{
    /**
     * Test that the factory sets all values.
     *
     * @return void
     */
    public function testFactory()
    {
        $eventDispatcher  = $this->getMockForAbstractClass(EventDispatcherInterface::class);
        $filterUrlBuilder = $this->getMockBuilder(FilterUrlBuilder::class)->disableOriginalConstructor()->getMock();
        $translator       = $this->getMockForAbstractClass(TranslatorInterface::class);

        $factory = new RegisterFilterSettingTypeFactory($eventDispatcher, $filterUrlBuilder, $translator);

        $this->assertSame('register', $factory->getTypeName());
        $this->assertSame(
            'bundles/metamodelsfilterregister/filter_register.png',
            $factory->getTypeIcon()
        );
        $this->assertSame(['tabletext', 'translatedtext', 'text'], $factory->getKnownAttributeTypes());
    }

    /**
     * Test that the factory creates an instance.
     *
     * @return void
     */
    public function testCreateInstance()
    {
        $eventDispatcher  = $this->getMockForAbstractClass(EventDispatcherInterface::class);
        $filterUrlBuilder = $this->getMockBuilder(FilterUrlBuilder::class)->disableOriginalConstructor()->getMock();
        $translator       = $this->getMockForAbstractClass(TranslatorInterface::class);

        $collection = $this->getMockForAbstractClass(ICollection::class);
        $factory    = new RegisterFilterSettingTypeFactory($eventDispatcher, $filterUrlBuilder, $translator);

        $this->assertInstanceOf(Register::class, $factory->createInstance([], $collection));
    }
}
