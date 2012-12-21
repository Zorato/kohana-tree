kohana-tree
===========

Kohana array-to-tree module 

This module was created to build tree structures from flat arrays.<br>
In simple language: build menus, catalogues, folder-structures etc with your data from database.

Installation
-----
1) place 'tree' folder in your kohana modules directory<br>
2) add module in your bootstrap file (like this: 'tree'=>MODPATH.'tree', )<br>
3) enjoy!<br>

Usage
-----

First of all get data from database by ORM, DB::select() or any other way.<br>
Then use Tree::factory() method to provide module received data. This method receives arrays or any traversable objects,
so you can provide it Database_Result object or an array(Note, that if you use array, you shuold use chaining method ->type(Tree::ARR) ).<br>

After that, you should set callback function using ->callback('process_leaf') method.<br>
This function will receive Tree_Leaf object and should return html code for current leaf.<br>

Then just call ->make() method to create tree and use ->get($attributes) method to get your html code!<br>

Notes
------
Methods factory(), data(), id(), parent_id(), type(), callback() and make() are chainable.<br>
In Tree_leaf object, your data will be in object property (if you used Objects) or data property (if factory() received array).<br>
You can change 'id' and 'parent_id' (theese are default ones) properties to look for in your data by methods id() and parent_id().<br>


Code samples
------

https://github.com/Zorato/kohana-tree/blob/master/code_samples.php

