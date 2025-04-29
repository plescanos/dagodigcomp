<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Digcomp;

use Orchid\Screen\Layouts\Chart;

class PieEdadLayout extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'bar';
    protected $maxSlices =7;
    protected $export = 'yes';
    
   
    

    /**
     * @var int
     */
    protected $height = 300;

}
