<?php

namespace UForm\Render;


class TwigLoaderFileSystem extends \Twig_Loader_Filesystem {
    public function normalizeName($name)
    {
        $name .= ".twig";
        return parent::normalizeName($name); // TODO: Change the autogenerated stub
    }
}