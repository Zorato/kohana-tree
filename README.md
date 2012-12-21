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

// Getting data from db using ORM
$menu_items = ORM::factory('menu')
                 ->where('active','=',1) // use any orm method you want
                 ->order_by('position') // Tree module will generate tree depending on order of data you provided
                 ->find_all();
// Making tree:
$tree = Tree::factory($menu_items) // provide data
            ->callback('process_menu_items') //set callback function
            ->make(); //make tree
    
// Now you can do whatever you want
echo $tree->get(array('id'=>'nav-menu', class=>'menu')); // will output html code with your menu
$tree->get_tree_array(); // will return tree array for manual traversing
$tree->traverse();  // apply callback function to every Tree_Leaf, 
                    // will NOT output anything, your callback function should do that instead!
                    
// other useful functions: leaf_children($id), parents($id), find($id).

//callback function example:

function process_menu_items($leaf)
{
    $menu_item = $leaf->object; // your Model_Menu object will be in ->object property
    return $menu_item->text; // you should return text (code) to be placed between <li></li> of tree leaf.
}

//generated code (e.g.) from echo $tree->get() method:
<ul id="nav-menu" class="menu">
  <li>Home</li>
  <li>About
    <ul>
      <li>Company</li>
      <li>Partners</li>
    </ul>
  </li>
  <li>Contacts</li>
</ul>

