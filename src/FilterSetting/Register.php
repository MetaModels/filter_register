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
 * @author     Olli <olli17@gmx.net>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Markus Nestmann <markus.nestmann@outlook.com>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_register/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterRegisterBundle\FilterSetting;

use Contao\StringUtil;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\IFilter as IMetaModelFilter;
use MetaModels\Filter\Rules\StaticIdList;
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
     * @return string|null
     */
    protected function getParamName()
    {
        if ($this->get('urlparam')) {
            return $this->get('urlparam');
        }

        $objAttribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));
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
        return \in_array($strKeyOption, \explode(',', $arrWidget['value'])) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFrontendFilterValue($arrWidget, $arrFilterUrl, $strKeyOption)
    {
        if ($this->get('filtermultiple')) {
            $arrCurrent = !empty($arrWidget['value']) ? \explode(',', $arrWidget['value']) : [];

            // toggle if active.
            if ($this->isActiveFrontendFilterValue($arrWidget, $arrFilterUrl, $strKeyOption)) {
                $arrCurrent = \array_diff($arrCurrent, [$strKeyOption]);
            } else {
                $arrCurrent[] = $strKeyOption;
            }

            return \implode(',', $arrCurrent);
        }

        // toggle if active.
        if ($this->isActiveFrontendFilterValue($arrWidget, $arrFilterUrl, $strKeyOption)) {
            $current = '';
        } else {
            $current = $strKeyOption;
        }

        return $current;
    }

    /**
     * Generate the filter options for the parameters.
     *
     * @param IAttribute        $objAttribute The attribute to fetch the values from.
     * @param list<string>|null $arrIds       The id list to limit the results to.
     * @param null|array        $arrCount     The array to use for storing the count.
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
            $mixOptions = \trim(\strip_tags($mixOptions));

            if ($mixOptions === '') {
                unset($arrOptions[$mixOptionKey]);
            }
        }

        $arrNewOptions = [];
        $arrNewCount   = [];

        // Sort the values, first char uppercase.
        foreach ($arrOptions as $strOptionsKey => $strOptionValue) {
            if ($strOptionsKey === '-') {
                continue;
            }

            $strFirstChar   = \mb_substr($strOptionValue, 0, 1);
            $charUpperFist  = \mb_strtoupper($strFirstChar);
            $charLowerFirst = \mb_strtolower($strFirstChar);

            $arrNewOptions[$charLowerFirst] = $charUpperFist;
            $arrNewCount[$charLowerFirst]   = (($arrNewCount[$charLowerFirst] ?? 0) + ($arrCount[$strOptionsKey] ?? 0));
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
        $objMetaModel = $this->getMetaModel();
        $objAttribute = $objMetaModel->getAttributeById((int) $this->get('attr_id'));
        $strParamName = $this->getParamName();
        assert(\is_string($strParamName));
        $strParamValue = $arrFilterUrl[$strParamName];

        // Check if we have a valid value.
        if (!$objAttribute) {
            $objFilter->addFilterRule(new StaticIdList(null));

            return;
        }

        if ($strParamValue) {
            $arrIds = [];
            foreach (\explode(',', $strParamValue) as $paramKey) {
                $charResult = $objAttribute->searchFor($paramKey . '%');
                if (null === $charResult) {
                    $objFilter->addFilterRule(new StaticIdList(null));
                    return;
                }
                $arrIds = \array_merge($arrIds, $charResult);
            }
            $objFilter->addFilterRule(new StaticIdList($arrIds));

            return;
        }

        $objFilter->addFilterRule(new StaticIdList(null));
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.LongVariable)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getParameterFilterWidgets(
        $arrIds,
        $arrFilterUrl,
        $arrJumpTo,
        FrontendFilterOptions $objFrontendFilterOptions
    ) {
        $objAttribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));
        if (!$objAttribute) {
            return [];
        }

        $arrCount   = [];
        $arrOptions = $this->getParameterFilterOptions($objAttribute, $arrIds, $arrCount);

        $strParamName = $this->getParamName() ?? '';
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

        $cssID = StringUtil::deserialize($this->get('cssID'), true);

        return [
            $strParamName => $this->prepareFrontendFilterWidget(
                [
                    'label'     => [
                        $this->getLabel(),
                        'GET: ' . $strParamName
                    ],
                    'inputType' => 'tags',
                    'options'   => $arrOptions,
                    'count'     => $arrCount,
                    'showCount' => $objFrontendFilterOptions->isShowCountValues(),
                    'eval'      => [
                        'includeBlankOption' => ($this->get('blankoption')
                                                 && !$objFrontendFilterOptions->isHideClearFilter()),
                        'blankOptionLabel'   => $this->translator->trans('do_not_filter', [], 'metamodels_filter'),
                        'multiple'           => true,
                        'colname'            => $objAttribute->getColName(),
                        'urlparam'           => $strParamName,
                        'onlypossible'       => $this->get('onlypossible'),
                        'shownumbers'        => $this->get('shownumbers'),
                        'hideempty'          => $this->get('hideempty'),
                        'template'           => $this->get('template'),
                        'hide_label'         => $this->get('hide_label'),
                        'cssID'              => !empty($cssID[0]) ? ' id="' . $cssID[0] . '"' : '',
                        'class'              => !empty($cssID[1]) ? ' ' . $cssID[1] : '',
                    ],
                    // we need to implode again to have it transported correctly in the frontend filter.
                    'urlvalue'  => !empty($arrParamValue) ? \implode(',', $arrParamValue) : ''
                ],
                $arrFilterUrl,
                $arrJumpTo,
                $objFrontendFilterOptions
            )
        ];
    }
}
