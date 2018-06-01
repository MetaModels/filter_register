<?php

/**
 * This file is part of MetaModels/filter_register.
 *
 * (c) 2012-2018 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage FilterRegister
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use MetaModels\Filter\Setting\Events\CreateFilterSettingFactoryEvent;
use MetaModels\Filter\Setting\RegisterFilterSettingTypeFactory;
use MetaModels\MetaModelsEvents;

return [
    MetaModelsEvents::FILTER_SETTING_FACTORY_CREATE => [
        function (CreateFilterSettingFactoryEvent $event) {
            $event->getFactory()->addTypeFactory(new RegisterFilterSettingTypeFactory());
        }
    ]
];
