<?php
include_once _PS_CLASS_DIR_.'ObjectModel.php';

class PrestashopModuleScaffolding extends ObjectModel
{
    public $id;
    public $id_category;
    public $id_lang;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'prestashopmodulescaffolding',
        'primary' => 'id',
        'fields' => array(
            'id' => array(
                'type' => self::TYPE_INT,
                'required' => true
            ),
            'id_category' => array(
                'type' => self::TYPE_INT,
                'required' => false
            ),
            'id_lang' => array(
                'type' => self::TYPE_INT,
                'required' => true
            ),

        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
//        Shop::addTableAssociation(PrestashopModuleScaffolding::$definition['table'], array('type'=>'shop'));
        parent::__construct($id, $id_lang, $id_shop);

    }
}