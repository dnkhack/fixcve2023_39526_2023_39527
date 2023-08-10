<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fixcve2023_39526_2023_39527 extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fixcve2023_39526_2023_39527';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'DNK Soft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CVE-2023-39526 CVE-2023-39527');
        $this->description = $this->l('CVE-2023-39526 CVE-2023-39527');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() && $this->applayPatch();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->removePatch();
    }

    public function applayPatch(){
        $file = _PS_CORE_DIR_ . '/classes/Validate.php';
        $bfile = _PS_CORE_DIR_ . '/classes/Validate_cve2023_39527.php.backup';
        $content = file_get_contents($file);
        if ($content !== false && file_put_contents($bfile, $content) !== false) {
            $search  = '$events .= \'|onselectstart|onstart|onstop|onanimationcancel|onanimationend|onanimationiteration|onanimationstart\';';
            $replace = $search . "\n        " . '$events .= \'|onpointerover|onpointerenter|onpointerdown|onpointermove|onpointerup|onpointerout|onpointerleave|onpointercancel|ongotpointercapture|onlostpointercapture\';';
            $content = str_replace($search, $replace, $content);
            if (file_put_contents($file,$content) === false) {
                return false;
            }
        }

        $file = _PS_CORE_DIR_ . '/classes/RequestSql.php';
        $bfile = _PS_CORE_DIR_ . '/classes/RequestSql_cve2023_39526.php.backup';
        $content = file_get_contents($file);
        if ($content !== false && file_put_contents($bfile, $content) !== false) {
            $search  = "'SQL_SMALL_RESULT', 'SQL_BIG_RESULT', 'QUICK', 'SQL_BUFFER_RESULT', 'SQL_CACHE', 'SQL_NO_CACHE', 'SQL_CALC_FOUND_ROWS', 'WITH',";
            $replace = $search . "\n            'OUTFILE', 'DUMPFILE',";
            $content = str_replace($search, $replace, $content);
            if (file_put_contents($file,$content) === false) {
                return false;
            }
        }

        $file = _PS_CORE_DIR_ . '/classes/db/Db.php';
        $bfile = _PS_CORE_DIR_ . '/classes/db/Db_cve2023_39526.php.backup';
        $content = file_get_contents($file);
        if ($content !== false && file_put_contents($bfile, $content) !== false) {
            $search  = 'if (!preg_match(\'#^\\s*\\(?\\s*(select|show|explain|describe|desc)\\s#i\', $sql)) {';
            if (strpos($content, $search) === false) {
                $search  = 'if (!preg_match(\'#^\\s*\\(?\\s*(select|show|explain|describe|desc|checksum)\\s#i\', $sql)) {';
            }
            $replace = 'if (
            !preg_match(\'#^\\s*\\(?\\s*(select|show|explain|describe|desc|checksum)\\s#i\', $sql)
            || stripos($sql, \'outfile\') !== false
            || stripos($sql, \'dumpfile\') !== false
        ) {';
            $content = str_replace($search, $replace, $content);
            if (file_put_contents($file,$content) === false) {
                return false;
            }
        }

        return true;
    }

    public function removePatch()
    {
        $file = _PS_CORE_DIR_ . '/classes/Validate.php';
        $bfile = _PS_CORE_DIR_ . '/classes/Validate_cve2023_39527.php.backup';
        $content = file_get_contents($bfile);
        if ($content !== false) {
            if (file_put_contents($file, $content) === false) {
                return false;
            } else {
                unlink($bfile);
            }
        }

        $file = _PS_CORE_DIR_ . '/classes/RequestSql.php';
        $bfile = _PS_CORE_DIR_ . '/classes/RequestSql_cve2023_39526.php.backup';
        $content = file_get_contents($bfile);
        if ($content !== false) {
            if (file_put_contents($file, $content) === false) {
                return false;
            } else {
                unlink($bfile);
            }
        }

        $file = _PS_CORE_DIR_ . '/classes/db/Db.php';
        $bfile = _PS_CORE_DIR_ . '/classes/db/Db_cve2023_39526.php.backup';
        $content = file_get_contents($bfile);
        if ($content !== false) {
            if (file_put_contents($file, $content) === false) {
                return false;
            } else {
                unlink($bfile);
            }
        }
        return true;
    }
}
