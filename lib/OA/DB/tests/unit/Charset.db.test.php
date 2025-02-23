<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

require_once RV_PATH . '/lib/RV.php';

require_once MAX_PATH . '/lib/OA.php';
require_once MAX_PATH . '/lib/OA/DB/Charset.php';

/**
 * A class for testing the OA_DB_Charset class.
 *
 * @package    OpenXDB
 * @subpackage TestSuite
 */
class Test_OA_DB_Charset extends UnitTestCase
{
    /**
     * The constructor method.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function testMySQL()
    {
        $oDbh = OA_DB::singleton();

        if ($oDbh->dbsyntax !== 'mysql' && $oDbh->dbsyntax !== 'mysqli') {
            return;
        }

        $oDbc = OA_DB_Charset::factory($oDbh);
        $this->assertTrue($oDbc);

        $aVersion = $oDbh->getServerVersion();
        if (version_compare($aVersion['native'], '4.1.2', '<')) {
            return;
        }

        $this->assertTrue($oDbc->oDbh);

        //TODO: set the charset during database creation.
        //      Currently the character_set_server from my.ini
        //      is used as default during creation and the result
        //      of getDatabaseCharset() also depends on that value.
        //$this->assertEqual($oDbc->getDatabaseCharset(), 'utf8');

        $this->assertTrue($oDbc->getDatabaseCharset());
        $this->assertTrue($oDbc->getClientCharset());

        foreach (['utf8', 'latin1', 'utf8mb4', 'cp1251'] as $charset) {
            $this->assertTrue($oDbc->setClientCharset($charset));
            $this->assertEqual($oDbc->getClientCharset(), $charset);
        }

        $this->assertEqual($oDbc->getDatabaseCharset(), $oDbc->getConfigurationValue());
    }

    public function testPgSQL()
    {
        $oDbh = OA_DB::singleton();

        if ($oDbh->dbsyntax !== 'pgsql') {
            return;
        }

        $oDbc = OA_DB_Charset::factory($oDbh);
        $this->assertTrue($oDbc);

        $this->assertTrue($oDbc->oDbh);
        $this->assertEqual($oDbc->getDatabaseCharset(), 'UTF8');
        $this->assertEqual($oDbc->getClientCharset(), 'UTF8');

        foreach (['LATIN1', 'UTF8', 'SJIS'] as $charset) {
            $this->assertTrue($oDbc->setClientCharset($charset));
            $this->assertEqual($oDbc->getClientCharset(), $charset);
        }

        $this->assertEqual($oDbc->getDatabaseCharset(), $oDbc->getConfigurationValue());
    }
}
