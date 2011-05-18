<?php  //$Id$

// This file keeps track of upgrades to
// the mtgdistribute block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_block_mtgdistribute_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;
    require_once $CFG->dirroot.'/blocks/mtgdistribute/lib.php';

    $result = true;

/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.

    if ($result && $oldversion < 2010102700) {
        // You can now get A* in A-levels, so update the scales to reflect that.
        if ($scale = $DB->get_record('scale', array('name' => ALIS_ADVANCED_GCE.' MTG'))) {
            $result = $result && $DB->set_field('scale', array('scale' => MTG_SCALE_ADVANCED_GCE, 'name' => ALIS_ADVANCED_GCE.' MTG'));
        }
        if ($scale = $DB->get_record('scale', array('name' => ALIS_ADVANCED_SUBSIDIARY_GCE.' MTG'))) {
            $result = $result && $DB->set_field('scale', array('scale' => MTG_SCALE_ADVANCED_SUBSIDIARY_GCE, 'name' => ALIS_ADVANCED_SUBSIDIARY_GCE.' MTG'));
        }
        if ($scale = $DB->get_record('scale', array('name' => ALIS_ADVANCED_GCE_DOUBLE.' MTG'))) {
            $result = $result && $DB->set_field('scale', array('scale' => MTG_SCALE_ADVANCED_GCE_DOUBLE, 'name' => ALIS_ADVANCED_GCE_DOUBLE.' MTG'));
        }
        if ($scale = $DB->get_record('scale', array('name' => ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE.' MTG'))) {
            $result = $result && $DB->set_field('scale', array('scale' => MTG_SCALE_ADVANCED_SUBSIDIARY_GCE_DOUBLE, 'name' => ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE.' MTG'));
        }
    }

	if ($result && $oldversion < 2011051700) {
		// Moodle 2 upgrade, changing to a report and changing the table names.
		// Define table mtgdistribute to be dropped
        $table = new xmldb_table('mtgdistribute');

        // Conditionally launch drop table for mtgdistribute
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
		
		$table = new xmldb_table('mtgdistribute_alisdata');

        // Launch rename table for mtgdistribute
        $dbman->rename_table($table, 'report_targetgrades_alisdata');
		
		$table = new xmldb_table('mtgdistribute_qualtype');

        // Launch rename table for mtgdistribute
        $dbman->rename_table($table, 'report_targetgrades_qualtype');
		
		$table = new xmldb_table('mtgdistribute_patterns');

        // Launch rename table for mtgdistribute
        $dbman->rename_table($table, 'report_targetgrades_patterns');
		
    }

    return $result;
}

?>
