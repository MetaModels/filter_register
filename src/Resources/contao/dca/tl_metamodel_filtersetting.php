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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['register extends default'] = [
    '+config' => [
        'attr_id',
        'urlparam',
        'label',
        'template',
        'shownumbers',
        'hideempty',
        'onlypossible',
        'skipfilteroptions'
    ],
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['shownumbers'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['shownumbers'],
    'exclude'                 => true,
    'default'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => [
        'tl_class'            => 'clr w50',
    ],
    'sql'                     => 'char(1) NOT NULL default \'1\''
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['hideempty'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['hideempty'],
    'exclude'                 => true,
    'default'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => [
        'tl_class'            => 'w50',
    ],
    'sql'                     => 'char(1) NOT NULL default \'1\''
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['onlypossible'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['onlypossible'],
    'exclude'                 => true,
    'default'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => [
        'tl_class'            => 'w50',
    ],
    'sql'                     => 'char(1) NOT NULL default \'1\''
];
