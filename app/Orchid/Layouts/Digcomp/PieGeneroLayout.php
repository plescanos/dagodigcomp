<?php

//declare(strict_types=1);

namespace App\Orchid\Layouts\Digcomp;

use Orchid\Screen\Layouts\Chart;

class PieGeneroLayout extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'pie';
    protected $export = 'no';

    /**
     * @var int
     */
    protected $height = 350;
}
