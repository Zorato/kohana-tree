<?php defined('SYSPATH') or die('No direct script access.');

class Tree_Leaf {
    
    public $id;
    public $object;
    public $data;
    public $children;
    public $level;
    public $parent;
    
    /**
     * Tree_Leaf::children()
     * returns flat (non-tree) array with ids of all this leaf's descendants
     * @return array
     */
    public function children()
    {
        $children = array($this->id);
        foreach($this->children as $child)
            $children = array_merge($children,$child->children());
        return $children;
    }
    
}