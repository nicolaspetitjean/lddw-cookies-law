<?php
/**
 * 2018 http://www.la-dame-du-web.com
 *
 * @author    Nicolas PETITJEAN <n.petitjean@la-dame-du-web.com>
 * @copyright 2018 Nicolas PETITJEAN
 * @license MIT License
 */

if(!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classloader.php');

class lddw_cookieslaw extends Module
{
    private $fields;
    private static $cookie_name = 'cookie_notice_accepted';

    public function __construct()
    {
        $this->name = 'lddw_cookieslaw';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Nicolas PETITJEAN';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cookies Law');
        $this->description = $this->l('European Union Cookies Law.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->buildFields();
    }

    public function getFields()
    {
        return $this->fields;
    }

    private function buildFields()
    {
        $this->fields = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input'  => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Title'),
                    'name'     => 'LDDW_CL_TITLE',
                    'required' => true,
                    'lang'     => true,
                ),
                array(
                    'type'     => 'textarea',
                    'label'    => $this->l('Notification message'),
                    'name'     => 'LDDW_CL_MESSAGE',
                    'required' => true,
                    'lang'     => true,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Button text'),
                    'name'     => 'LDDW_CL_TXT_BUTTON',
                    'required' => true,
                    'lang'     => true,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Button text more'),
                    'name'     => 'LDDW_CL_TXTMORE_BUTTON',
                    'required' => true,
                    'lang'     => true,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Button text more link'),
                    'name'     => 'LDDW_CL_LINK',
                    'required' => true,
                    'lang'     => true,
                ),
                array(
                    'type'     => 'select',
                    'desc'     => 'Specify cookie lifetime.',
                    'label'    => $this->l('Cookie expiry'),
                    'name'     => 'LDDW_CL_EXPIRY',
                    'required' => true,
                    'lang'     => false,
                    'options'  => array(
                        'query' => array(
                            array('id' => 'day', 'name' => $this->l('1 day')),
                            array('id' => 'week', 'name' => $this->l('1 week')),
                            array('id' => 'month', 'name' => $this->l('1 month')),
                            array('id' => 'year', 'name' => $this->l('1 year'))
                        ),
                        'id'    => 'id',
                        'name'  => 'name',
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
    }

    public function install()
    {
        if(Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return
            parent::install() &&
            $this->registerHook(array(
                'displayFooter',
                'header'
            ));
    }

    public function uninstall()
    {
        if(!parent::uninstall()
            || !Configuration::deleteByName('LDDW_CL_MESSAGE')
            || !Configuration::deleteByName('LDDW_CL_TITLE')
            || !Configuration::deleteByName('LDDW_CL_TXT_BUTTON')
            || !Configuration::deleteByName('LDDW_CL_TXTMORE_BUTTON')
            || !Configuration::deleteByName('LDDW_CL_LINK')
            || !Configuration::deleteByName('LDDW_CL_EXPIRY')
        ) {
            return false;
        }

        return true;
    }

    public function getConfigValues()
    {
        $fields = $this->getFields();
        $return = [];
        foreach($fields['input'] as $field) {
            if($this->isFieldMultilang($field)) {
                $return[$field['name']] = LddwHelper::getConfigMultilang($field['name']);
            } else {
                $return[$field['name']] = Configuration::get($field['name']);
            }
        }

        return $return;
    }

    public function getSubmitedValues()
    {
        $fields = $this->getFields();
        $return = [];
        foreach($fields['input'] as $field) {
            if($this->isFieldMultilang($field)) {
                $return[$field['name']] = LddwHelper::getValueMultilang($field['name']);
            } else {
                $return[$field['name']] = Tools::getValue($field['name'], Configuration::get($field['name']));
            }
        }

        return $return;
    }

    public function isFieldMultilang($field)
    {
        return isset($field['lang']) && $field['lang'] ? true : false;
    }

    public function hookHeader()
    {
        $this->context->controller->registerJavascript('modules-js-lddw-cookies-law', 'modules/' . $this->name . '/js/lddw_cookieslaw.js', ['media' => 'all', 'priority' => 150]);
        $this->context->controller->registerStylesheet('modules-css-lddw-cookies-law', 'modules/' . $this->name . '/css/lddw_cookieslaw.css', ['media' => 'all', 'priority' => 150]);
    }

    public function getContent()
    {
        $output = null;

        if(Tools::isSubmit('submit' . $this->name)) {
            $errors = array();
            $submitedValues = $this->getSubmitedValues();

            // Validate message
            $LDDW_CL_MESSAGE = $submitedValues['LDDW_CL_MESSAGE'];
            if(!LddwHelper::validateNotEmpty($LDDW_CL_MESSAGE)) {
                $errors[] = $this->l('Message can\'t be empty.');
            }

            // Validate title
            $LDDW_CL_TITLE = $submitedValues['LDDW_CL_TITLE'];
            if(!LddwHelper::validateNotEmpty($LDDW_CL_TITLE)) {
                $errors[] = $this->l('Title can\'t be empty.');
            }

            // Validate Expiry
            $LDDW_CL_EXPIRY = $submitedValues['LDDW_CL_EXPIRY'];
            $correct_values = array('day', 'week', 'month', 'year');
            if(!in_array($LDDW_CL_EXPIRY, $correct_values)) {
                $errors[] = $this->l('Cookie lifetime value is not correct.');
            }

            // Update.. or not !
            if(empty($errors)) {
                foreach($submitedValues as $submitedKey => $submitedValue) {
                    Configuration::updateValue($submitedKey, $submitedValue);
                }
                $output = $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output = $this->displayError(implode("<br />", $errors));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = $this->fields;

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigValues(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id
        );

        return $helper->generateForm($fields_form);
    }

    public function hookDisplayFooter()
    {
        $id_lang = $this->context->language->id;
        $configValues = $this->getConfigValues();
        $parsed_url = parse_url($this->context->shop->getBaseURL());
        $this->smarty->assign(array(
            'title' => $configValues['LDDW_CL_TITLE'][$id_lang],
            'message' => $configValues['LDDW_CL_MESSAGE'][$id_lang],
            'text_button' => $configValues['LDDW_CL_TXT_BUTTON'][$id_lang],
            'text_more' => $configValues['LDDW_CL_TXTMORE_BUTTON'][$id_lang],
            'url' => filter_var($configValues['LDDW_CL_LINK'][$id_lang], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED),
            'expiry' => $configValues['LDDW_CL_EXPIRY'],
            'domain' => $parsed_url['host'],
            'cookie_setted' => $this->cookie_setted(),
        ));

        return $this->display(
            __FILE__,
            './views/templates/hook/footer.tpl');
    }

    /**
     * Checks if cookie is setted
     */
    public function cookie_setted()
    {
        return isset($_COOKIE[self::$cookie_name]);
    }
}