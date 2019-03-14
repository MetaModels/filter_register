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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     Olli <olli17@gmx.net>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterRegisterBundle\FilterSetting;

use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\IFilter as IMetaModelFilter;
use MetaModels\Filter\Rules\StaticIdList as MetaModelFilterRuleStaticIdList;
use MetaModels\Filter\Setting\SimpleLookup;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Filter "register" for FE-filtering, based on filters by the MetaModels team.
 */
class Register extends SimpleLookup
{
    /**
     * Retrieve the filter parameter name to react on.
     *
     * @return string
     */
    protected function getParamName()
    {
        if ($this->get('urlparam')) {
            return $this->get('urlparam');
        }

        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));
        if ($objAttribute) {
            return $objAttribute->getColName();
        }

        return null;
    }

    /**
     * Overrides the parent implementation to always return true, as this setting is always available for FE filtering.
     *
     * @return bool true as this setting is always available.
     */
    public function enableFEFilterWidget()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function isActiveFrontendFilterValue($arrWidget, $arrFilterUrl, $strKeyOption)
    {
        return \in_array($strKeyOption, (array) $arrWidget['value']) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFrontendFilterValue($arrWidget, $arrFilterUrl, $strKeyOption)
    {
        $arrCurrent = (array) $arrWidget['value'];
        // toggle if active.
        if ($this->isActiveFrontendFilterValue($arrWidget, $arrFilterUrl, $strKeyOption)) {
            $arrCurrent = \array_diff($arrCurrent, [$strKeyOption]);
        } else {
            $arrCurrent[] = $strKeyOption;
        }
        return \implode(',', $arrCurrent);
    }

    /**
     * Generate the filter options for the parameters.
     *
     * @param IAttribute $objAttribute The attribute to fetch the values from.
     *
     * @param array      $arrIds       The id list to limit the results to.
     *
     * @param null|array $arrCount     The array to use for storing the count.
     *
     * @return array
     */
    protected function getParameterFilterOptions($objAttribute, $arrIds, &$arrCount = null)
    {
        $arrOptions = $objAttribute->getFilterOptions(
            $this->get('onlypossible') ? $arrIds : null,
            (bool) $this->get('onlyused'),
            $arrCount
        );

        // Remove empty values.
        foreach ($arrOptions as $mixOptionKey => $mixOptions) {
            // Remove html/php tags.
            $mixOptions = \strip_tags($mixOptions);
            $mixOptions = \trim($mixOptions);

            if ($mixOptions === '' || $mixOptions === null) {
                unset($arrOptions[$mixOptionKey]);
            }
        }

        $arrNewOptions = [];
        $arrNewCount   = [];

        // Sort the values, first char uppercase.
        foreach ($arrOptions as $strOptionsKey => $strOptionValue) {
            if ($strOptionsKey == '-') {
                continue;
            }

            $strFirstChar   = \mb_substr($strOptionValue, 0, 1);
            $charUpperFist  = \ucfirst($strFirstChar);
            $charLowerFirst = \lcfirst($strFirstChar);

            $arrNewOptions[$charLowerFirst] = $charUpperFist;
            $arrNewCount[$charLowerFirst]   = ($arrNewCount[$charLowerFirst] + $arrCount[$strOptionsKey]);
        }

        $arrOptions = $arrNewOptions;
        $arrCount   = $arrNewCount;

        return $arrOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRules(IMetaModelFilter $objFilter, $arrFilterUrl)
    {
        $objMetaModel  = $this->getMetaModel();
        $objAttribute  = $objMetaModel->getAttributeById($this->get('attr_id'));
        $strParamName  = $this->getParamName();
        $strParamValue = $arrFilterUrl[$strParamName];
        $strWhat       = $strParamValue . '%';

        if ($objAttribute && $strParamName && $strParamValue) {
            $arrIds = $objAttribute->searchFor($strWhat);
            $objFilter->addFilterRule(new MetaModelFilterRuleStaticIdList($arrIds));
            return;
        }

        $objFilter->addFilterRule(new MetaModelFilterRuleStaticIdList(null));
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function getParameterFilterWidgets(
        $arrIds,
        $arrFilterUrl,
        $arrJumpTo,
        FrontendFilterOptions $objFrontendFilterOptions
    ) {
        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));

        $arrCount   = [];
        $arrOptions = $this->getParameterFilterOptions($objAttribute, $arrIds, $arrCount);

        $strParamName = $this->getParamName();
        // if we have a value, we have to explode it by comma to have a valid value which the active
        // checks may cope with.
        if (\array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName])) {
            if (\is_array($arrFilterUrl[$strParamName])) {
                $arrParamValue = $arrFilterUrl[$strParamName];
            } else {
                $arrParamValue = \explode(',', $arrFilterUrl[$strParamName]);
            }

            // ok, this is rather hacky here. The magic value of '--none--' means clear in the widget.
            if (\in_array('--none--', $arrParamValue)) {
                $arrParamValue = null;
            }
        }

        $GLOBALS['MM_FILTER_PARAMS'][] = $strParamName;

        return [
            $this->getParamName() => $this->prepareFrontendFilterWidget(
                [
                    'label'     => [
                        ($this->get('label') ? $this->get('label') : $objAttribute->getName()),
                        'GET: ' . $strParamName
                    ],
                    'inputType' => 'tags',
                    'options'   => $arrOptions,
                    'count'     => $arrCount,
                    'showCount' => $objFrontendFilterOptions->isShowCountValues(),
                    'eval'      => [
                        'includeBlankOption' => ($this->get('blankoption')
                                                 && !$objFrontendFilterOptions->isHideClearFilter()),
                        'blankOptionLabel'   => &$GLOBALS['TL_LANG']['metamodels_frontendfilter']['do_not_filter'],
                        'multiple'           => true,
                        'colname'            => $objAttribute->getColName(),
                        'urlparam'           => $strParamName,
                        'onlypossible'       => $this->get('onlypossible'),
                        'shownumbers'        => $this->get('shownumbers'),
                        'hideempty'          => $this->get('hideempty'),
                        'template'           => $this->get('template')
                    ],
                    // we need to implode again to have it transported correctly in the frontend filter.
                    'urlvalue'  => !empty($arrParamValue) ? \implode(',', $arrParamValue) : ''
                ],
                [],
                $arrJumpTo,
                $objFrontendFilterOptions
            )
        ];
    }
}
