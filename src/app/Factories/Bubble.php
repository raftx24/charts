<?php

namespace LaravelEnso\Charts\app\Factories;

class Bubble extends Chart
{
    private $radiusLimit;
    private $maxRadius;

    public function __construct()
    {
        parent::__construct();

        $this->radiusLimit = 25;

        $this->type('bubble')
            ->ratio(1.6)
            ->scales();
    }

    public function response()
    {
        return [
            'data' => ['datasets' => $this->data],
            'options' => $this->options,
            'title' => $this->title,
            'type' => $this->type,
        ];
    }

    protected function build()
    {
        $this->maxRadius()
            ->computeRadius()
            ->mapDatasetsLabels()
            ->data();
    }

    private function maxRadius()
    {
        $this->maxRadius = collect($this->datasets)->map(function ($dataset) {
            return max(array_column($dataset, 2));
        })->max();

        return $this;
    }

    private function computeRadius()
    {
        $this->datasets = collect($this->datasets)->map(function ($dataset) {
            return collect($dataset)->map(function ($bubble) {
                $bubble[2] = round($this->radiusLimit * $bubble[2] / $this->maxRadius, 2);

                return $bubble;
            });
        })->toArray();

        return $this;
    }

    private function mapDatasetsLabels()
    {
        $this->datasets = array_combine(
            array_values($this->labels),
            array_values($this->datasets)
        );

        return $this;
    }

    private function data()
    {
        collect($this->datasets)->each(function ($dataset, $label) {
            $color = $this->color();

            $this->data[] = [
                'label' => $label,
                'borderColor' => $color,
                'backgroundColor' => $this->hex2rgba($color),
                'hoverBackgroundColor' => $this->hex2rgba($color, 0.6),
                'data' => $this->dataset($dataset),
                'datalabels' => [
                    'backgroundColor' => $color,
                ],
            ];
        });
    }

    private function dataset($dataset)
    {
        return collect($dataset)->map(function ($values) {
            return [
                'x' => $values[0],
                'y' => $values[1],
                'r' => $values[2],
            ];
        });
    }
}
