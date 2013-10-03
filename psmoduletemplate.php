<?php
/*
*  @author Carles San Agustin <hello@carlessanagustin.com>
*  @copyright  2013 carlessanagustin.com
*  @license    http://opensource.org/licenses/MIT - MIT License
*/

if (!defined('_PS_VERSION_'))
	exit;

class psModuleTemplate extends Module
{
	const SCHEMA_CREATE_SQL = 'data/schema_create';
	const SCHEMA_DROP_SQL = 'data/schema_drop';
	const CONFIG_XML = 'data/config.xml';
	
	protected $xml;
	
	public $input;
	public $textarea1;
	public $textarea2;
	private $_postErrors = array();
	
	public function __construct()
	{
	$this->name = 'psmoduletemplate';
	$this->version = '0.1';
	$this->tab = 'others';
	$this->author = "Carles San Agustin";
	$this->url = "http://www.carlessanagustin.com";
	$this->email = "hello@carlessanagustin.com";
	$this->year = '2013';
	$this->module_key = 'sp8l7hw860z2411zcq1cdyrzm044asfc';
	$this->ps_versions_compliancy['min'] = '1.5.0.1';
	$this->need_instance = 0;
	
	parent::__construct();
	
	$this->displayName = $this->l('PS Module Template block module');
	$this->description = $this->l('Adds my own PS Module Template.');
	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	
	$this->xml = simplexml_load_file(dirname(__FILE__).'/'.self::CONFIG_XML);
 
    if (!Configuration::get('MYMODULE_NAME'))       
      $this->warning = $this->l(' NO NAME ??!! ');
	}

    /*
	 * INSTALL ********************************************
	 */

	public function install()
	{
		
	if (Shop::isFeatureActive())
	  Shop::setContext(Shop::CONTEXT_ALL);
		 

	return ( parent::install() && $this->_installDependences() );
	}
	protected function _installDependences()
    {

        if ( !$this->_installHooks() )
            return false;

        if ( !$this->_runSqlFile(self::SCHEMA_CREATE_SQL.'.sql') )
            return false;

        if ( !$this->_installConfiguration() )
            return false;

        return true;
    }
	
    protected function _installHooks()
    {

	foreach ($this->xml->hooks as $hooks)
    	foreach ($hooks as $hook)
        {
			if ( !$this->registerHook((string)$hook) )
				return false;
		}	
		return true;
	}
	
    protected function _installConfiguration() 
    {	
/*   
	if ( !Configuration::updateValue('MYMODULE_NAME', 'carlessanagustin.com') )
		return false; 
		
	return true;
*/

        foreach ($this->xml->confs->conf as $conf){
            Configuration::updateValue((string)$conf->name, (string)$conf->value);
			//print_r(' creating... '); print_r((string)$conf->name);
		}

        return true;

    }
	
    /*
	 * UNINSTALL ********************************************
	 */
	public function uninstallOFF()
	{
	if (!parent::uninstall() || 
	!Configuration::deleteByName('MYMODULE_NAME') || 
	!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'psmoduletemplate`'))
		return false; 
		
	return true;
	}
	
    public function uninstall()
    {
    	return ( parent::uninstall() && $this->_uninstallDependences() );
    }

    protected function _uninstallDependences()
    {
        if ( !$this->_runSqlFile(self::SCHEMA_DROP_SQL.'.sql') )
            return false;
			
		if ( !$this->_uninstallConfiguration() )
            return false;
        return true;
    }
	
    protected function _uninstallConfiguration()
    {
/*    	
	if ( !Configuration::deleteByName('MYMODULE_NAME') )
		return false; 
		
	return true;
*/

        foreach ($this->xml->confs->conf as $conf){
            Configuration::deleteByName((string)$conf->name);
			//print_r(' deleting... '); print_r((string)$conf->name);
		}
        return true;

    }

    /*
	 * SQL ********************************************
	 */
	 	
    protected function _runSqlFile($fileName)
    {
        $inputFile = dirname(__FILE__).'/'.$fileName;
        $query = '';

        if (($fdi = fopen($inputFile, 'r')) === false)
            return false;
        while (($line = fgets($fdi)) !== false)
            $query .= $line;

        $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, $query);

        if (!Db::getInstance()->Execute($query))
            return false;

        return true;
    }	
	
    /*
	 * HOOKS ********************************************
	 */
	public function hookDisplayHome($params)
	{
		/* {$link->getModuleLink('favoriteproducts', 'default')} == "?module=favoriteproducts&controller=default" */
		/* module + controller: psmoduletemplate.php or display.php */
	    $this->context->smarty->assign(
	        array(
	            'my_module_name' => Configuration::get('NAME'),
	            'my_module_linkHome' => $this->context->link->getModuleLink('psmoduletemplate', 'psmoduletemplateHome'),
	            'my_module_message' => $this->l('You are in hookDisplayHome')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-home-hook.tpl');
	}
		
	public function hookDisplayLeftColumn($params)
	{
		/* {$link->getModuleLink('favoriteproducts', 'default')} == "?module=favoriteproducts&controller=default" */
		/* module + controller: psmoduletemplate.php or display.php */
	    $this->context->smarty->assign(
	        array(
	            'my_module_name' => Configuration::get('NAME'),
	            'my_module_link' => $this->context->link->getModuleLink('psmoduletemplate', 'psmoduletemplate'), 
	            'my_module_message' => $this->l('You are in hookDisplayLeftColumn')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-hook.tpl');
	}
	
	public function hookDisplayRightColumn($params)
	{
		/* {$link->getModuleLink('favoriteproducts', 'default')} == "?module=favoriteproducts&controller=default" */
		/* module + controller: psmoduletemplate.php or display.php */
	    $this->context->smarty->assign(
	        array(
	            'my_module_name' => Configuration::get('NAME'),
	            'my_module_link' => $this->context->link->getModuleLink('psmoduletemplate', 'display'), 
	            'my_module_message' => $this->l('You are in hookDisplayRightColumn')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-hook.tpl');
		
		/* for hook left = hook right */
		//return $this->hookDisplayLeftColumn($params);
	}
	  
	public function hookDisplayHeader()
	{
		if ( !$this->context->controller->addCSS($this->_path.'css/psmoduletemplate.css', 'all') || !$this->context->controller->addJS (($this->_path). 'js/psmoduletemplate.js')  )
			return false;
		return true;
	}

	// back-office: Add a new feature
	public function hookDisplayFeatureForm()
	{
	    $this->context->smarty->assign(
	        array(
	            'my_module_message' => $this->l('You are in hookDisplayFeatureForm')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-default-hook.tpl');
	}
	// back-office: Add a new feature value
	public function hookDisplayFeatureValueForm()
	{
	    $this->context->smarty->assign(
	        array(
	            'my_module_message' => $this->l('You are in hookDisplayFeatureValueForm')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-default-hook.tpl');
	}

	// back-office: Catalog  > Attributes and Values  > Add New Values
	public function hookDisplayAttributeForm()
	{
	    $this->context->smarty->assign(
	        array(
	            'my_module_message' => $this->l('You are in hookDisplayAttributeForm')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-default-hook.tpl');
	}	
	
	// back-office: Catalog  > Attributes and Values  > Add New Attributes
	public function hookDisplayAttributeGroupForm()
	{
	    $this->context->smarty->assign(
	        array(
	            'my_module_message' => $this->l('You are in hookDisplayAttributeGroupForm')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-default-hook.tpl');
	}	

	public function hookDisplayAdminProductsExtra()
	{
	    $this->context->smarty->assign(
	        array(
	            'my_module_message' => $this->l('You are in hookDisplayAdminProductsExtra')
	        )
	    );
	    return $this->display(__FILE__, 'psmoduletemplate-productTab-hook.tpl');
	}







	
	

	
	
	
	
	
	
	
	
	
	
	/**
	 * ***************************************************************
	 *
	 */	

	public function getContentOFF()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= '<div class="alert error">'.$err.'</div>';
		}
		else
			$this->_html .= '<br />';

		$this->_displayIntroText();
		$this->_displayForm();

		return $this->_html;
	}

	private function _displayFormOFF()
	{
		$this->_html .=
		'<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
			<legend><img src="#" />'.$this->l('PS Module Template Configuration').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('here instructions or introduction').'<br /><br /></td></tr>
					<tr><td width="130" style="height: 35px;">'.$this->l('input').'</td><td><input type="text" name="input" value="'./*htmlentities(Tools::getValue('input', $this->input), ENT_COMPAT, 'UTF-8').*/'" style="width: 300px;" /></td></tr>
					<tr>
						<td width="130" style="vertical-align: top;">'.$this->l('textarea 1').'</td>
						<td style="padding-bottom:15px;">
							<textarea name="textarea1" rows="4" cols="53">'./*htmlentities(Tools::getValue('textarea1', $this->textarea1), ENT_COMPAT, 'UTF-8').*/'</textarea>
							<p>'.$this->l('caption here').'</p>
						</td>
					</tr>
					<tr>
						<td width="130" style="vertical-align: top;">'.$this->l('textarea 2').'</td>
						<td style="padding-bottom:15px;">
							<textarea name="textarea2" rows="4" cols="53">'./*htmlentities(Tools::getValue('textarea2', $this->textarea2), ENT_COMPAT, 'UTF-8').*/'</textarea>
							<p>'.$this->l('caption here').'</p>
						</td>
					</tr>
					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}
	private function _displayIntroText()
	{
		$this->_html .= '<img src="#" style="float:left; margin-right:15px;"><b>
		'.$this->l('Lorem ipsum dolor sit amet, consectetur.').'</b><br />
		'.$this->l('Lorem ipsum dolor sit amet, consectetur.').'<br /><br />';
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			if (!Tools::getValue('details'))
				$this->_postErrors[] = $this->l('Account details are required.');
			elseif (!Tools::getValue('owner'))
				$this->_postErrors[] = $this->l('Account owner is required.');
		}
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('MYMODULE_INPUT', Tools::getValue('input'));
			Configuration::updateValue('MYMODULE_TEXTAREA1', Tools::getValue('textarea1'));
			Configuration::updateValue('MYMODULE_TEXTAREA2', Tools::getValue('textarea2'));
		}
		$this->_html .= '<div class="conf confirm"> '.$this->l('Settings updated').'</div>';
	}
	
	/**
	 * ***************************************************************
	 *		switch: getContentOFF + _displayFormOFF
	 */	
	public function getContent()
	{
	    $output = null;
	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	        $my_module_name = strval(Tools::getValue('MYMODULE_NAME'));
	        if (!$my_module_name  || empty($my_module_name) || !Validate::isGenericName($my_module_name))
	            $output .= $this->displayError( $this->l('Invalid Configuration value') );
	        else
	        {
	            Configuration::updateValue('MYMODULE_NAME', $my_module_name); 
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    return $output.$this->_displayForm();
	}
	private function _displayForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('Configuration value'),
	                'name' => 'MYMODULE_NAME',
	                'size' => 20,
	                'required' => true
	            )
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
	    
	    return $helper->generateForm($fields_form);
	}


	
}















