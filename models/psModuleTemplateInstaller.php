<?php

/*
*  @author Carles San Agustin <hello@carlessanagustin.com>
*  @copyright  2013 carlessanagustin.com
*  @license    http://opensource.org/licenses/MIT - MIT License
*/

class myModuleInstaller
{
    const SQL_SCHEMA_PATH = 'data/sql/schema';
    /* const SQL_UPDATE_PATH = 'data/sql/update_'; */
    const CONF_XML_PATH = 'data/configuration.xml';

    protected static $instance = null;

    protected $xml;
    protected $mI;

    public static function getInstance($moduleInstance)
    {
        if (self::$instance === null)
            self::$instance = new myModuleInstaller($moduleInstance);
        return self::$instance;
    }

    public function __construct($moduleInstance)
    {
        $this->xml = simplexml_load_file(dirname(__FILE__).'/../'.self::CONF_XML_PATH);
        $this->mI = $moduleInstance;
    }

    ################
    # INSTALLATION #
    ################

    public function install()
    {
        // Add hooks values
        if ($this->_installHooks() !== true)
            return false;
/*
        // Add SQL schema
        if ($this->_installSql() !== true)
            return false;

        // Add configuration values
        if ($this->_installConfiguration() !== true)
            return false;

        // Add admin tab
        if ($this->_installTab() !== true)
            return false;

        // Add metas infos
        if ($this->_installMetas() !== true)
            return false;

        // Add quick access
        if ($this->_installQuickAccess() !== true)
            return false;

        // Add top menu links
        $this->_installTopmenuLinks();
*/
        return true;
    }

    protected function _installHooks()
    {
	  $this->registerHook('header') &&
	  $this->registerHook('leftColumn') &&
	  $this->registerHook('rightColumn');
}

    protected function _installSql()
    {
        // Detect previous version
        $previousVersion = strval(Configuration::get($this->mI->prefixConfiguration.'VERSION'));

        // SQL creation
        if ($previousVersion && $previousVersion != $this->mI->version)
        {
            // Previous version detected, run migrations
            if ($this->_installSqlUpdate($previousVersion) !== true)
                return false;
        }
        elseif (!$previousVersion)
        {
            // First install, create SQL schema
            if ($this->_installSqlCreate() !== true)
                return false;

            // SQL datas: insert value for each insert found in XML configuration
            foreach ($this->xml->sql->insert as $sql)
            {
                // Replace DB prefix
                $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, (string)$sql);
                // Execute SQL request
                 if (!Db::getInstance()->Execute($query))
                     return false;
            }
        }

        return true;
    }

    protected function _installSqlCreate()
    {
        if (!$this->_installSqlFile(self::SQL_SCHEMA_PATH.'.sql'))
            return false;
        return true;
    }

    protected function _installSqlUpdate($previousVersion)
    {
        $versionA = explode('.', $previousVersion);
        $versionB = explode('.', $this->mI->version);
        $versionA[0] = intval($versionA[0]);
        $versionA[1] = intval($versionA[1]);
        $versionB[0] = intval($versionB[0]);
        $versionB[1] = intval($versionB[1]);

        for ($i = $versionA[0]; $i <= $versionB[0]; $i++)
        {
            if ($i > $versionA[0])
            {
                $startJ = 0;
                $fileName = self::SQL_UPDATE_PATH.($i - 1).'.'.$j.'_'.$i.'.'.$startJ.'.sql';
                if (file_exists(dirname(__FILE__).'/../'.$fileName))
                {
                    if ($this->_installSqlFile($fileName) !== true)
                        return false;
                }
            } else
                $startJ = $versionA[1];
            $endJ = ($i == $versionB[0] ? $versionB[1] : 9);
            for ($j = $startJ; $j < $endJ; $j++)
            {
                $fileName = self::SQL_UPDATE_PATH.$i.'.'.$j.'_'.$i.'.'.($j + 1).'.sql';
                if (file_exists(dirname(__FILE__).'/../'.$fileName))
                {
                    if ($this->_installSqlFile($fileName) !== true)
                        return false;
                }
                else
                    continue;
            }
        }
        return true;
    }

    private function _installSqlFile($fileName)
    {
        $inputFile = dirname(__FILE__).'/../'.$fileName;
        $query = '';

        // Open & read input
        if (($fdi = fopen($inputFile, 'r')) === false)
            return false;
        while (($line = fgets($fdi)) !== false)
            $query .= $line;

        // Replace DB prefix
        $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, $query);

        // Execute SQL request
        if (!Db::getInstance()->Execute($query))
            return false;

        return true;
    }

    protected function _installConfiguration()
    {
        // Update value for each configuration found in XML configuration
        foreach ($this->xml->confs->conf as $conf)
            Configuration::updateValue($this->mI->prefixConfiguration.(string)$conf->name, (string)$conf->value);

        // Add module version
        Configuration::updateValue($this->mI->prefixConfiguration.'VERSION', $this->mI->version);

        return true;
    }

    protected function _installTab()
    {
        $mainIdTab = 0;
        $i = 0;

        // Create tab for each tab found in XML configuration
        foreach ($this->xml->tabs->tab as $tab)
        {
            $newTab = new Tab();
            $i18n = array();
            foreach ($tab->langs->lang as $lang)
                $i18n[(int)Language::getIdByIso((string)$lang['iso'])] = (string)$lang;
            foreach (Language::getLanguages(false /* active */) as $lang)
                $newTab->name[$lang['id_lang']] = array_key_exists($lang['id_lang'], $i18n) ? $i18n[$lang['id_lang']] : current($i18n);
            $newTab->class_name = (string)$tab->class;
            $newTab->id_parent = (int)$tab['main'] ? 0 : $mainIdTab;
            $newTab->module = $this->mI->name;
            $newTab->add();

            if ((int)$tab['main'])
            {
                $mainIdTab = $newTab->id;
                if ((int)$tab['first'])
                {
                    $currentTabs = Tab::getTabs(1 /* id_lang */, $newTab->id_parent);
                    for ($i = count($currentTabs); $i; $i--)
                        $newTab->updatePosition(0 /* way */, $i - 1 /* position */);
                }
            }
        }
        return true;
    }

    protected function _installMetas()
    {
        // Create tab for each tab found in XML configuration
        foreach ($this->xml->metas->meta as $meta)
        {
            $newMeta = new Meta();
            $i18n = array();
            foreach ($meta->langs->lang as $lang)
                $i18n[(int)Language::getIdByIso((string)$lang['iso'])] = $lang;
            foreach (Language::getLanguages(false /* active */) as $lang)
            {
                $idLang = array_key_exists($lang['id_lang'], $i18n) ? $lang['id_lang'] : 1;
                $newMeta->title[$lang['id_lang']] = (string)$i18n[$idLang]->title;
                $newMeta->description[$lang['id_lang']] = (string)$i18n[$idLang]->description;
                $newMeta->url_rewrite[$lang['id_lang']] = (string)$i18n[$idLang]->url_rewrite;
            }
            $newMeta->page = 'module-'.$this->mI->name.'-'.(string)$meta->page;
            $newMeta->add();
        }
        return true;
    }

    protected function _installQuickAccess()
    {
        // Create quick access for each quick access found in XML configuration
        foreach ($this->xml->quicks->quick as $quick)
        {
            $newQuick = new QuickAccess();
            $i18n = array();
            foreach ($quick->langs->lang as $lang)
                $i18n[(int)Language::getIdByIso((string)$lang['iso'])] = (string)$lang;
            foreach (Language::getLanguages(false /* active */) as $lang)
                $newQuick->name[$lang['id_lang']] = array_key_exists($lang['id_lang'], $i18n) ? $i18n[$lang['id_lang']] : current($i18n);
            $newQuick->link = (string)$quick->link;
            $newQuick->new_window = (int)$quick['blank'];
            $newQuick->add();
        }
        return true;
    }

    protected function _installTopmenuLinks()
    {
        // Check if blocktopmenu is available
        $moduleInstance = Module::getInstanceByName('blocktopmenu');
        if (!Validate::isLoadedObject($moduleInstance) || !class_exists('MenuTopLinks'))
            return ;

        // Create links for each top menu links found in XML configuration
        foreach ($this->xml->topmenu->link as $toplink)
        {
            $i18n = array();
            $link = array();
            $label = array();
            foreach ($toplink->langs->lang as $lang)
                $i18n[(int)Language::getIdByIso((string)$lang['iso'])] = (string)$lang;
            foreach (Language::getLanguages(false /* active */) as $lang)
            {
                $link[$lang['id_lang']] = Context::getContext()->link->getModuleLink('prestavod', (string)$toplink->controller, array() /* params */, false /* SSL */, $lang['id_lang']);
                $label[$lang['id_lang']] = array_key_exists($lang['id_lang'], $i18n) ? $i18n[$lang['id_lang']] : $i18n[1];
            }
            MenuTopLinks::add($link, $label, 0 /* newWindow */, Context::getContext()->shop->id);
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT max(`id_linksmenutop`) as `id` FROM `'._DB_PREFIX_.'linksmenutop`');
            if (is_array($row) && array_key_exists('id', $row))
                Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', Configuration::get('MOD_BLOCKTOPMENU_ITEMS').',LNK'.(int)$row['id']);
        }
        return true;
    }

    #############
    # UNINSTALL #
    #############

    public function uninstall()
    {
        // Delete configuration values
        if ($this->_uninstallConfiguration() !== true)
            return false;

        // Delete admin tab
        if ($this->_uninstallTab() !== true)
            return false;

        // Delete metas infos
        if ($this->_uninstallMetas() !== true)
            return false;

        // Delete quick access
        if ($this->_uninstallQuickAccess() !== true)
            return false;

        // Delete top menu links
        if ($this->_uninstallTopmenuLinks() !== true)
            return false;

        return true;
    }

    protected function _uninstallConfiguration()
    {
        // Delete each configuration found in XML configuration
        foreach ($this->xml->confs->conf as $conf)
            Configuration::deleteByName($this->mI->prefixConfiguration.(string)$conf->name);
        return true;
    }

    protected function _uninstallTab()
    {
        // Delete tab for each tab found in XML configuration
        foreach ($this->xml->tabs->tab as $tab)
        {
            $tab = new Tab((int)Tab::getIdFromClassName((string)$tab->class));
            $tab->delete();
        }
        return true;
    }

    protected function _uninstallMetas()
    {
        // Delete meta for each meta found in XML configuration
        foreach ($this->xml->metas->meta as $meta)
        {
            $query = '
            SELECT `id_meta`
            FROM `'._DB_PREFIX_.Meta::$definition['table'].'`
            WHERE `page` = \'module-'.$this->mI->name.'-'.pSql((string)$meta->page).'\'';
            if ($rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query))
            {
                $metas = ObjectModel::hydrateCollection('Meta', $rows);
                if ($metas && is_array($metas))
                    foreach ($metas as $mt)
                        $mt->delete();
            }
        }
        return true;
    }

    protected function _uninstallQuickAccess()
    {
        // Delete quick access for each quick access found in XML configuration
        foreach ($this->xml->quicks->quick as $quick)
        {
            $query = '
            SELECT `id_quick_access`
            FROM `'._DB_PREFIX_.QuickAccess::$definition['table'].'`
            WHERE `link` = \''.pSql((string)$quick->link).'\'';
            if ($rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query))
            {
                $quicks = ObjectModel::hydrateCollection('QuickAccess', $rows);
                if ($quicks && is_array($quicks))
                    foreach ($quicks as $qk)
                        $qk->delete();
            }
        }
        return true;
    }

    protected function _uninstallTopmenuLinks()
    {
        // Check if blocktopmenu is available
        $moduleInstance = Module::getInstanceByName('blocktopmenu');
        if (!Validate::isLoadedObject($moduleInstance) || !class_exists('MenuTopLinks'))
            return ;

        // Delete links for each top menu links found in XML configuration
        foreach ($this->xml->topmenu->link as $toplink)
        {
            foreach (Language::getLanguages(false /* active */) as $lang)
            {
                $link = Context::getContext()->link->getModuleLink('prestavod', (string)$toplink->controller, array() /* params */, false /* SSL */, $lang['id_lang']);
                $query = '
                SELECT l.`id_linksmenutop`
                FROM `'._DB_PREFIX_.'linksmenutop` l
                LEFT JOIN `'._DB_PREFIX_.'linksmenutop_lang` ll ON (l.`id_linksmenutop` = ll.`id_linksmenutop` AND ll.`id_lang` = '.(int)$lang['id_lang'].')
                WHERE ll.`link` = \''.pSql($link).'\'';
                if ($rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query))
                    foreach ($rows as $link)
                    {
                        MenuTopLinks::remove($link['id_linksmenutop'], Context::getContext()->shop->id);
                        $newConf = str_replace(',LNK'.(int)$link['id_linksmenutop'], '', Configuration::get('MOD_BLOCKTOPMENU_ITEMS'));
                        Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $newConf);
                    }
            }
        }
        return true;
    }
}

