
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
