
var chartColors = {
  red: 'rgb(255, 99, 132)',
  orange: 'rgb(255, 159, 64)',
  yellow: 'rgb(255, 205, 86)',
  green: 'rgb(75, 192, 192)',
  info: '#41B1F9',
  blue: '#3245D1',
  purple: 'rgb(153, 102, 255)',
  grey: '#EBEFF6'
};

// Gráfico de Saldo Últimos 7 Días
var ctxSaldo = document.getElementById("canvasSaldo").getContext("2d");
new Chart(ctxSaldo, {
  type: "line",
  data: {
    labels: fechasSaldo,
    datasets: [{
      label: "Saldo",
      backgroundColor: "#fff",
      borderColor: "#fff",
      data: totalesSaldo,
      fill: false,
      pointBorderWidth: 100,
      pointBorderColor: "transparent",
      pointRadius: 3,
      pointBackgroundColor: "transparent",
      pointHoverBackgroundColor: "rgba(63,82,227,1)",
    }],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: -10,
        top: 10,
      },
    },
    legend: { display: false },
    title: { display: false },
    tooltips: { mode: "index", intersect: false },
    hover: { mode: "nearest", intersect: true },
    scales: {
      xAxes: [{ gridLines: { drawBorder: false, display: false }, ticks: { display: false } }],
      yAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { display: false } }],
    },
  },
});

// Gráfico de Productos Vendidos Últimos 7 Días
var ctxProductos = document.getElementById("canvasProductos").getContext("2d");
new Chart(ctxProductos, {
  type: "line",
  data: {
    labels: fechasProductos,
    datasets: [{
      label: "Productos Vendidos",
      backgroundColor: "#fff",
      borderColor: "#fff",
      data: totalesProductos,
      fill: false,
      pointBorderWidth: 100,
      pointBorderColor: "transparent",
      pointRadius: 3,
      pointBackgroundColor: "transparent",
      pointHoverBackgroundColor: "rgba(63,82,227,1)",
    }],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: -10,
        top: 10,
      },
    },
    legend: { display: false },
    title: { display: false },
    tooltips: { mode: "index", intersect: false },
    hover: { mode: "nearest", intersect: true },
    scales: {
      xAxes: [{ gridLines: { drawBorder: false, display: false }, ticks: { display: false } }],
      yAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { display: false } }],
    },
  },
});

// Gráfico de Ventas por Meses Últimos 7 Meses
var ctxVentas = document.getElementById("canvasVentas").getContext("2d");
new Chart(ctxVentas, {
  type: "line",
  data: {
    labels: mesesVentas,
    datasets: [{
      label: "Ventas",
      backgroundColor: "#fff",
      borderColor: "#fff",
      data: totalesVentas,
      fill: false,
      pointBorderWidth: 100,
      pointBorderColor: "transparent",
      pointRadius: 3,
      pointBackgroundColor: "transparent",
      pointHoverBackgroundColor: "rgba(63,82,227,1)",
    }],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: -10,
        top: 10,
      },
    },
    legend: { display: false },
    title: { display: false },
    tooltips: { mode: "index", intersect: false },
    hover: { mode: "nearest", intersect: true },
    scales: {
      xAxes: [{ gridLines: { drawBorder: false, display: false }, ticks: { display: false } }],
      yAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { display: false } }],
    },
  },
});
// Gráfico de Saldo por Meses Últimos 7 Meses
var ctxSaldoMensual = document.getElementById("canvasSaldoMensual").getContext("2d");
new Chart(ctxSaldoMensual, {
    type: "line",
    data: {
        labels: mesesSaldo,
        datasets: [{
            label: "Saldo Mensual",
            backgroundColor: "#fff",
            borderColor: "#fff",
            data: totalesSaldoMensual,
            fill: false,
            pointBorderWidth: 100,
            pointBorderColor: "transparent",
            pointRadius: 3,
            pointBackgroundColor: "transparent",
            pointHoverBackgroundColor: "rgba(63,82,227,1)",
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: -10,
                top: 10,
            },
        },
        legend: { display: false },
        title: { display: false },
        tooltips: { mode: "index", intersect: false },
        hover: { mode: "nearest", intersect: true },
        scales: {
            xAxes: [{
                gridLines: { drawBorder: false, display: false },
                ticks: { display: false },
            }],
            yAxes: [{
                gridLines: { display: false, drawBorder: false },
                ticks: { display: false },
            }],
        },
    },
});


var randomScalingFactor = function () {
  return (Math.random() > 0.5 ? 1.0 : 1.0) * Math.round(Math.random() * 100);
};

// draws a rectangle with a rounded top
Chart.helpers.drawRoundedTopRectangle = function (ctx, x, y, width, height, radius) {
  ctx.beginPath();
  ctx.moveTo(x + radius, y);
  // top right corner
  ctx.lineTo(x + width - radius, y);
  ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
  // bottom right	corner
  ctx.lineTo(x + width, y + height);
  // bottom left corner
  ctx.lineTo(x, y + height);
  // top left	
  ctx.lineTo(x, y + radius);
  ctx.quadraticCurveTo(x, y, x + radius, y);
  ctx.closePath();
};

Chart.elements.RoundedTopRectangle = Chart.elements.Rectangle.extend({
  draw: function () {
    var ctx = this._chart.ctx;
    var vm = this._view;
    var left, right, top, bottom, signX, signY, borderSkipped;
    var borderWidth = vm.borderWidth;

    if (!vm.horizontal) {
      // bar
      left = vm.x - vm.width / 2;
      right = vm.x + vm.width / 2;
      top = vm.y;
      bottom = vm.base;
      signX = 1;
      signY = bottom > top ? 1 : -1;
      borderSkipped = vm.borderSkipped || 'bottom';
    } else {
      // horizontal bar
      left = vm.base;
      right = vm.x;
      top = vm.y - vm.height / 2;
      bottom = vm.y + vm.height / 2;
      signX = right > left ? 1 : -1;
      signY = 1;
      borderSkipped = vm.borderSkipped || 'left';
    }

    // Canvas doesn't allow us to stroke inside the width so we can
    // adjust the sizes to fit if we're setting a stroke on the line
    if (borderWidth) {
      // borderWidth shold be less than bar width and bar height.
      var barSize = Math.min(Math.abs(left - right), Math.abs(top - bottom));
      borderWidth = borderWidth > barSize ? barSize : borderWidth;
      var halfStroke = borderWidth / 2;
      // Adjust borderWidth when bar top position is near vm.base(zero).
      var borderLeft = left + (borderSkipped !== 'left' ? halfStroke * signX : 0);
      var borderRight = right + (borderSkipped !== 'right' ? -halfStroke * signX : 0);
      var borderTop = top + (borderSkipped !== 'top' ? halfStroke * signY : 0);
      var borderBottom = bottom + (borderSkipped !== 'bottom' ? -halfStroke * signY : 0);
      // not become a vertical line?
      if (borderLeft !== borderRight) {
        top = borderTop;
        bottom = borderBottom;
      }
      // not become a horizontal line?
      if (borderTop !== borderBottom) {
        left = borderLeft;
        right = borderRight;
      }
    }

    // calculate the bar width and roundess
    var barWidth = Math.abs(left - right);
    var roundness = this._chart.config.options.barRoundness || 0.5;
    var radius = barWidth * roundness * 0.5;

    // keep track of the original top of the bar
    var prevTop = top;

    // move the top down so there is room to draw the rounded top
    top = prevTop + radius;
    var barRadius = top - prevTop;

    ctx.beginPath();
    ctx.fillStyle = vm.backgroundColor;
    ctx.strokeStyle = vm.borderColor;
    ctx.lineWidth = borderWidth;

    // draw the rounded top rectangle
    Chart.helpers.drawRoundedTopRectangle(ctx, left, (top - barRadius + 1), barWidth, bottom - prevTop, barRadius);

    ctx.fill();
    if (borderWidth) {
      ctx.stroke();
    }

    // restore the original top value so tooltips and scales still work
    top = prevTop;
  },
});

Chart.defaults.roundedBar = Chart.helpers.clone(Chart.defaults.bar);

Chart.controllers.roundedBar = Chart.controllers.bar.extend({
  dataElementType: Chart.elements.RoundedTopRectangle
});

// Usando Moment.js para asegurarte que las fechas se conviertan correctamente
var ctxBar = document.getElementById("bar").getContext("2d");
var myBar = new Chart(ctxBar, {
  type: 'bar',
  data: {
    labels: meses.map(function (mes) {
      // Convertimos correctamente los meses con Moment.js
      return moment(mes + "-01").format('MMM');
    }),
    datasets: [{
      label: 'Producto más vendido por mes',
      backgroundColor: [
        'rgba(75, 192, 192, 0.6)', 
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(153, 102, 255, 0.6)', 
        'rgba(255, 159, 64, 0.6)',  
        'rgba(201, 203, 207, 0.6)', 
        'rgba(75, 192, 192, 0.6)'
      ],
      data: productosPorMes.map(function (producto) {
        return producto.cantidad; 
      })
    }]
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: "Ult. 7 meses"
    },
    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
          var index = tooltipItem.index;
          var producto = productosPorMes[index];
          return producto.nombre + ': ' + producto.cantidad + ' vendidos';
        }
      }
    },
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
          padding: 10,
        },
        gridLines: {
          drawBorder: false,
        }
      }],
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        }
      }]
    }
  }
});

var radialBarsOptions = {
  series: [44, 80, 67],
  chart: {
    height: 350,
    type: "radialBar",
  },
  theme: {
    mode: "light",
    palette: "palette1",
    monochrome: {
      enabled: true,
      color: "#3245D1",
      shadeTo: "light",
      shadeIntensity: 0.65,
    },
  },
  plotOptions: {
    radialBar: {
      dataLabels: {
        name: {
          offsetY: -15,
          fontSize: "22px",
        },
        value: {
          fontSize: "2.5rem",
        },
        total: {
          show: true,
          label: "Earnings",
          color: "#25A6F1",
          fontSize: "16px",
          formatter: function (w) {
            // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
            return "$4,124";
          },
        },
      },
    },
  },
  labels: ["Apples", "Oranges", "Bananas", "Berries"],
};
var radialBars = new ApexCharts(document.querySelector("#radialBars"), radialBarsOptions);
radialBars.render();

