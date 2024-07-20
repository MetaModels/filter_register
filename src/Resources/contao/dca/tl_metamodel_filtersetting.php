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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['register extends default'] = [
    '+config' => [
        'attr_id',
        'urlparam',
        'label',
        'hide_label',
        'template',
        'shownumbers',
        'hideempty',
        'filtermultiple',
        'onlypossible',
        'skipfilteroptions',
        'cssID'
    ],
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['shownumbers'] = [
    'label'       => 'shownumbers.label',
    'description' => 'shownumbers.description',
    'exclude'     => true,
    'default'     => '1',
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'clr w50 cbx m12',
    ],
    'sql'         => 'char(1) NOT NULL default \'1\''
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['hideempty'] = [
    'label'       => 'hideempty.label',
    'description' => 'hideempty.description',
    'exclude'     => true,
    'default'     => '1',
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'w50 cbx m12',
    ],
    'sql'         => 'char(1) NOT NULL default \'1\''
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['filtermultiple'] = [
    'label'       => 'filtermultiple.label',
    'description' => 'filtermultiple.description',
    'exclude'     => true,
    'default'     => false,
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'w50 cbx',
    ],
    'sql'         => 'char(1) NOT NULL default \'\''
];
