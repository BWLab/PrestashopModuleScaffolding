<?php
include_once _PS_MODULE_DIR_ . 'prestashopmodulescaffolding/class/PrestashopModuleScaffolding.php';
class AdminPrestashopModuleScaffoldingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'prestashop_module_scaffolding';
        $this->className = 'PrestashopModuleScaffolding';
        $this->lang = false;

        $this->identifier = 'id_generation';

        $this->name = 'meta';
        Shop::addTableAssociation($this->table, array('type' => 'shop'));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );

 

        parent::__construct();

    }

    public function loadObject($opt = false)
    {

        return parent::loadObject($opt);

    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function setMedia()
    {
        $this->addCSS(array('/modules/prestashopmodulescaffolding/views/css/main.css'));

        return parent::setMedia();
    }

    public function initToolbar()
    {
        $this->toolbar_btn['generate'] = array(
            'desc' => $this->l('Generate'),
            'href' => self::$currentIndex . '&amp;' . 'action=generate' . '&amp;token=' . $this->token,
        );

        return parent::initToolbar();
    }

    public function postProcess()
    {

        if (Tools::getValue('action') == 'generate') {

         
        }
        parent::postProcess();
    }

   

  
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Title of form')
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id',
                ),

                array(
                    'type' => 'hidden',
                    'name' => 'id_lang',
                ),
            
              
            ),
            'submit' => array(
                'title' => $this->l('    Save   '),
                'class' => 'button'
            )
        );

        return parent::renderForm();
    }
}