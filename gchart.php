<?php

class GChart 
{
  public function GChart( $data, $type, $options=array() ) 
  // expects an array of label => value
  {
    $this->addData( $data );

    /*if(!empty($options['type'])) {
      $this->type = $options['type'];
    }*/
    $type = strtolower($type);
    switch ($type) {
      case "linechart":
        $this->type = 'LineChart';
        break;
      case "piechart":
        $this->type = 'PieChart';
        break;
      case "geomap_region":
        $this->type = 'GeoMap';
        $this->dataMode = 'regions';
        break;
      default:
        $this->type = 'PieChart';
        break;
      }

    if(!empty($options['xlabel']))
      $this->xlabel = $options['xlabel'];

    if(!empty($options['ylabel']))
      $this->ylabel = $options['ylabel'];

    if(isset($options['is3D']))
      $this->is3D = $options['is3D'];

    if(!empty($options['width']))
      $this->width = $options['width'];
    
    if(!empty($options['height']))
      $this->height = $options['height'];
    
    if(!empty($options['legend']))
      $this->legend = $options['legend'];

    if(!empty($options['legendFontSize']))
      $this->legendFontSize = $options['legendFontSize'];

    if(isset($options['enableTooltip']))
      $this->enableTooltip = $options['enableTooltip'];

    if(!empty($options['tooltipFontSize']))
      $this->tooltipFontSize = $options['tooltipFontSize'];
    
    if(!empty($options['tooltipHeight']))
      $this->tooltipHeight = $options['tooltipHeight'];
    
    if(!empty($options['tooltipWidth']))
      $this->tooltipWidth = $options['tooltipWidth'];
    
    if(!empty($options['pieJoinAngle']))
      $this->pieJoinAngle = $options['pieJoinAngle'];
    
    if(!empty($options['pieMinimalAngle']))
      $this->pieMinimalAngle = $options['pieMinimalAngle'];
    
    if(!empty($options['title']))
      $this->title = $options['title'];
    
    if(!empty($options['titleFontSize']))
      $this->titleFontSize = $options['titleFontSize'];
  }
  
  private $data = array();
  private $options = array();
  public $xlabel = '';
  public $ylabel = '';
  public $type = 'PieChart';
  public $divName = 'chart_div';
  private $dataMode = '';

  // Configuration Options

  public $is3D = 'false';
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

  public $width = 600; // Height of the chart in pixels
  public $height = 400; // Width of the chart in pixels

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
      $datatable .= 'data.setValue('.$index.', 1, '. $datum['value'] . ");\n";
    }
    //echo $datatable;
    return $datatable;
  }

  public function makeDiv() {
    return '<div id="'. $this->divName .'"></div>';
  }

  private function makeVarChart() {
    $varChart = 'var chart = new google.visualization.'.$this->type.'(document.getElementById(\''.$this->divName."'));\n";
    $varChart .= 'chart.draw(data, {' . $this->makeOptions() ."});\n";
    return $varChart;
  }

  private function makeVarContainer() {
    $varContainer = 'var container = document.getElementById(\''.$this->divName."');\n";
    $varContainer .= "var geomap = new google.visualization.GeoMap(container);\n";
    $varContainer .= "geomap.draw(data, options);\n";
    return $varContainer;
  }

  private function makeOptions() {
    $options = '';
    $options .= 'enableTooltip: ' . $this->enableTooltip . ', ';
    $options .= 'height: ' . $this->height . ', ';
    $options .= 'is3D: ' . $this->is3D . ', ';
    $options .= 'legend: \'' . $this->legend . '\', ';
    $options .= 'pieJoinAngle: ' . $this->pieJoinAngle . ', ';
    $options .= 'pieMinimalAngle: ' . $this->pieMinimalAngle . ', ';
    $options .= 'tooltipFontSize: ' . $this->tooltipFontSize . ', ';
    $options .= 'tooltipHeight: ' . $this->tooltipHeight . ', ';
    $options .= 'tooltipWidth: ' . $this->tooltipWidth . ', ';
    $options .= 'width: ' . $this->width . ', ';

    if( $this->title ) {
      $options .= 'title: \'' . $this->title . '\', ';
    }

    if( $this->legendFontSize ) {
      $options .= 'legendFontSize: ' . $this->legendFontSize . ', ';
    }

    if( $this->titleFontSize ) {
      $options .= 'titleFontSize: ' . $this->titleFontSize . ', ';
    }

    if( $this->type == 'GeoMap') {
      $options .= 'dataMode: \'' . $this->dataMode . '\', ';
    }

    return $options;
  }

  public function headerJs() {
    return '<script type="text/javascript" src="http://www.google.com/jsapi"></script>';
  }

  private function makeDrawChart()
  {
    $drawChart = "function drawChart() {\n";
    $drawChart .= $this->makeVarData();
    $drawChart .= $this->makeVarChart();

    //$drawChart .=

    $drawChart .= "\n}\n";

    return $drawChart;
  }

  public function makeChart() {
    $chart = 'google.load("visualization", "1", {packages:["' . strtolower($this->type) . "\"]});\n";
    $chart .= $this->makeDrawChart();
    $chart .= "google.setOnLoadCallback(drawChart);\n";

    return $chart;
  }
}
