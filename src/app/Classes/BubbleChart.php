<?php

namespace LaravelEnso\Charts\app\Classes;

class BubbleChart extends AbstractChart
{
    public $fill = false;
    public $maxRadius = 25;
    private $maxDatasetsRadius;

    public function getResponse()
    {
        return [
            'data'    => ['datasets' => $this->data],
            'options' => $this->options,
            'title'   => $this->title,
        ];
    }

    protected function buildChartData()
    {
        $colorIndex = 0;

        $this->getBubbleChartDatasetMaxRadius();
        $this->resizeBubbleChartDatasetRadius();
        $this->mapDatasetsWithLabels();

        foreach ($this->datasets as $label => $dataset) {
            $borderColor = $this->chartColors[$colorIndex];

            $this->data[] = [
                'label'                => $label,
                'borderColor'          => $borderColor,
                'backgroundColor'      => $this->hex2rgba($borderColor),
                'hoverBackgroundColor' => $this->hex2rgba($borderColor, 0.6),
                'data'                 => $this->buildDatasetArray($dataset),
            ];

            $colorIndex++;
        }
    }

    private function resizeBubbleChartDatasetRadius()
    {
        foreach ($this->datasets as &$dataset) {
            foreach ($dataset as &$bubble) {
                $bubble[2] = round($this->maxRadius * $bubble[2] / $this->maxDatasetsRadius, 2);
            }
        }
    }

    private function getBubbleChartDatasetMaxRadius()
    {
        $maxArray = [];

        foreach ($this->datasets as $dataset) {
            $maxArray[] = max(array_column($dataset, 2));
        }

        $this->maxDatasetsRadius = max($maxArray);
    }

    private function mapDatasetsWithLabels()
    {
        $this->datasets = array_combine(array_values($this->labels), array_values($this->datasets));
    }

    private function buildDatasetArray($dataset)
    {
        $datasetArray = [];

        foreach ($dataset as $values) {
            $datasetArray[] = [
                'x' => $values[0],
                'y' => $values[1],
                'r' => $values[2],
            ];
        }

        return $datasetArray;
    }
}
