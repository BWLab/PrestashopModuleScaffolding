<?php
include_once _PS_MODULE_DIR_ . 'bwlabmetagenerate/class/BwlabMetaGeneration.php';
class AdminBwlabMetaGenerateController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'bwlab_meta_generation';
        $this->className = 'BwlabMetaGeneration';
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

        $this->fields_list = array(
            'id_category' => array(
                'title' => $this->l('category name'),
                'width' => 'auto',
                'callback' => 'getCategoryName',

            ),
            'id_lang' => array(
                'title' => $this->l('language'),
                'width' => 'auto',
                'callback' => 'getLangName',
            ),
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
        $this->addCSS(array('/modules/bwlabmetagenerate/views/css/main.css'));

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

            $shop_id = $this->context->shop->getCurrentShop();

            $sql = "select * from " .
                _DB_PREFIX_ . $this->table . '_shop ' .
                'where id_shop = ' . $shop_id;

            $cats = DB::getInstance()->executeS($sql);


            foreach ($cats as $cat) {

                //configuration
                $meta = new BwlabMetaGeneration($cat['id_generation']);

                //prodotti
                $sql = 'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p ' .
                    'LEFT JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product ' .
                    'WHERE p.id_category_default = ' . $meta->id_category .
                    ' AND p.active = 1 AND ps.id_shop = ' . $shop_id;

                $products = Db::getInstance()->executeS($sql);

                //categoria
                $category = new Category($cat['id_category'], $meta->id_lang);

                foreach ($products as $product) {

                    /**
                     * @var ProductCore $product
                     */
                    $product = new Product(
                        $product['id_product'], false, $meta->id_lang, $shop_id
                    );

                    $title = $this->generazioneMeta(
                        $category,
                        $product,
                        $meta->id_lang,
                        $meta->title
                    );

                    $keywords = $this->generazioneMeta(
                        $category,
                        $product,
                        $meta->id_lang,
                        $meta->meta_keywords
                    );

                    $description = $this->generazioneMeta(
                        $category,
                        $product,
                        $meta->id_lang,
                        $meta->meta_descriptions
                    );

                    $url_rewrite = $this->generazioneMeta(
                        $category,
                        $product,
                        $meta->id_lang,
                        $meta->url_rewrite
                    );

                    $save = false;
                    $fields = array();


                    if (!empty($title)) {
                        $fields['meta_title'] = $title;
                    }

                    if (!empty($description)) {
                        $fields['meta_description'] = $description;
                    }

                    if (!empty($keywords)) {
                        $fields['meta_keywords'] = $keywords;
                    }
                    if (!empty($url_rewrite)) {

                        $fields['link_rewrite'] = Tools::link_rewrite($url_rewrite);
                    }

                    if (count($fields) > 0) {

                            Db::getInstance()->update(
                                'product_lang',
                                $fields,
                                'id_product = ' . $product->id . ' and id_lang = ' . $meta->id_lang . ' and id_shop = ' . $shop_id
                            );


                    }
                }
            }

        }
        parent::postProcess();
    }

    private function generazioneMeta($category, $product, $id_lang, $pattern)
    {
        if (empty($pattern)) {
            return $pattern;
        }

        $result = $pattern;

        $result = $this->matchPattern(
            'product_name',
            $product->name,
            $result
        );

        $result = $this->matchPattern(
            'default_cat_name',
            $category->name,
            $result
        );

        $man = new Manufacturer($product->id_manufacturer);
        $result = $this->matchPattern(
            'manufacturer_name',
            $man->name,
            $result
        );

        $result = $this->matchPattern(
            'product_reference',
            $product->referece,
            $result
        );

        $result = $this->matchPattern(
            'product_price',
            number_format($product->price, 2),
            $result
        );


        $result = str_replace(
            array('{', '}'),
            '',
            $result
        );

        return $result;
    }

    private function matchPattern($pattern, $replace, $string)
    {

        $_pattern = "#" . $pattern . "#";

        if (preg_match($_pattern, $string)) {
            return str_replace($_pattern, $replace, $string);
        }

        return $string;
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Prepare meta of products selected categpry')
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_category',
                ),

                array(
                    'type' => 'hidden',
                    'name' => 'id_lang',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Category name'),
                    'name' => 'catname',
                    'required' => false,
                    'desc' => $this->l('insert model of title'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'required' => true,
                    'desc' => $this->l('insert model of title'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Keywords'),
                    'name' => 'meta_keywords',
                    'required' => true,
                    'desc' => $this->l('insert model of keywords'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Descriptions'),
                    'name' => 'meta_descriptions',
                    'required' => true,
                    'desc' => $this->l('insert model of meta descriptions'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Url Rewrite'),
                    'name' => 'url_rewrite',
                    'required' => true,
                    'desc' => $this->l('insert model of url_rewrite'),
                ),
                array(
                    'type' => 'shop',
                    'label' => $this->l('Shops abbinati'),
                )
            ),
            'submit' => array(
                'title' => $this->l('    Save   '),
                'class' => 'button'
            )
        );

        return parent::renderForm();
    }

    public function getCategoryName($id)
    {
        $c = new CategoryCore($id);

        return $c->getName($this->context->language->id);
    }

    public function getShopName($id)
    {
        $c = new ShopCore($id);

        return $c->name;
    }

    public function getLangName($id)
    {
        $c = new LanguageCore($id);

        return $c->name;
    }
}