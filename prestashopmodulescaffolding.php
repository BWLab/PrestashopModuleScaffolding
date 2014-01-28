<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class PrestashopModuleScaffoldingMain extends Module
{

    /* @var boolean error */
    protected $_errors = false;

    public function __construct()
    {


        $this->name = 'prestashop_module_scaffolding';
        $this->tab = 'others';
        $this->version = '1.0';
        $this->author = ' ... author';
        //$this->module_key = '.....';

        parent::__construct();

        $this->displayName = $this->l('<insert displayed name>');
        $this->description = $this->l('<insert description>');
        $this->confirmUninstall = $this->l('Are you sure?');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestashopmodulescaffolding` (
              `id` int(10) NOT NULL,
			  `id_category` int(10) NOT NULL,
			  `id_lang` int(10) not null,
			  PRIMARY KEY  (`id`)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {

                $this->uninstall();

                return false;
            }
        }

        $this->installTab();

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestashopmodulescaffolding`;';
        

        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return false;
            }
        }

        $this->uninstallTab();

        return true;
    }

    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminPrestashopModuleScaffolding');
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        } else {
            return false;
        }
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminPrestashopModuleScaffolding";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = "Prestashop modules Scaffolding";
        }
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminAdmin');
        $tab->module = $this->name;

        return $tab->add();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPrestashopModuleScaffolding'));
    }

}