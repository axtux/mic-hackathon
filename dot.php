<?php
require('server/lib.php');

function pure($s) {
	return str_replace('"', '\\"', $s);
}

header('Content-Type: application/x-dot');
echo "digraph {\n";
$st = $db->query('SELECT * FROM dot');
while ($q = $st->fetch()) {
	$x = pure($q['x']);
	$y = pure($q['y']);
	$l = pure($q['label']);
	echo "\"$x\" -> \"$y\" [label=\"$l\"];\n";
}
echo "}\n";
