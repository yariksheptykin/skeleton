<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Christian
 * Date: 14.09.11
 * Time: 20:53
 * To change this template use File | Settings | File Templates.
 */
 
class OnlineShop_Framework_FilterService {

    protected $config;

    /**
     * @param $config OnlineShop Configuration
     * @param Zend_View $view View in which the filters are rendered
     */
    public function __construct($config, Zend_View $view) {
        $this->config = $config;
        $this->view = $view;
    }

    /**
     * Returns instance of FilterType or
     * just the name if the name is not defined in the OnlineShop configuration
     *
     * @param $name
     * @return OnlineShop_Framework_FilterService_AbstractFilterType | string
     */
    public function getFilterDefinitionClass($name) {
        if($this->config->$name) {
            return new $this->config->$name->class($this->view, $this->config->$name->script,$this->config->$name);
        } else {
            return $name; //throw new OnlineShop_Framework_Exception_UnsupportedException($name . " not as filter type configured.");
        }
    }


    /**
     * Initializes the FilterService, adds all conditions to the ProductList and returns an array of the currently set filters
     *
     * @param OnlineShop_Framework_AbstractFilterDefinition $filterObject filter definition object to use
     * @param OnlineShop_Framework_ProductList $productList product list to use and add conditions to
     * @param array $params request params with eventually set filter conditions
     * @return array returns set filters
     */
    public function initFilterService(OnlineShop_Framework_AbstractFilterDefinition $filterObject, OnlineShop_Framework_ProductList $productList, $params = array()) {
        $currentFilter = array();

        if ($filterObject->getFilters()) {
            foreach($filterObject->getFilters() as $filter) {

                /**
                 * @var $filter OnlineShop_Framework_AbstractFilterDefinitionType
                 */
                $currentFilter = $this->addCondition($filter, $productList, $currentFilter, $params);
            }
        }

        if ($filterObject->getConditions()) {
            foreach($filterObject->getConditions() as $condition) {

                /**
                 * @var $condition OnlineShop_Framework_AbstractFilterDefinitionType
                 */
                $this->addCondition($condition, $productList, $currentFilter, array(), true);
            }
        }

        return $currentFilter;

    }

    /**
     * Returns filter frontend script for given filter type (delegates )
     *
     * @param OnlineShop_Framework_AbstractFilterDefinitionType $filterDefinition filter definition to get frontend script for
     * @param OnlineShop_Framework_ProductList $productList current product list (with all set filters) to get available options and counts
     * @param $currentFilter current filter for this filter definition
     * @return string view snippet
     */
    public function getFilterFrontend(OnlineShop_Framework_AbstractFilterDefinitionType $filterDefinition, OnlineShop_Framework_ProductList $productList, $currentFilter) {
        return $this->getFilterDefinitionClass($filterDefinition->getType())->getFilterFrontend($filterDefinition, $productList, $currentFilter);
    }

    /**
     * Adds condition - delegates it to the OnlineShop_Framework_FilterService_AbstractFilterType instance
     *
     * @param OnlineShop_Framework_AbstractFilterDefinitionType $filterDefinition
     * @param OnlineShop_Framework_ProductList $productList
     * @param $currentFilter
     * @param $params
     * @param bool $isPrecondition
     * @return array updated currentFilter array
     */
    public function addCondition(OnlineShop_Framework_AbstractFilterDefinitionType $filterDefinition, OnlineShop_Framework_ProductList $productList, $currentFilter, $params, $isPrecondition = false) {
        return $this->getFilterDefinitionClass($filterDefinition->getType())->addCondition($filterDefinition, $productList, $currentFilter, $params, $isPrecondition);
    }

}
