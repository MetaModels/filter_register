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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Marc Reimann <reimann@mediendepot-ruhr.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterRegisterBundle\FilterSetting;

use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\AbstractFilterSettingTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Attribute type factory for from-to filter settings.
 */
class RegisterFilterSettingTypeFactory extends AbstractFilterSettingTypeFactory
{
    /**
     * {@inheritDoc}
     *
     * @param EventDispatcherInterface $dispatcher       The event dispatcher.
     * @param FilterUrlBuilder         $filterUrlBuilder The filter URL builder.
     */
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly FilterUrlBuilder $filterUrlBuilder,
        private readonly TranslatorInterface $translator
    ) {
        parent::__construct();

        $this
            ->setTypeName('register')
            ->setTypeIcon('bundles/metamodelsfilterregister/filter_register.png')
            ->setTypeClass(Register::class)
            ->allowAttributeTypes('tabletext', 'translatedtext', 'text');
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $filterSettings)
    {
        return new Register(
            $filterSettings,
            $information,
            $this->dispatcher,
            $this->filterUrlBuilder,
            $this->translator
        );
    }
}
