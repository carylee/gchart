<?php

class GChart 
{
  public function GChart( $data ) 
  // expects an array of label => value
  {
    $this->addData( $data );
  }
  
  private $data = array();
  private $options = array();
  public $xlabel = '';
  public $ylabel = '';
  public $type = 'PieChart';
  public $divName = 'chart_div';

  // Configuration Options

  public $is3D = false;
  /**
   * enableToolTip
   * If set to true, tooltips are shown when the user clicks on a slice
   */
  public $enableTooltip = true; // 

  /*
   * tooltipFontSize
   * The font size of the tooltip text
   * This might be reduced, if the tooltip is too small to hold the text in the specified font.
   */
  public $tooltipFontSize = 11;
  public $tooltipHeight = 60;
  public $tooltipWidth = 120;

  /**
   * Position and type of legend. Can be one of the following:
   * 'right' - To the right of the chart
   * 'left' - To the left of the chart
   * 'top' - Above the chart
   * 'bottom' - Below the chart
   * 'label' - Labels near slices
   * 'none' - No legend is displayed
   */
  public $legend = 'right'; // 

  public $width = 400; // Height of the chart in pixels
  public $height = 240; // Width of the chart in pixels

  /**
   * pieJoinAngle
   * Any slice less than this angle will be combined into a generic slice labeled "Other".
   */
  public $pieJoinAngle = 0;

  /** 
   * pieMinimalAngle
   * Any slice smaller than this angle will not be drawn in the pie chart 
   * (though it will still get a legend entry). The remaining slices will 
   * expand to fill the pie in fixed proportion. Note: This can distort the 
   * apparent values, especially when this number is large, because a quantity 
   * is being removed from the pie.
   *
   * To calculate the sizes of the slices, first angles smaller than pieJoinAngle 
   * are joined to the "Other" slice, and then all slices larger than pieMinimalAngle are drawn
   */
  public $pieMinimalAngle = 0;


  /* 
   * Options that should be left out if not set 
   * 
   */

  public $title = false; // Text to display above the chart
  public $titleFontSize = false;
  public $legendFontSize = false;

  
  //private $counter = 0;
  
  public function addDatum( $label, $value )
  {
    $this->data[] = array( 'label'=>$label, 'value'=>$value);
  }

  public function addData( $data )
  {
    foreach( $data as $label=>$value )
    {
      $this->addDatum( $label, $value );
    }
  }

  private function makeVarData() {
    $datatable = "var data = new google.visualization.DataTable();\n";
    $datatable .= "data.addColumn('string', '$this->xlabel');\n";
    $datatable .= "data.addColumn('number', '$this->ylabel');\n";
    $datatable .= "data.addRows(". count($this->data) . ");\n";

    foreach( $this->data as $index=>$datum )
    {
      $datatable .= 'data.setValue('.$index.', 0, \''.$datum['label']."');\n";
      $datatable .= 'data.setValue('.$index.', 1, \''.$datum['value']."');\n";
    }
    return $datatable;
  }

  private function makeVarChart() {
    $varChart = 'var chart = new.visualization.'.$this->type.'(document.getElementById(\''.$this->divName."'));\n";
    $varChart .= 'chart.draw(data, {' . $this->makeOptions() ."});\n";
    return $varChart;
  }

  private function makeOptions() {
    $options = '';
    $options .= 'enableTooltip: ' . $this->enableTooltip . ', ';
    $options .= 'height: ' . $this->height . ', ';
    $options .= 'is3D: ' . $this->is3D . ', ';
    $options .= 'legend: ' . $this->legend . ', ';
    $options .= 'pieJoinAngle: ' . $this->pieJoinAngle . ', ';
    $options .= 'pieMinimalAngle: ' . $this->pieMinimalAngle . ', ';
    $options .= 'tooltipFontSize: ' . $this->tooltipFontSize . ', ';
    $options .= 'tooltipHeight: ' . $this->tooltipHeight . ', ';
    $options .= 'tooltipWidth: ' . $this->tooltipWidth . ', ';
    $options .= 'width: ' . $this->width . ', ';

    if( $this->title ) {
      $options .= 'title: ' . $this->title . ', ';
    }

    if( $this->legendFontSize ) {
      $options .= 'legendFontSize: ' . $this->legendFontSize . ', ';
    }

    if( $this->titleFontSize ) {
      $options .= 'titleFontSize: ' . $this->titleFontSize . ', ';
    }

    return $options;
  }

  private function makeDrawChart()
  {
    $drawChart = "function drawChart() {\n";
    $drawChart .= $this->makeVarData();
    $drawChart .= $this->makeVarChart();

    $drawChart .= "\n}\n";

    return $drawChart;
  }

  public function makeChart() {
    $chart = 'google.load("visualization", "1", {packages:["' . strtolower($this->type) . "\"]});\n";
    $chart .= "google.setOnLoadCallback(drawChart);\n";
    $chart .= $this->makeDrawChart();

    return $chart;
  }
}
