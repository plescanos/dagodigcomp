<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\DigComp;

use Orchid\Screen\Layouts\Chart;

class ChartBar extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = self::TYPE_BAR;

    /**
     * Height of the chart.
     *
     * @var int
     */
    protected $height = 300;
    protected $export = 'no';
}
