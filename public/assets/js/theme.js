'use strict';

var Dropdowns = (function() {

  // Variables
  //
  // Dropdown variales

  var $dropdown = $('.dropup, .dropright, .dropdown, .dropleft');
  var $dropdownMenu = $('.dropdown-menu');
  var $dropdownSubmenu = $('.dropdown-menu .dropdown-menu');
  var $dropdownSubmenuToggle = $('.dropdown-menu .dropdown-toggle');


  // Methods
  //
  // Droddown functions

  // Toggle submenu
  function toggleSubmenu(toggle) {
    var $siblingDropdown = toggle.closest($dropdown).siblings($dropdown);
    var $siblingSubmenu = $siblingDropdown.find($dropdownMenu);

    // Hide sibling submenus
    $siblingSubmenu.removeClass('show');

    // Show / hide current submenu
    toggle.next($dropdownSubmenu).toggleClass('show');
  }

  // Hide submenu
  function hideSubmenu(dropdown) {
    var $submenu = dropdown.find($dropdownSubmenu);

    // Check if there is a submenu
    if ($submenu.length) {
      $submenu.removeClass('show');
    }
  }


  // Events
  //
  // Dropdown events

  // Toggle submenu
  $dropdownSubmenuToggle.on('click', function() {
    toggleSubmenu($(this));

    return false;
  });

  // Hide submenu
  $dropdown.on('hide.bs.dropdown', function() {
    hideSubmenu($(this));
  });

})();


//
// Charts global ==================================
//

var ThemeCharts = (function() {

  // Variables
  // 
  // Theme chart variables

  // Toggle
  var $toggle = $('[data-toggle="chart"]');

  // Fonts
  var fonts = {
    base: 'Cerebri Sans'
  }

  // Colors
  var colors = {
    gray: {
      100: '#95AAC9',
      300: '#E3EBF6',
      600: '#95AAC9',
      700: '#6E84A3',
      900: '#283E59'
    },
    primary: {
      100: '#D2DDEC',
      300: '#A6C5F7',
      700: '#2C7BE5',
    },
    black: '#12263F',
    white: '#FFFFFF',
    transparent: 'transparent',
  };

  // Options
  var options = {
    defaults: {
      global: {
        responsive: true,
        maintainAspectRatio: false,
        defaultColor: colors.primary[600],
        defaultFontColor: colors.gray[600],
        defaultFontFamily: fonts.base,
        defaultFontSize: 13,
        layout: {
          padding: 0
        },
        legend: {
          display: false,
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 16
          }
        },
        elements: {
          point: {
            radius: 0,
            backgroundColor: colors.primary[700]
          },
          line: {
            tension: .4,
            borderWidth: 3,
            borderColor: colors.primary[700],
            backgroundColor: colors.transparent,
            borderCapStyle: 'rounded'
          },
          rectangle: {
            backgroundColor: colors.primary[700]
          },
          arc: {
            borderWidth: 4,
            backgroundColor: colors.primary[700]
          }
        },
        tooltips: {
          enabled: false,
          mode: 'index',
          intersect: false,
          custom: function(model) {

            // Get tooltip
            var $tooltip = $('#chart-tooltip');

            // Create tooltip on first render
            if (!$tooltip.length) {
              $tooltip = $('<div id="chart-tooltip" class="popover bs-popover-top" role="tooltip"></div>');

              // Append to body
              $('body').append($tooltip);
            }

            // Hide if no tooltip
            if (model.opacity === 0) {
              $tooltip.css('display', 'none');
              return;
            }

            function getBody(bodyItem) {
              return bodyItem.lines;
            }

            // Fill with content
            if (model.body) {
              var titleLines = model.title || [];
              var bodyLines = model.body.map(getBody);
              var html = '';

              // Add arrow
              html += '<div class="arrow"></div>';

              // Add header
              titleLines.forEach(function(title) {
                html += '<h3 class="popover-header text-center">' + title + '</h3>';
              });

              // Add body
              bodyLines.forEach(function(body, i) {
                var colors = model.labelColors[i];
                var styles = 'background-color: ' + colors.backgroundColor;
                var indicator = '<span class="popover-body-indicator" style="' + styles + '"></span>';
                var align = (bodyLines.length > 1) ? 'justify-content-left' : 'justify-content-center';
                html += '<div class="popover-body d-flex align-items-center ' + align + '">' + indicator + body + '</div>';
              });

              $tooltip.html(html);
            }

            // Get tooltip position
            var $canvas = $(this._chart.canvas);

            var canvasWidth = $canvas.outerWidth();
            var canvasHeight = $canvas.outerHeight();

            var canvasTop = $canvas.offset().top;
            var canvasLeft = $canvas.offset().left;

            var tooltipWidth = $tooltip.outerWidth();
            var tooltipHeight = $tooltip.outerHeight();

            var top = canvasTop + model.caretY - tooltipHeight - 16;
            var left = canvasLeft + model.caretX - tooltipWidth / 2;

            // Display tooltip
            $tooltip.css({
              'top': top + 'px',
              'left':  left + 'px',
              'display': 'block',
            });

          },
          callbacks: {
            label: function(item, data) {
              var label = data.datasets[item.datasetIndex].label || '';
              var yLabel = item.yLabel;
              var content = '';

              if (data.datasets.length > 1) {
                content += '<span class="popover-body-label mr-auto">' + label + '</span>';
              }

              content += '<span class="popover-body-value">$' + yLabel + 'k</span>';
              return content;
            }
          }
        }
      },
      doughnut: {
        cutoutPercentage: 83,
        tooltips: {
          callbacks: {
            title: function(item, data) {
              var title = data.labels[item[0].index];
              return title;
            },
            label: function(item, data) {
              var value = data.datasets[0].data[item.index];
              var content = '';

              content += '<span class="popover-body-value">' + value + '%</span>';
              return content;
            }
          }
        },
        legendCallback: function(chart) {
          var data = chart.data;
          var content = '';

          data.labels.forEach(function(label, index) {
            var bgColor = data.datasets[0].backgroundColor[index];

            content += '<span class="chart-legend-item">';
            content += '<i class="chart-legend-indicator" style="background-color: ' + bgColor + '"></i>';
            content += label;
            content += '</span>';
          });

          return content;
        }
      }
    }
  }

  // yAxes
  Chart.scaleService.updateScaleDefaults('linear', {
    gridLines: {
      borderDash: [2],
      borderDashOffset: [2],
      color: colors.gray[300],
      drawBorder: false,
      drawTicks: false,
      lineWidth: 0,
      zeroLineWidth: 0,
      zeroLineColor: colors.gray[300],
      zeroLineBorderDash: [2],
      zeroLineBorderDashOffset: [2]
    },
    ticks: {
      beginAtZero: true,
      padding: 10,
      callback: function(value) {
        if ( !(value % 10) ) {
          return '$' + value + 'k'
        }
      }
    }
  });

  // xAxes
  Chart.scaleService.updateScaleDefaults('category', {
    gridLines: {
      drawBorder: false,
      drawOnChartArea: false,
      drawTicks: false
    },
    ticks: {
      padding: 20
    },
    maxBarThickness: 10
  });


  // Methods
  //
  // Theme chart functions

  // Parse global options
  function parseOptions(parent, options) {
    for (var item in options) {
      if (typeof options[item] !== 'object') {
        parent[item] = options[item];
      } else {
        parseOptions(parent[item], options[item]);
      }
    }
  }

  // Push options
  function pushOptions(parent, options) {
    for (var item in options) {
      if (Array.isArray(options[item])) {
        options[item].forEach(function(data) {
          parent[item].push(data);
        });
      } else {
        pushOptions(parent[item], options[item]);
      }
    }
  }

  // Pop options
  function popOptions(parent, options) {
    for (var item in options) {
      if (Array.isArray(options[item])) {
        options[item].forEach(function(data) {
          parent[item].pop();
        });
      } else {
        popOptions(parent[item], options[item]);
      }
    }
  }

  // Toggle options
  function toggleOptions(elem) {
    var options = elem.data('add');
    var $target = $(elem.data('target'));
    var $chart = $target.data('chart');

    if (elem.is(':checked')) {

      // Add options
      pushOptions($chart, options);

      // Update chart
      $chart.update();
    } else {

      // Remove options
      popOptions($chart, options);

      // Update chart
      $chart.update();
    }
  }

  // Update options
  function updateOptions(elem) {
    var options = elem.data('update');
    var $target = $(elem.data('target'));
    var $chart = $target.data('chart');

    // Parse options
    parseOptions($chart, options);

    // Toggle ticks
    toggleTicks(elem, $chart);

    // Update chart
    $chart.update();
  }

  // Toggle ticks
  function toggleTicks(elem, $chart) {
    
    if (elem.data('prefix') !== undefined || elem.data('prefix') !== undefined) {
      var prefix = elem.data('prefix') ? elem.data('prefix') : '';
      var suffix = elem.data('suffix') ? elem.data('suffix') : '';

      // Update ticks
      $chart.options.scales.yAxes[0].ticks.callback = function(value) {
        if ( !(value % 10) ) {
          return prefix + value + suffix;
        }
      }

      // Update tooltips
      $chart.options.tooltips.callbacks.label = function(item, data) {
        var label = data.datasets[item.datasetIndex].label || '';
        var yLabel = item.yLabel;
        var content = '';

        if (data.datasets.length > 1) {
          content += '<span class="popover-body-label mr-auto">' + label + '</span>';
        }

        content += '<span class="popover-body-value">' + prefix + yLabel + suffix + '</span>';
        return content;
      }

    }
  }


  // Events
  //
  // Run functions on windows load or special events

  // Parse global options
  parseOptions(Chart, options);

  // Toggle options
  $toggle.on({
    'change': function() {
      var $this = $(this);

      if ($this.is('[data-add]')) {
        toggleOptions($this);
      }
    },
    'click': function() {
      var $this = $(this);

      if ($this.is('[data-update]')) {
        updateOptions($this);
      }
    }
  });


  // Return
  //
  // Make variables global

  return {
    fonts: fonts,
    colors: colors,
    options: options
  };
  
})();


//
// Header ==================================
// Header card charts
//

var Header = (function() {

  // Variables

  var $headerChart = $('#headerChart');

  // Init
  //
  // Init chart

  function init($chart) {

    // Create chart
    var headerChart = new Chart($chart, {
      type: 'line',
      options: {
        scales: {
          yAxes: [{
            gridLines: {
              color: ThemeCharts.colors.gray[900],
              zeroLineColor: ThemeCharts.colors.gray[900]
            }
          }]
        }
      },
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Performance',
          data: [0,10,5,15,10,20,15,25,20,30,25,40]
        }]
      }
    });

    // Save to jQuery object
    $chart.data('chart', headerChart);

  };

  // Events
  //
  // Header chart events

  if ($headerChart.length) {
    init($headerChart);
  }

})();


//
// Performance ==================================
// Performance card charts
//

var Performance = (function() {

  // Variables

  var $performanceChart = $('#performanceChart');

  // Init
  //
  // Init chart

  function init($chart) {

    // Create chart
    var performanceChart = new Chart($chart, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Performance',
          data: [0,10,5,15,10,20,15,25,20,30,25,40]
        }]
      }
    });

    // Save to jQuery object
    $chart.data('chart', performanceChart);
  }

  // Events
  //
  // Performance chart events

  if ($performanceChart.length) {
    init($performanceChart);
  }

})();


//
// Performance Alias ==================================
// Performance card charts
//

var PerformanceAlias = (function() {

  // Variables

  var $performanceChartAlias = $('#performanceChartAlias');

  // Init
  //
  // Init chart

  function init($chart) {

    // Create chart
    var performanceChartAlias = new Chart($chart, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Performance',
          data: [0,10,5,15,10,20,15,25,20,30,25,40]
        }]
      }
    });

    // Save to jQuery object
    $chart.data('chart', performanceChartAlias);
  }

  // Events
  //
  // Performance chart events

  if ($performanceChartAlias.length) {
    init($performanceChartAlias);
  }

})();


//
// Orders ==================================
// Orders card charts
//

var Orders = (function() {

  // Variables

  var $ordersChart = $('#ordersChart');

  // Init
  //
  // Init chart

  // Init chart
  function init($chart) {

    // Create chart
    var ordersChart = new Chart($chart, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Sales',
          data: [25,20,30,22,17,10,18,26,28,26,20,32]
        }]
      }
    });

    // Save to jQuery object
    $chart.data('chart', ordersChart);
  }

  // Events
  //
  // Orders chart events

  // Init chart
  if ($ordersChart.length) {
    init($ordersChart);
  }
  
})();


//
// Orders Alias ==================================
// Orders card charts
//

var OrdersAlias = (function() {

  // Variables

  var $ordersChartAlias = $('#ordersChartAlias');

  // Init
  //
  // Init chart

  // Init chart
  function init($chart) {

    // Create chart
    var ordersChartAlias = new Chart($chart, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Sales',
          data: [25,20,30,22,17,10,18,26,28,26,20,32]
        }]
      }
    });

    // Save to jQuery object
    $chart.data('chart', ordersChartAlias);
  }

  // Events
  //
  // Orders chart events

  // Init chart
  if ($ordersChartAlias.length) {
    init($ordersChartAlias);
  }
  
})();


//
// Devices ==================================
// Devices card charts
//

var Devices = (function() {

  // Variables

  var $devicesChart = $('#devicesChart');

  // Methods
  //
  // Chart functions

  // Init chart
  function init($chart) {

    // Create chart
    var devicesChart = new Chart($chart, {
      type: 'doughnut',
      data: {
        labels: ['Desktop', 'Tablet', 'Mobile'],
        datasets: [{
          data: [60, 25, 15],
          backgroundColor: [
            ThemeCharts.colors.primary[700],
            ThemeCharts.colors.primary[300],
            ThemeCharts.colors.primary[100]
          ],
          hoverBorderColor: ThemeCharts.colors.white
        }]
      }
    });

    // Save to jQuery object
    $chart.data('chart', devicesChart);
  }

  // Generate legend
  function generateLegend($chart) {
    var content = $chart.data('chart').generateLegend();
    var legend = $chart.data('target');
    var $legend = $(legend);

    $legend.html(content);
  }

  // Events
  //
  // Chart events

  if ($devicesChart.length) {

    // Init chart
    init($devicesChart);

    // Generate legend
    generateLegend($devicesChart);
  }

})();


//
// Weekly hours ==================================
// Weekly hours card charts
//

var WeeklyHours = (function() {

  // Variables

  var $weeklyHoursChart = $('#weeklyHoursChart');

  // Init
  //
  // Init chart

  function init($chart) {

    // Create chart
    var weeklyHoursChart = new Chart($chart, {
      type: 'bar',
      options: {
        scales: {
          yAxes: [{
            ticks: {
              callback: function(value) {
                if ( !(value % 10) ) {
                  return value + 'hrs'
                }
              }
            }
          }]
        },
        tooltips: {
          callbacks: {
            label: function(item, data) {
              var label = data.datasets[item.datasetIndex].label || '';
              var yLabel = item.yLabel;
              var content = '';

              if (data.datasets.length > 1) {
                content += '<span class="popover-body-label mr-auto">' + label + '</span>';
              }

              content += '<span class="popover-body-value">' + yLabel + 'hrs</span>';
              return content;
            }
          }
        }
      },
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          data: [21, 12, 28, 15, 5, 12, 17, 2]
        }]
      }
    });

    // Save to jQuery object
    $chart.data('chart', weeklyHoursChart);
  }

  // Events
  //
  // Weekly hours chart events

  if ($weeklyHoursChart.length) {
    init($weeklyHoursChart);
  }

})();


//
// Navbar ==================================
//

var Navbar = (function() {

  // Variables

  var $nav = $('.navbar-nav, .navbar-nav .nav');
  var $navCollapse = $('.navbar-nav .collapse');

  // Methods
  //
  // Navbar methods

  function accordion($this) {
    $this.closest($nav).find($navCollapse).not($this).collapse('hide');
  }

  // Events
  //
  // Navbar events

  $navCollapse.on({
    'show.bs.collapse': function() {
      accordion( $(this) );
    }
  })
  
})();


//
// Tooltips ==================================
//

var Tooltip = (function() {

  // Variables

  var $tooltip = $('[data-toggle="tooltip"]');

  // Methods
  //
  // Tooltips methods

  function init() {
    $tooltip.tooltip();
  }

  // Events
  //
  // Tooltip events

  if( $tooltip.length ) {
    init();
  }
  
})();


//
// Popovers ==================================
//

var Popover = (function() {

  // Variables

  var $popover = $('[data-toggle="popover"]');

  // Methods
  //
  // Tooltips methods

  function init() {
    $popover.popover();
  }

  // Events
  //
  // Tooltip events

  if( $popover.length ) {
    init();
  }
  
})();


//
// Highlight.js ==================================
//

var Highlight = (function() {
  
  // Variables

  var $highlight = $('.highlight');

  // Methods

  function init(i, block) {
    hljs.highlightBlock(block);
  }

  // Events

  $highlight.each(function(i, block) {
    init(i, block);
  });

})();


//
// Flatpickr ==================================
//

var Flatpickr = (function() {

  // Variables

  var $formControl = $('#formControlFlatpickr');

  // Methods
  //
  // Flatpicks methods

  function init($input) {
    $input.flatpickr();
  }

  // Events
  //
  // Flatpickr events

  if ($formControl.length) {
    init($formControl);
  }

})();


//
// List.js ==================================
//

var Lists = (function() {

  // Variables

  var $lists = $('[data-toggle="lists"]');
  var $listsSort = $('[data-sort]');

  // Methods

  // Init
  function init($list) {
    new List($list.get(0), getOptions($list));
  }

  // Get options
  function getOptions($list) {
    var options = {
      valueNames: $list.data('lists-values'),
      listClass: $list.data('lists-class') ? $list.data('lists-class') : 'list'
    }

    return options;
  }

  // Events

  // Init
  if ($lists.length) {
    $lists.each(function() {
      init($(this));
    });
  }

  // Sort
  $listsSort.on('click', function() {
    return false;
  });

})();