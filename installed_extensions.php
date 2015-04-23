<?php

/* List of installed additional extensions. If extensions are added to the list manually
	make sure they have unique and so far never used extension_ids as a keys,
	and $next_extension_id is also updated. More about format of this file yo will find in 
	FA extension system documentation.
*/

$next_extension_id = 2; // unique id for next installed extension

$installed_extensions = array (
  1 => 
  array (
    'name' => 'Inventory Items CSV Import',
    'package' => 'import_items',
    'version' => '2.3.0-2',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/import_items',
  ),
);
?>