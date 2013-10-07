<?php
/*
*  @author Carles San Agustin <hello@carlessanagustin.com>
*  @copyright  2013 carlessanagustin.com
*  @license    http://opensource.org/licenses/MIT - MIT License
*/

class ProductAccess extends ObjectModel
{
    public         $int_field;
    public         $date_field;
    public         $active;
    public         $string_field;
    public         $html_field;

    public static $definition = array(
        'table' => 'product_access',
        'primary' => 'id_product_access',
        'multilang' => true,
        'multishop' => false,
        'multilang_shop' => false,
        'fields' => array(
            'int_field' => array(
                'type' => ObjectModel::TYPE_INT,
				'required' => false,
                'validate' => 'isUnsignedInt',
                'lang' => false
            ),
            'date_field' => array(
                'type' => ObjectModel::TYPE_DATE,
                'required' => false
            ),
            'active' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'required' => false
            ),
			'string_field' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false,
                'validate' => 'isCatalogName',
                'lang' => false,   
                'size' => 128
            ),
			'html_field' => array(
            	'type' => ObjectModel::TYPE_HTML,
            	'required' => false,
            	'validate' => 'isString',
            	'lang' => false,
            	'size' => 3999999999999),
        )
    );

	public function __construct($id = NULL, $id_lang = NULL)
	{}
	public function add($autodate = true, $nullValues = false)
	{}
	public function associateTo(/*integer|array $id_shops*/)
	{}
	public function delete()
	{}
	public function deleteImage(/*mixed $force_delete = false*/)
	{}	
	public function deleteSelection($selection)
	{}
	public function getFields()
	{}
	public function getValidationRules($className = _CLASS_)
	{}
	public function save($nullValues = false, $autodate = true)
	{}
	public function toggleStatus()
	{}
	public function update($nullValues = false)
	{}
	public function validateFields($die = true, $errorReturn = false)
	{}







} /* END class */
