<?php
/**
 * RecordAdminCreateForm extension - An extension to enable the creation of any RA record from any page.
 * See http://www.organicdesign.co.nz/Extension:RecordAdminCreateForm for installation and usage details
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author [http://www.organicdesign.co.nz/wiki/User:Jack User:Jack]
 * @copyright © 2008 [http://www.organicdesign.co.nz/wiki/User:Jack User:Jack]
 * @licence GNU General Public Licence 2.0 or later
 */
if ( !defined( 'MEDIAWIKI' ) )           die( 'Not an entry point.' );
if ( !defined( 'RECORDADMIN_VERSION' ) ) die( 'This extension depends on the RecordAdmin extension' );

define( 'RECORDADMINCREATEFORM_VERSION', '1.0.3, 2009-11-26' );

$wgExtensionFunctions[] = 'efSetupRecordAdminCreateForm';

$wgExtensionCredits['parserhook'][] = array(
	'name'        => 'RecordAdminCreateForm',
	'author'      => '[http://www.organicdesign.co.nz/wiki/User:Jack User:Jack]',
	'description' => 'An extension to enable the creation of any RA record from any page. Made with [http://www.organicdesign.co.nz/Template:Extension Template:Extension].',
	'url'         => 'http://www.organicdesign.co.nz/Extension:RecordAdminCreateForm',
	'version'     => RECORDADMINCREATEFORM_VERSION
);

/**
 * Function called from the hook BeforePageDisplay, creates a form which links to a new RA form page with title and type arguments in the url.
 */
function efRecordAdminCreateForm (&$out) {
	global $wgRecordAdminCategory;

	# Make options list from items in records cat
	$options = '';
	$dbr = &wfGetDB(DB_SLAVE);
	$cl  = $dbr->tableName( 'categorylinks' );
	$cat = $dbr->addQuotes( $wgRecordAdminCategory );
	$res = $dbr->select( $cl, 'cl_from', "cl_to = $cat", __METHOD__, array( 'ORDER BY' => 'cl_sortkey' ) );
	while ( $row = $dbr->fetchRow( $res ) ) $options .= '<option>' . Title::newFromID( $row[0] )->getText() . '</option>';

	# Post the form to Special:RecordAdmin
	$action = Title::makeTitle( NS_SPECIAL, 'RecordAdmin' )->getLocalUrl();

	# Add a form to the page
	$out->mBodytext .= "
		<form id='RACreateForm' method='POST' action='$action'>
			Create a new <select name='wpType'>$options</select>
			called <input name='wpTitle' />
			<input type='submit' value='Create' />
		</form>";

	return true;
}

/**
 * Setup function
 */
function efSetupRecordAdminCreateForm() {
	global $wgHooks;
	$wgHooks['BeforePageDisplay'][] = 'efRecordAdminCreateForm';
}
