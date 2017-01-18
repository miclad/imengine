<?php

$tree = new Tree('folders', 'entity="catalog" and active=1');
$sections = $tree->getFullTree();

$this->setActionTemplate('nav_tree');