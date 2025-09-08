var chartData = typeof monthlyRevenueData !== 'undefined' && monthlyRevenueData.length > 0 
    ? monthlyRevenueData.map(function(item) { return item.revenue; })
    : [10, 20, 12, 30, 14, 35, 16, 32, 14, 25, 13, 28];

var chartCategories = typeof monthlyRevenueData !== 'undefined' && monthlyRevenueData.length > 0
    ? monthlyRevenueData.map(function(item) { return item.month; })
    : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

var options = {
  series: [{
    name: "Revenue",
    data: chartData
  }],
  chart: {
    height: 264,
    type: 'line',
    toolbar: {
      show: false
    },
    zoom: {
      enabled: false
    },
    dropShadow: {
      enabled: true,
      top: 6,
      left: 0,
      blur: 4,
      color: "#000",
      opacity: 0.1,
    },
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    colors: ['#487FFF'],
    width: 3
  },
  markers: {
    size: 0,
    strokeWidth: 3,
    hover: {
      size: 8
    }
  },
  tooltip: {
    enabled: true,
    x: {
      show: true,
    },
    y: {
      formatter: function (value) {
        return "Rp " + (value * 1000).toLocaleString('id-ID');
      }
    },
    z: {
      show: false,
    }
  },
  grid: {
    row: {
      colors: ['transparent', 'transparent'],
      opacity: 0.5
    },
    borderColor: '#D1D5DB',
    strokeDashArray: 3,
  },
  yaxis: {
    labels: {
      formatter: function (value) {
        return "Rp" + value + "k";
      },
      style: {
        fontSize: "14px"
      }
    },
  },
  xaxis: {
    categories: chartCategories,
    tooltip: {
      enabled: false
    },
    labels: {
      formatter: function (value) {
        return value;
      },
      style: {
        fontSize: "14px"
      }
    },
    axisBorder: {
      show: false
    },
    crosshairs: {
      show: true,
      width: 20,
      stroke: {
        width: 0
      },
      fill: {
        type: 'solid',
        color: '#487FFF40',
      }
    }
  }
};

if (document.querySelector("#revenueChart")) {
  var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
  chart.render();
}

var propertyStatusOptions = {
  series: [
    typeof monthlyRevenueData !== 'undefined' ? monthlyRevenueData.length : 12,
    typeof monthlyRevenueData !== 'undefined' ? Math.floor(monthlyRevenueData.length * 0.8) : 10
  ],
  colors: ['#487FFF', '#FF9F29'],
  labels: ['Occupied', 'Available'],
  legend: {
    show: true,
    position: 'bottom'
  },
  chart: {
    type: 'donut',
    height: 200,
    sparkline: {
      enabled: false
    }
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total',
            formatter: function (w) {
              return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
            }
          }
        }
      }
    }
  },
  stroke: {
    width: 0,
  },
  dataLabels: {
    enabled: false
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }],
};

if (document.querySelector("#propertyStatusChart")) {
  var propertyChart = new ApexCharts(document.querySelector("#propertyStatusChart"), propertyStatusOptions);
  propertyChart.render();
}