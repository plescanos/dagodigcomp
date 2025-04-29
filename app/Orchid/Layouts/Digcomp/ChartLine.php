<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Digcomp;

use Orchid\Screen\Layouts\Chart;

class ChartLine extends Chart
{
    /**
     * Height of the chart.
     *
     * @var int
     */
    protected $height = 300;

    /**
     * Configuring line.
     *
     * @var array
     */
    protected $lineOptions = [
        'spline'     => 1,
        'regionFill' => 1,
        'hideDots'   => 0,
        'hideLine'   => 0,
        'heatline'   => 0,
        'dotSize'    => 3,
    ];

    protected $export = 'no';

    /**
     * To highlight certain values on the Y axis, markers can be set.
     * They will shown as dashed lines on the graph.
     */
    protected function markers(): ?array
    {
        return [
            [
                'label'   => 'Media',
                'value'   => 40,
            ],
        ];
    }
}
