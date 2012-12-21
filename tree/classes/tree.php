<?php defined('SYSPATH') or die('No direct script access.');

class Tree {
    
    const ARR='array';
    const OBJ='object';
    
    protected $_data;
    protected $_tree;
    protected $_tmp;
    
    protected $_id='id';
    protected $_parent_id='parent_id';
    protected $_type=Tree::OBJ;
    protected $_callback='';
    
    protected $_arg = NULL;
    
    public function __construct($data_arr = NULL)
    {
        if (is_array($data_arr) || ($data_arr instanceof Traversable))
            $this->_data = $data_arr;
        else 
            throw new HTTP_Exception_500('Tree provided data is not traversable object nor array');    
    }
    
    public static function factory($data_arr)
    {
        return new self($data_arr);
    }
    
    public function data($data_arr)
    {
        $this->_data = $data_arr;    
        return $this;
    }
    
    public function id($property)
    {
        $this->_id = $property;
        return $this;
    }
    public function parent_id($property)
    {
        $this->_parent_id = $property;
        return $this;
    }
    public function type($type)
    {
        if ($type == Tree::ARR || $type == Tree::OBJ) $this->_type = $type;
        return $this;
    }
    public function callback($function,$arg=NULL)
    {
        $this->_callback = $function;
        if ($arg !== NULL) $this->_arg = $arg;
        return $this;
    }
    
    public function make()
    {
        $this->_tmp = $this->_data;
        $this->_tree = array();
        while (count($this->_tmp))
        {
            foreach($this->_tmp as $key=>$row)
            {            
                $leaf = $this->_make_leaf($key);
                if ($leaf) $this->_tree[] = $leaf;
                break;   
            }    
        }
        return $this;
    }
    
    public function get_tree_array()
    {
        return $this->_tree;
    }
    
    public function get(array $attributes=array())
    {
        $tree = '<ul';
        foreach($attributes as $name=>$value) $tree.=" $name=\"$value\"";
        $tree.='>';
        foreach($this->_tree as $leaf)
        {
            $this->_process_leaf($tree,$leaf);
        }
        $tree.='</ul>';
        return $tree;
    }
    
    public function traverse($tree = NULL)
    {
        if (!is_array($tree)) $tree = $this->_tree;
        
        foreach($tree as $leaf)
        {
            call_user_func($this->_callback,$this->_type==Tree::OBJ?$leaf->object:$leaf->data,$leaf->level);
            if (!empty($leaf->children) && is_array($leaf->children) && count($leaf->children)) $this->traverse($leaf->children);
        }

    }
    
    public function find($id)
    {
        return $this->_search($id,$this->_tree);
    }
    
    public function leaf_children($id)
    {
        $leaf = $this->find($id);
        if ($leaf) return $leaf->children();
        else return array();
    }
    
    public function parents($id)
    {
        $parents = array();
        $leaf = $this->find($id);
        if (!empty($leaf->parent)) 
            $parents = array_merge($parents,$this->parents($leaf->parent));
        return $parents;
    }
    
    
    
    
    
    
    
    
    
    protected function _search($id,$leafs)
    {
        foreach($leafs as $leaf)
        {
            if ($leaf->id == $id)
            {
                return $leaf;    
            } 
            elseif (count($leaf->children)) 
            {
                $result = $this->_search($id,$leaf->children);
                if ($result) return $result;    
            }
        }
        return false;
    }
    
    protected function _process_leaf(&$tree, &$leaf)
    {
        $tree.='<li>';
        if (is_callable($this->_callback)) $tree.= call_user_func($this->_callback,$leaf,$this->_arg);
        else $tree.= $leaf->id;
        if (count($leaf->children))
        {
            $tree.='<ul>';
            foreach($leaf->children as $child) $this->_process_leaf($tree, $child);
            $tree.='</ul>';
        }
        $tree.='</li>';
    }
    
    protected function _make_leaf($key,$level=0,$parent_id=0)
    {        
        $row = $this->_tmp[$key];
        unset($this->_tmp[$key]);
        $leaf = new Tree_Leaf;
        $leaf->level = $level++;
        $leaf->parent = $parent_id;
        if ($this->_type === Tree::OBJ && is_object($row))
        {

                $leaf->id = property_exists($row,$this->_id) ? $row->{$this->_id} : $row->__get($this->_id);

                $leaf->object = $row;
                $leaf->children = $this->_get_children($leaf->id,$level);    
        }
        elseif ($this->_type === Tree::ARR && is_array($row))
        {
            if (isset($row[$this->_id],$row[$this->_parent_id]))
            {
                $leaf->id = $row[$this->_id];
                $leaf->data = $row;
                $leaf->children = $this->_get_children($leaf->id,$level);    
            }     
            else
            {
                $leaf = NULL; 
            }
        }
        else
        {
            $leaf = NULL; 
        }
        return $leaf;
    }
    
    protected function _get_children($parent_id,$level)
    {
        $children = array();
        foreach($this->_tmp as  $key=>$row)
        {
            if ($this->_type === Tree::ARR) 
            {
                if (isset($row[$this->_parent_id]) && $row[$this->_parent_id] == $parent_id) 
                {
                    $leaf = $this->_make_leaf($key,$level,$parent_id);
                    if ($leaf) $children[] = $leaf;
                }        
            }
            elseif ($this->_type === Tree::OBJ)
            {
               if ((property_exists($row,$this->_parent_id) && $row->parent_id == $parent_id) ||
                  (method_exists($row,'__get') && $row->__get($this->_parent_id) ==  $parent_id))
               {
                    $leaf = $this->_make_leaf($key,$level,$parent_id);
                    if ($leaf) $children[] = $leaf;
               } 
            }
        }
        return $children;
    }
    
    
}